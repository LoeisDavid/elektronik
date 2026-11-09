@props(['paginator' => null, 'data' => null, 'label' => 'data'])

@php
    $p = $paginator ?? $data;
@endphp

@if ($p && $p->total() >= 0)
    @php
        $halaman   = $p->currentPage();
        $totalHal  = max(1, $p->lastPage());
        $btnBase   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs transition-all duration-200';
        $btnNormal = $btnBase . ' border-[#A8B1C1] text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200';
        $btnAktif  = $btnBase . ' font-semibold bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb';
        $btnOff    = $btnBase . ' border-[#A8B1C1] text-gray-300 pointer-events-none';
        $nomor     = fn ($i) => $i === $halaman
            ? '<span class="' . $btnAktif . '">' . $i . '</span>'
            : '<a href="' . e($p->url($i)) . '" class="' . $btnNormal . '">' . $i . '</a>';
        $titik     = '<span class="px-1">...</span>';
    @endphp
    <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854] rounded-b-lg">
        {{-- INFO JUMLAH DATA --}}
        <div class="whitespace-nowrap">
            Menampilkan {{ $p->firstItem() ?? 0 }}-{{ $p->lastItem() ?? 0 }} dari {{ $p->total() ?? 0 }} {{ $label }}
        </div>

        {{-- TOMBOL PAGINATION --}}
        <div class="flex items-center gap-1">
            @if ($halaman > 1)
                <a href="{{ $p->previousPageUrl() }}" class="{{ $btnNormal }}">‹</a>
            @else
                <span class="{{ $btnOff }}">‹</span>
            @endif

            {!! $nomor(1) !!}

            @if ($totalHal > 3)
                @if ($halaman <= 2)
                    {!! $nomor(2) !!}
                    {!! $nomor(3) !!}
                @elseif ($halaman >= $totalHal - 1)
                    {!! $titik !!}
                    {!! $nomor($totalHal - 2) !!}
                    {!! $nomor($totalHal - 1) !!}
                @else
                    {!! $titik !!}
                    {!! $nomor($halaman - 1) !!}
                    {!! $nomor($halaman) !!}
                    {!! $nomor($halaman + 1) !!}
                @endif

                @if ($halaman <= $totalHal - 2)
                    {!! $titik !!}
                @endif

                {!! $nomor($totalHal) !!}
            @elseif ($totalHal == 3)
                {!! $nomor(2) !!}
                {!! $nomor(3) !!}
            @elseif ($totalHal == 2)
                {!! $nomor(2) !!}
            @endif

            @if ($halaman < $totalHal)
                <a href="{{ $p->nextPageUrl() }}" class="{{ $btnNormal }}">›</a>
            @else
                <span class="{{ $btnOff }}">›</span>
            @endif
        </div>
    </div>
@endif
