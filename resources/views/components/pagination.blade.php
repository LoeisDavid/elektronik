@props(['paginator' => null, 'data' => null, 'label' => 'data'])

@php
    $p = $paginator ?? $data;
@endphp

@if ($p && $p->total() > 0)
    <div class="px-4 py-3 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-[#142854] rounded-b-lg">
        {{-- INFO JUMLAH DATA --}}
        <div class="whitespace-nowrap font-medium text-gray-600">
            Menampilkan <span class="font-bold text-[#142854]">{{ $p->firstItem() ?? 0 }}</span> - <span class="font-bold text-[#142854]">{{ $p->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#142854]">{{ $p->total() ?? 0 }}</span> {{ $label }}
        </div>

        {{-- TOMBOL PAGINATION --}}
        @if ($p->lastPage() > 1)
            <div class="flex items-center gap-1 select-none">
                {{-- PREVIOUS --}}
                @if ($p->onFirstPage())
                    <span class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#A8B1C1] opacity-50 cursor-not-allowed">
                        ‹
                    </span>
                @else
                    <a href="{{ $p->previousPageUrl() }}"
                       class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200 font-bold"
                       title="Halaman Sebelumnya">
                        ‹
                    </a>
                @endif

                @php
                    $current = $p->currentPage();
                    $last = $p->lastPage();
                @endphp

                {{-- HALAMAN 1 --}}
                <a href="{{ $p->url(1) }}"
                   class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold transition-all duration-200 {{ $current == 1 ? 'bg-[#142854] text-white border-[#142854]' : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854]' }}">
                    1
                </a>

                @if ($last <= 5)
                    {{-- JIKA TOTAL HALAMAN <= 5, TAMPILKAN SEMUA --}}
                    @for ($page = 2; $page <= $last; $page++)
                        <a href="{{ $p->url($page) }}"
                           class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold transition-all duration-200 {{ $current == $page ? 'bg-[#142854] text-white border-[#142854]' : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854]' }}">
                            {{ $page }}
                        </a>
                    @endfor
                @else
                    {{-- SLIDING WINDOW UNTUK HALAMAN BANYAK --}}
                    @if ($current <= 3)
                        @for ($page = 2; $page <= 4; $page++)
                            <a href="{{ $p->url($page) }}"
                               class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold transition-all duration-200 {{ $current == $page ? 'bg-[#142854] text-white border-[#142854]' : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854]' }}">
                                {{ $page }}
                            </a>
                        @endfor
                        <span class="px-1 text-gray-400 font-semibold">...</span>
                        <a href="{{ $p->url($last) }}"
                           class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold transition-all duration-200 {{ $current == $last ? 'bg-[#142854] text-white border-[#142854]' : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854]' }}">
                            {{ $last }}
                        </a>
                    @elseif ($current >= $last - 2)
                        <span class="px-1 text-gray-400 font-semibold">...</span>
                        @for ($page = $last - 3; $page <= $last; $page++)
                            @if ($page > 1)
                                <a href="{{ $p->url($page) }}"
                                   class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold transition-all duration-200 {{ $current == $page ? 'bg-[#142854] text-white border-[#142854]' : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854]' }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor
                    @else
                        <span class="px-1 text-gray-400 font-semibold">...</span>
                        <a href="{{ $p->url($current - 1) }}"
                           class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] transition-all duration-200">
                            {{ $current - 1 }}
                        </a>
                        <a href="{{ $p->url($current) }}"
                           class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-[#142854] text-white border-[#142854]">
                            {{ $current }}
                        </a>
                        <a href="{{ $p->url($current + 1) }}"
                           class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854] transition-all duration-200">
                            {{ $current + 1 }}
                        </a>
                        <span class="px-1 text-gray-400 font-semibold">...</span>
                        <a href="{{ $p->url($last) }}"
                           class="h-7 w-7 border rounded-md flex items-center justify-center text-xs font-semibold transition-all duration-200 {{ $current == $last ? 'bg-[#142854] text-white border-[#142854]' : 'bg-white text-[#142854] border-[#A8B1C1] hover:bg-gray-100 hover:border-[#142854]' }}">
                            {{ $last }}
                        </a>
                    @endif
                @endif

                {{-- NEXT --}}
                @if ($p->hasMorePages())
                    <a href="{{ $p->nextPageUrl() }}"
                       class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200 transition-all duration-200 font-bold"
                       title="Halaman Berikutnya">
                        ›
                    </a>
                @else
                    <span class="h-7 w-7 border border-[#A8B1C1] rounded-md flex items-center justify-center text-[#A8B1C1] opacity-50 cursor-not-allowed">
                        ›
                    </span>
                @endif
            </div>
        @endif
    </div>
@endif
