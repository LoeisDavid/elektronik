{{-- ========================================================= --}}
{{-- MODAL TAMBAH SALES                                        --}}
{{-- ========================================================= --}}
<div
    id="tambahSalesModal"
    class="fixed inset-0 z-50 {{ $errors->any() && !$errors->editSales->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeTambahModal()"></div>

    <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black">
        <h2 class="text-2xl font-bold text-black tracking-tight mb-5">Tambah Sales</h2>

        <form id="formTambah" method="POST" action="{{ route('POS.sales.store') }}" class="space-y-4">
            @csrf

            {{-- DES --}}
            <div>
                <label class="block text-sm font-semibold text-black mb-1">Nama Sales</label>
                <input
                    type="text"
                    name="DES"
                    required
                    placeholder="Masukkan nama sales"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ old('DES') }}"
                >
                @error('DES')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- HP --}}
            <div>
                <label class="block text-sm font-semibold text-black mb-1">Nomor HP</label>
                <input
                    type="text"
                    name="HP"
                    required
                    placeholder="Masukkan nomor HP"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ old('HP') }}"
                >
                @error('HP')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- AL --}}
            <div>
                <label class="block text-sm font-semibold text-black mb-1">Alamat</label>
                <input
                    type="text"
                    name="AL"
                    required
                    placeholder="Masukkan alamat sales"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ old('AL') }}"
                >
                @error('AL')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeTambahModal()"
                    class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold text-black hover:bg-gray-300 hover:border-gray-900 active:bg-gray-200 transition-all duration-200 cursor-pointer"
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
{{-- MODAL EDIT SALES                                          --}}
{{-- ========================================================= --}}
<div
    id="editSalesModal"
    class="fixed inset-0 z-50 {{ $errors->editSales->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeEditModal()"></div>

    <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black">
        <h2 class="text-2xl font-bold text-black tracking-tight mb-5">Edit Sales</h2>

        <form
            id="editSalesForm"
            method="POST"
            class="space-y-4"
            action="{{ $errors->editSales->any() && old('SLS') ? route('POS.sales.update', ['id' => old('SLS')]) : '' }}"
        >
            @csrf
            @method('PUT')

            {{-- SLS --}}
            <div>
                <label class="block text-sm font-semibold text-black mb-1">Kode Sales</label>
                <input
                    id="editSLS"
                    type="text"
                    name="SLS"
                    readonly
                    class="w-full bg-gray-100 border border-black/80 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed focus:outline-none"
                    value="{{ $errors->editSales->any() ? old('SLS') : '' }}"
                >
            </div>

            {{-- DES --}}
            <div>
                <label class="block text-sm font-semibold text-black mb-1">Nama Sales</label>
                <input
                    id="editDES"
                    type="text"
                    name="DES"
                    required
                    placeholder="Masukkan nama sales"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ $errors->editSales->any() ? old('DES') : '' }}"
                >
                @error('DES', 'editSales')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- HP --}}
            <div>
                <label class="block text-sm font-semibold text-black mb-1">Nomor HP</label>
                <input
                    id="editHP"
                    type="text"
                    name="HP"
                    required
                    placeholder="Masukkan nomor HP"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ $errors->editSales->any() ? old('HP') : '' }}"
                >
                @error('HP', 'editSales')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- AL --}}
            <div>
                <label class="block text-sm font-semibold text-black mb-1">Alamat</label>
                <input
                    id="editAL"
                    type="text"
                    name="AL"
                    required
                    placeholder="Masukkan alamat sales"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm text-black transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ $errors->editSales->any() ? old('AL') : '' }}"
                >
                @error('AL', 'editSales')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold text-black hover:bg-gray-300 hover:border-gray-900 active:bg-gray-200 transition-all duration-200 cursor-pointer"
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
