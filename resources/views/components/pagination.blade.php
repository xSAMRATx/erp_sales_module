<div class="flex justify-center items-center pt-4 pb-4 gap-2">
    <a href="{{ $paginator->previousPageUrl() }}"
        class="mr-2 {{ $paginator->onFirstPage() ? 'text-gray-400 cursor-not-allowed' : '' }}"
        aria-disabled="{{ $paginator->onFirstPage() }}" tabindex="{{ $paginator->onFirstPage() ? '-1' : '' }}"
        title="Previous">
        <i class="fa-solid fa-angles-left"></i>
    </a>

    @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
        <a href="{{ $url }}"
            class="px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg
           {{ $page == $paginator->currentPage() ? 'bg-blue-500 text-white' : 'text-black hover:bg-blue-500 hover:text-white' }}">
            {{ $page }}
        </a>
    @endforeach

    <a href="{{ $paginator->nextPageUrl() }}"
        class="ml-2 {{ !$paginator->hasMorePages() ? 'text-gray-400 cursor-not-allowed' : '' }}"
        aria-disabled="{{ !$paginator->hasMorePages() }}" tabindex="{{ !$paginator->hasMorePages() ? '-1' : '' }}"
        title="Next">
        <i class="fa-solid fa-angles-right"></i>
    </a>
</div>
