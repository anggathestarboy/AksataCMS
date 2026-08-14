<?php

namespace App\Livewire\Admin\SectionTypes;

use App\Models\SectionType;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Edit extends SectionTypeForm
{
    public SectionType $sectionType;

    public function mount(SectionType $sectionType): void
    {
        $this->sectionType = $sectionType;
        $this->name = $sectionType->name;
        $this->slug = $sectionType->slug;
        $this->icon = $sectionType->icon;
        $this->fields = $sectionType->fields ?? [];
    }

    public function save()
    {
        $validated = $this->validate(array_merge($this->rules(), [
            'slug' => [
                'required',
                'string',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('section_types', 'slug')->ignore($this->sectionType->getKey()),
            ],
        ]), $this->validationMessages());

        $this->sectionType->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'icon' => $validated['icon'],
            'fields' => $this->normalizeFields(),
        ]);

        session()->flash('status', 'Section type updated successfully.');

        return redirect()->route('admin.section-types.edit', $this->sectionType);
    }

    public function delete()
    {
        $this->sectionType->delete();

        session()->flash('status', 'Section type deleted successfully.');

        return redirect()->route('admin.section-types.index');
    }

    public function render()
    {
        return view('livewire.admin.section-types.edit');
    }
}
