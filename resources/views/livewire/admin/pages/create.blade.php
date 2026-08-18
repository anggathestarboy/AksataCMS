<div class="py-6" x-data @keydown.window="if ((event.ctrlKey || event.metaKey) && event.key === 's') { event.preventDefault(); $wire.save(); }">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-gray-900">Create Page</h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pages.index') }}"
                    class="text-xs font-medium text-gray-500 hover:text-gray-700 transition">
                    ← Back to list
                </a>
                <button type="button" wire:click="save" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                    <svg wire:loading.remove class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg wire:loading class="w-3.5 h-3.5 mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Save
                    <span class="ml-1.5 text-[10px] text-indigo-300 font-normal normal-case tracking-normal">Ctrl+S</span>
                </button>
            </div>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start">

            {{-- LEFT COLUMN: Info --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Page Details</h3>
                </div>
                <div class="px-4 py-4">
                    <p class="text-xs text-gray-500">
                        Fill in the page details on the right sidebar, then click <strong>Save</strong>.
                        Once saved, you'll be able to add sections to build your page content.
                    </p>
                </div>
            </div>

            {{-- RIGHT COLUMN: Sidebar --}}
            <div class="lg:sticky lg:top-6">
                @include('livewire.admin.pages.partials.page-form')
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
        x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        x-cloak
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium text-white"
        :class="type === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
        <svg x-show="type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <svg x-show="type !== 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span x-text="message"></span>
    </div>
</div>
