<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Pages</h1>
            <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                New Page
            </a>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-4 border-b border-gray-200">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search pages..."
                    class="block w-full sm:w-72 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published At</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sections</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pages as $page)
                        @php $translation = $page->translations->firstWhere('locale', config('cms.default_locale')); @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $translation?->title ?? $page->translations->first()?->title ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($page->status === 'published')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $page->published_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $page->sections_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" wire:click="movePage({{ $page->id }}, 'up')" title="Move up" class="text-gray-400 hover:text-gray-700">↑</button>
                                <button type="button" wire:click="movePage({{ $page->id }}, 'down')" title="Move down" class="ms-1 text-gray-400 hover:text-gray-700">↓</button>
                                @if ($page->status === 'published' && $page->url() !== null)
                                    <a href="{{ $page->url() }}" target="_blank" rel="noopener" class="ms-3 text-emerald-600 hover:text-emerald-900">View</a>
                                @endif
                                <a href="{{ route('admin.pages.edit', $page) }}" class="ms-3 text-indigo-600 hover:text-indigo-900">Edit</a>
                                <button type="button" x-data @click="if (confirm('Delete this page?')) $wire.delete({{ $page->id }})"
                                    class="ms-3 text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pages->links() }}
            </div>
        </div>
    </div>
</div>
