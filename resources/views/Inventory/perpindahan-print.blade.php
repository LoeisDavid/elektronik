@php
    $targetTahap = match($rp->SS) {
        'Request'    => 'request',
        'Pengiriman' => 'surat-jalan',
        default      => 'penerimaan',
    };
@endphp

@if($targetTahap === 'request')
    @include('Inventory.perpindahan-print-request')
@elseif($targetTahap === 'surat-jalan')
    @include('Inventory.perpindahan-print-surat-jalan')
@else
    @include('Inventory.perpindahan-print-penerimaan')
@endif
