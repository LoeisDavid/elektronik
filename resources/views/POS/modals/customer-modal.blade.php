{{-- ========================================================= --}}
{{-- MODAL TAMBAH CUSTOMER / DESA                              --}}
{{-- ========================================================= --}}
@php
    $desaList = $desaList ?? [];
@endphp

<div
    id="tambahCustomerModal"
    class="fixed inset-0 z-50 {{ $errors->any() && !$errors->editCustomer->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeTambahModal()"></div>

    <div class="relative w-full max-w-md bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
        <h2 id="judulTambahModal" class="text-2xl font-bold mb-4">Tambah Data</h2>

        {{-- Switch Tab: Customer vs Desa --}}
        <div class="flex items-center p-1 bg-gray-200/80 rounded-xl mb-5 border border-gray-300 text-sm font-semibold">
            <button
                type="button"
                id="btnTabTambahCustomer"
                onclick="pilihTipeTambah('customer')"
                class="flex-1 py-1.5 rounded-lg text-center transition-all duration-150 bg-primary-db text-white shadow-xs cursor-pointer"
            >
                Customer
            </button>
            <button
                type="button"
                id="btnTabTambahDesa"
                onclick="pilihTipeTambah('desa')"
                class="flex-1 py-1.5 rounded-lg text-center transition-all duration-150 text-gray-700 hover:text-black hover:bg-white/50 cursor-pointer"
            >
                Desa
            </button>
        </div>

        <form id="formTambah" method="POST" action="{{ route('POS.customer.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="tipe" id="inputTipeTambah" value="{{ old('tipe', 'customer') }}">

            {{-- DES --}}
            <div>
                <label id="labelTambahDES" class="block text-sm font-semibold mb-1">Nama Customer</label>
                <input
                    type="text"
                    id="inputTambahDES"
                    name="DES"
                    required
                    placeholder="Masukkan nama customer"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ old('DES') }}"
                >
                @error('DES')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fields khusus Customer --}}
            <div id="fieldsCustomerTambah" class="space-y-4">
                {{-- TLP --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Nomor Telepon</label>
                    <input
                        type="text"
                        id="inputTambahTLP"
                        name="TLP"
                        required
                        placeholder="Masukkan nomor telepon"
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ old('TLP') }}"
                    >
                    @error('TLP')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DS (Desa) --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Desa</label>
                    <select
                        id="selectDesaTambah"
                        name="DS"
                        required
                        class="searchable-select w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm"
                    >
                        <option value="" disabled {{ old('DS') ? '' : 'selected' }}>Pilih Desa</option>
                        @foreach ($desaList as $d)
                            <option value="{{ $d->CST }}" {{ old('DS') == $d->CST ? 'selected' : '' }}>
                                {{ $d->DES }}
                            </option>
                        @endforeach
                    </select>
                    @error('DS')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- AL (Alamat) --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Alamat</label>
                    <textarea
                        id="inputTambahAL"
                        name="AL"
                        rows="2"
                        required
                        placeholder="Masukkan alamat customer"
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db resize-none"
                    >{{ old('AL') }}</textarea>
                    @error('AL')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeTambahModal()"
                    class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 active:bg-gray-200 transition-all duration-200 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    id="btnTambah"
                    type="submit"
                    class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL EDIT CUSTOMER / DESA                                 --}}
{{-- ========================================================= --}}
<div
    id="editCustomerModal"
    class="fixed inset-0 z-50 {{ $errors->editCustomer->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeEditModal()"></div>

    <div class="relative w-full max-w-md bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
        <h2 id="judulEditModal" class="text-2xl font-bold mb-5">Edit Customer</h2>

        <form
            id="editCustomerForm"
            method="POST"
            class="space-y-4"
            action="{{ $errors->editCustomer->any() && old('CST') ? route('POS.customer.update', ['id' => old('CST')]) : '' }}"
        >
            @csrf
            @method('PUT')
            <input type="hidden" name="tipe" id="editTipe" value="{{ old('tipe', 'customer') }}">

            {{-- CST (Kode) --}}
            <div>
                <label id="labelEditCST" class="block text-sm font-semibold mb-1 text-black">Kode Customer</label>
                <input
                    id="editCST"
                    type="text"
                    name="CST"
                    readonly
                    required
                    class="w-full bg-gray-100 border border-black/80 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed focus:outline-none"
                    value="{{ $errors->editCustomer->any() ? old('CST') : '' }}"
                >
            </div>

            {{-- DES (Nama) --}}
            <div>
                <label id="labelEditDES" class="block text-sm font-semibold mb-1 text-black">Nama Customer</label>
                <input
                    id="editDES"
                    type="text"
                    name="DES"
                    required
                    placeholder="Masukkan nama"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ $errors->editCustomer->any() ? old('DES') : '' }}"
                >
                @error('DES', 'editCustomer')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fields khusus Customer (Disembunyikan jika Desa) --}}
            <div id="fieldsCustomerEdit" class="space-y-4">
                {{-- TLP (Nomor Telepon) --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-black">Nomor Telepon</label>
                    <input
                        id="editTLP"
                        type="text"
                        name="TLP"
                        required
                        placeholder="Masukkan nomor telepon"
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        value="{{ $errors->editCustomer->any() ? old('TLP') : '' }}"
                    >
                    @error('TLP', 'editCustomer')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DS (Desa) --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-black">Desa</label>
                    <select
                        id="editDS"
                        name="DS"
                        required
                        class="searchable-select w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm"
                    >
                        <option value="" disabled {{ $errors->editCustomer->any() && old('DS') ? '' : 'selected' }}>Pilih Desa</option>
                        @foreach ($desaList as $d)
                            <option value="{{ $d->CST }}" {{ $errors->editCustomer->any() && old('DS') == $d->CST ? 'selected' : '' }}>
                                {{ $d->DES }}
                            </option>
                        @endforeach
                    </select>
                    @error('DS', 'editCustomer')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- AL (Alamat) --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-black">Alamat</label>
                    <textarea
                        id="editAL"
                        name="AL"
                        rows="2"
                        required
                        placeholder="Masukkan alamat customer"
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db resize-none"
                    >{{ $errors->editCustomer->any() ? old('AL') : '' }}</textarea>
                    @error('AL', 'editCustomer')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-300 hover:border-gray-900 active:bg-gray-200 transition-all duration-200 cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
