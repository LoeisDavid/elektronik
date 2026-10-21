{{-- ========================================================= --}}
{{-- MODAL TAMBAH DESA                                         --}}
{{-- ========================================================= --}}
<div
    id="tambahDesaModal"
    class="fixed inset-0 z-50 {{ $errors->any() && !$errors->editDesa->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeTambahModal()"></div>

    <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black">
        <h2 class="text-2xl font-bold mb-5">Tambah Desa</h2>

        <form id="formTambah" method="POST" action="{{ route('POS.desa.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1">Nama Desa</label>
                <input
                    type="text"
                    name="DES"
                    required
                    placeholder="Masukkan nama desa"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ old('DES') }}"
                >
                @error('DES')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
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
{{-- MODAL EDIT DESA                                           --}}
{{-- ========================================================= --}}
<div
    id="editDesaModal"
    class="fixed inset-0 z-50 {{ $errors->editDesa->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeEditModal()"></div>

    <div class="relative w-full max-w-sm bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black">
        <h2 class="text-2xl font-bold mb-1">Edit Desa</h2>
        <p class="text-xs text-gray-500 mb-5">
            Kode Desa: <span id="displayKodeDesa" class="font-semibold text-gray-700">{{ old('DS') }}</span>
        </p>

        <form
            id="editDesaForm"
            method="POST"
            class="space-y-4"
            action="{{ $errors->editDesa->any() && old('DS') ? route('POS.desa.update', ['id' => old('DS')]) : '' }}"
        >
            @csrf
            @method('PUT')

            <input type="hidden" id="editDS" name="DS" value="{{ old('DS') }}">

            <div>
                <label class="block text-sm font-semibold mb-1 text-black">Nama Desa</label>
                <input
                    id="editDES"
                    type="text"
                    name="DES"
                    required
                    placeholder="Masukkan nama desa"
                    class="w-full bg-white border border-black/80 rounded-xl px-3 py-2.5 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    value="{{ $errors->editDesa->any() ? old('DES') : '' }}"
                >
                @error('DES', 'editDesa')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

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
