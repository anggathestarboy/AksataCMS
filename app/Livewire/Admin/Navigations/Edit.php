<?php

namespace App\Livewire\Admin\Navigations;

use App\Models\Navigation;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Form
{
    public Navigation $navigation;

    public function mount(Navigation $navigation): void
    {
        $this->navigation = $navigation;
        $this->name = $navigation->name;
        $this->slug = $navigation->slug;
    }

    public function save()
    {
        $validated = $this->validate(array_merge($this->rules(), [
            'slug' => [
                'required',
                'string',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('navigations', 'slug')->ignore($this->navigation->getKey()),
            ],
        ]), $this->validationMessages());

        $this->navigation->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        session()->flash('status', 'Navigation updated successfully.');
    }

    public function delete()
    {
        $this->navigation->delete();

        session()->flash('status', 'Navigation deleted successfully.');

        return redirect()->route('admin.navigations.index');
    }

    public function render()
    {
        return view('livewire.admin.navigations.edit');
    }
}
