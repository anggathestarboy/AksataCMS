<?php

namespace App\Livewire\Admin\SectionTypes;

use App\Models\SectionType;
use App\Services\SectionTemplateGenerator;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends SectionTypeForm
{
    public function save()
    {
        $validated = $this->validate(array_merge($this->rules(), [
            'slug' => ['required', 'string', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:section_types,slug'],
        ]), $this->validationMessages());

        $sectionType = SectionType::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'icon' => $validated['icon'],
            'fields' => $this->normalizeFields(),
        ]);

        app(SectionTemplateGenerator::class)->generate($sectionType);

        $this->dispatch('show-toast', message: 'Section type created successfully.');

        return redirect()->route('admin.section-types.index');
    }

    public function render()
    {
        return view('livewire.admin.section-types.create');
    }
}
