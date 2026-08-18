<?php

namespace App\Livewire\Admin\Navigations;

use App\Models\Navigation;
use App\Models\NavigationItem;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ItemBuilder extends Component
{
    public Navigation $navigation;

    public string $activeLocale = 'id';

    public bool $showForm = false;

    public ?int $editingItemId = null;

    public ?int $formParentId = null;

    public string $type = 'external';

    public string $url = '';

    public bool $openInNewTab = false;

    public ?int $pageId = null;

    public string $icon = '';

    /**
     * @var array<string, string>
     */
    public array $labels = [];

    public function mount(Navigation $navigation): void
    {
        $this->navigation = $navigation;
        $this->labels = $this->defaultLabels();
    }

    /**
     * @return array<string, string>
     */
    protected function defaultLabels(): array
    {
        return collect(config('cms.locales'))
            ->mapWithKeys(fn (string $label, string $locale): array => [$locale => ''])
            ->all();
    }

    public function openCreate(?int $parentId = null): void
    {
        $this->editingItemId = null;
        $this->formParentId = $parentId;
        $this->type = 'external';
        $this->url = '';
        $this->openInNewTab = false;
        $this->pageId = null;
        $this->icon = '';
        $this->labels = $this->defaultLabels();
        $this->resetValidation();
        $this->showForm = true;
    }

    public function openEdit(int $itemId): void
    {
        $item = NavigationItem::with('translations')->findOrFail($itemId);

        $this->editingItemId = $itemId;
        $this->formParentId = $item->parent_id;
        $this->type = $item->type;
        $this->url = (string) $item->url;
        $this->openInNewTab = (bool) $item->open_in_new_tab;
        $this->pageId = $item->page_id;
        $this->icon = (string) $item->icon;
        $this->labels = $this->defaultLabels();

        foreach ($item->translations as $translation) {
            $this->labels[$translation->locale] = $translation->label;
        }

        $this->resetValidation();
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingItemId = null;
        $this->formParentId = null;
    }

    public function updatedType(): void
    {
        $this->url = '';
        $this->pageId = null;
        $this->resetValidation(['url', 'pageId']);
    }

    public function saveItem()
    {
        $validated = $this->validateItem();

        $isEdit = $this->editingItemId !== null;

        $payload = [
            'type' => $this->type,
            'url' => in_array($this->type, ['external', 'internal'], true) ? trim($this->url) : null,
            'page_id' => $this->type === 'page' ? $this->pageId : null,
            'icon' => trim($this->icon) ?: null,
            'open_in_new_tab' => $this->openInNewTab,
        ];

        if ($isEdit) {
            $item = NavigationItem::findOrFail($this->editingItemId);
            $item->update($payload);
        } else {
            $maxOrder = (int) NavigationItem::query()
                ->where('navigation_id', $this->navigation->getKey())
                ->where('parent_id', $this->formParentId)
                ->max('order');

            $item = $this->navigation->items()->create(array_merge($payload, [
                'parent_id' => $this->formParentId,
                'order' => $maxOrder + 1,
            ]));
        }

        foreach ($this->labels as $locale => $label) {
            if (blank($label)) {
                $item->translations()->where('locale', $locale)->delete();

                continue;
            }

            $item->translations()->updateOrCreate(
                ['locale' => $locale],
                ['label' => $label],
            );
        }

        $this->closeForm();

        $this->dispatch('show-toast', message: $isEdit ? 'Item updated successfully.' : 'Item created successfully.');
    }

    public function deleteItem(int $itemId): void
    {
        NavigationItem::query()
            ->where('navigation_id', $this->navigation->getKey())
            ->where('id', $itemId)
            ->delete();

        $this->dispatch('show-toast', message: 'Item deleted successfully.');
    }

    public function moveItem(int $itemId, string $direction): void
    {
        $item = NavigationItem::query()
            ->where('navigation_id', $this->navigation->getKey())
            ->findOrFail($itemId);

        $siblings = NavigationItem::query()
            ->where('navigation_id', $this->navigation->getKey())
            ->where('parent_id', $item->parent_id)
            ->orderBy('order')
            ->get();

        $index = $siblings->search(fn (NavigationItem $sibling): bool => $sibling->getKey() === $itemId);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $siblings->count()) {
            return;
        }

        $other = $siblings[$target];

        $itemOrder = $item->order;
        $otherOrder = $other->order;

        $item->update(['order' => $otherOrder]);
        $other->update(['order' => $itemOrder]);
    }

    public function updateOrder(int $itemId, int $position, ?int $parentId = null): void
    {
        $item = NavigationItem::query()
            ->where('navigation_id', $this->navigation->getKey())
            ->findOrFail($itemId);

        $position = max(0, (int) $position);

        $siblings = NavigationItem::query()
            ->where('navigation_id', $this->navigation->getKey())
            ->where('parent_id', $parentId)
            ->whereKeyNot($itemId)
            ->orderBy('order')
            ->get();

        DB::transaction(function () use ($item, $siblings, $parentId, $position): void {
            $item->update([
                'parent_id' => $parentId,
                'order' => $position + 1,
            ]);

            foreach ($siblings->values() as $index => $sibling) {
                $sibling->update(['order' => $index < $position ? $index + 1 : $index + 2]);
            }
        });
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = [
            'type' => ['required', 'string', Rule::in(['external', 'internal', 'page'])],
            'url' => ['nullable', 'required_if:type,external', 'required_if:type,internal', 'string', 'max:2048'],
            'pageId' => ['nullable', 'required_if:type,page', 'exists:pages,id'],
            'icon' => ['nullable', 'string', 'max:255'],
            'openInNewTab' => ['boolean'],
        ];

        foreach (config('cms.locales') as $locale => $label) {
            if ($locale === config('cms.default_locale')) {
                $rules["labels.{$locale}"] = ['required', 'string', 'max:255'];
            } else {
                $rules["labels.{$locale}"] = ['nullable', 'string', 'max:255'];
            }
        }

        return $rules;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function validateItem(): array
    {
        return $this->validate($this->rules());
    }

    public function render()
    {
        $this->navigation->load([
            'items.children.translations',
            'items.page.translations',
        ]);

        return view('livewire.admin.navigations.items', [
            'items' => $this->navigation->items,
            'pages' => Page::query()->with('translations')->orderBy('order')->get(),
        ]);
    }
}
