{{-- ========================================================= --}}
{{-- MODAL STRUK (SESUAI DESAIN KASIR / POMELO)                --}}
{{-- ========================================================= --}}
@include('POS.struk')

{{-- ========================================================= --}}
{{-- FORM PRINT STRUK TERSEMBUNYI                              --}}
{{-- ========================================================= --}}
<form
    id="formPrintStruk"
    method="POST"
    action="{{ route('POS.struk.print') }}"
    target="_blank"
    class="hidden"
>
    @csrf
    <input type="hidden" name="ref_no"          id="printRefNo">
    <input type="hidden" name="tanggal"         id="printTanggal">
    <input type="hidden" name="waktu"           id="printWaktu">
    <input type="hidden" name="kasir"           id="printKasir">
    <input type="hidden" name="metode"          id="printMetode">
    <input type="hidden" name="subtotal"        id="printSubtotal">
    <input type="hidden" name="discount_item"   id="printDiscountItem">
    <input type="hidden" name="discount_global" id="printDiscountGlobal">
    <input type="hidden" name="voucher"         id="printVoucher">
    <input type="hidden" name="ppn"             id="printPpn">
    <input type="hidden" name="ppn_nominal"     id="printPpnNominal">
    <input type="hidden" name="total"           id="printTotal">
    <input type="hidden" name="dibayar"         id="printDibayar">
    <input type="hidden" name="kembalian"       id="printKembalian">
    <input type="hidden" name="customer_nama"   id="printCustomerNama">
    <input type="hidden" name="customer_desa"   id="printCustomerDesa">
    <input type="hidden" name="customer_alamat" id="printCustomerAlamat">
    <input type="hidden" name="items"           id="printItems">
</form>
