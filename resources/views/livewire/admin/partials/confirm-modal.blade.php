{{-- Confirm Modal --}}
<div x-data="{
        open: false,
        title: '',
        message: '',
        confirmText: 'Delete',
        confirmMethod: '',
        confirmParams: [],
        loading: false,
        error: false,
        errorMessage: '',
        errorItems: [],
    }"
    x-on:confirm-modal.window="
        open = true;
        title = $event.detail.title || 'Are you sure?';
        message = $event.detail.message || 'This action cannot be undone.';
        confirmText = $event.detail.confirmText || 'Delete';
        confirmMethod = $event.detail.confirmMethod || '';
        confirmParams = $event.detail.confirmParams || [];
        loading = false;
        error = false;
        errorMessage = '';
        errorItems = [];
    "
    x-on:confirm-modal-close.window="open = false; loading = false;"
    x-on:confirm-modal-error.window="
        loading = false;
        error = true;
        errorMessage = $event.detail.message || 'Operation failed.';
        errorItems = $event.detail.items || [];
    "
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
    style="display: none;">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/50" x-on:click="if (!loading) open = false"></div>

    {{-- Modal --}}
    <div class="relative w-full max-w-md bg-white rounded-xl shadow-2xl"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">

        {{-- Icon + Content --}}
        <div class="px-6 pt-6 pb-4">
            <div class="flex items-start gap-4">
                <div class="shrink-0 flex items-center justify-center w-10 h-10 rounded-full"
                    :class="error ? 'bg-amber-100' : 'bg-red-100'">
                    <template x-if="!error">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </template>
                    <template x-if="error">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                    <template x-if="!error">
                        <p class="mt-2 text-sm text-gray-500" x-text="message"></p>
                    </template>
                    <template x-if="error">
                        <div class="mt-2">
                            <p class="text-sm text-amber-700 font-medium" x-text="errorMessage"></p>
                            <template x-if="errorItems.length > 0">
                                <ul class="mt-2 text-sm text-amber-600 list-disc list-inside space-y-0.5">
                                    <template x-for="item in errorItems" :key="item">
                                        <li x-text="item"></li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 pb-6 flex items-center justify-end gap-3">
            <button type="button"
                x-on:click="if (!loading) open = false"
                x-show="error"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                Close
            </button>
            <button type="button"
                x-on:click="if (!loading) open = false"
                x-show="!error"
                :disabled="loading"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-50">
                Cancel
            </button>
            <button type="button"
                x-show="!error"
                x-on:click="loading = true; $wire[confirmMethod](...confirmParams).then(() => { open = false; loading = false; }).catch(() => { loading = false; })"
                :disabled="loading"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition disabled:opacity-50">
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span x-text="loading ? 'Processing...' : confirmText"></span>
            </button>
        </div>
    </div>
</div>
