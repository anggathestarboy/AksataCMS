<?php

namespace App\Livewire\Admin\Navigations;

use App\Models\Navigation;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Form
{
    public function save()
    {
        $validated = $this->validate(array_merge($this->rules(), [
            'slug' => ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:navigations,slug'],
        ]), $this->validationMessages());

        $navigation = Navigation::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        session()->flash('status', 'Navigation created successfully.');

        return redirect()->route('admin.navigations.edit', $navigation);
    }

    public function render()
    {
        return view('livewire.admin.navigations.create');
    }
}
