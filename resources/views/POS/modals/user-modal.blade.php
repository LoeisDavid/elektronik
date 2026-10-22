{{-- ========================================================= --}}
{{-- MODAL TAMBAH USER                                         --}}
{{-- ========================================================= --}}
<div
    id="tambahUserModal"
    class="fixed inset-0 z-50 {{ $errors->any() && !$errors->editUser->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeTambahModal()"></div>

    <div class="relative w-full max-w-lg bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-5 tracking-tight">Tambah User</h2>

        <form method="POST" id="formTambah" action="{{ route('POS.user.store') }}" class="space-y-5">
            @csrf

            {{-- GROUP 1: Informasi Akun (Akses) --}}
            <div class="space-y-3 pt-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- USERNAME --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">Username</label>
                        <input
                            type="text"
                            name="USERNAME"
                            placeholder="Contoh: David Loes"
                            required
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            value="{{ old('USERNAME') }}"
                        >
                        @error('USERNAME')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">Password</label>
                        <input
                            type="password"
                            name="PASS"
                            placeholder="Masukkan password"
                            required
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                        @error('PASS')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- GROUP 2: Data Diri & Role --}}
            <div class="space-y-3 pt-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- NAMA LENGKAP (DES) --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Lengkap</label>
                        <input
                            type="text"
                            name="DES"
                            placeholder="Masukkan nama"
                            required
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            value="{{ old('DES') }}"
                        >
                        @error('DES')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NOMOR HANDPHONE (HP) --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nomor Handphone</label>
                        <input
                            type="text"
                            name="HP"
                            placeholder="08123456789"
                            required
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            value="{{ old('HP') }}"
                        >
                        @error('HP')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ROLE --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Role</label>
                    <select
                        name="ROLE"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    >
                        <option value="1" {{ old('ROLE', 1) == 1 ? 'selected' : '' }}>Admin</option>
                        <option value="2" {{ old('ROLE') == 2 ? 'selected' : '' }}>Kasir</option>
                    </select>
                    @error('ROLE')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- GROUP 3: Alamat --}}
            <div class="space-y-3 pt-1">
                <div>
                    <label class="block text-sm font-semibold mb-1">Alamat</label>
                    <textarea
                        name="AL"
                        rows="2"
                        placeholder="Masukkan alamat lengkap..."
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db resize-none"
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
                    class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-100 cursor-pointer transition-colors"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btnTambah"
                    class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold cursor-pointer transition-all duration-200 shadow-xs hover:shadow"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================= --}}
{{-- MODAL EDIT USER                                           --}}
{{-- ========================================================= --}}
<div
    id="editUserModal"
    class="fixed inset-0 z-50 {{ $errors->editUser->any() ? 'flex' : 'hidden' }} items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
>
    <div class="absolute inset-0" onclick="closeEditModal()"></div>

    <div class="relative w-full max-w-lg bg-secondary-bw border border-gray-300 p-6 rounded-2xl shadow-2xl z-10 text-black max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-5 tracking-tight">Edit User</h2>

        <form
            id="editUserForm"
            method="POST"
            class="space-y-5"
            action="{{ $errors->editUser->any() && old('USERNAME') ? route('POS.user.update', ['id' => old('USERNAME')]) : '' }}"
        >
            @csrf
            @method('PUT')

            {{-- GROUP 1: Informasi Akun (Akses) --}}
            <div class="space-y-3 pt-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- USERNAME --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">Username</label>
                        <input
                            id="editUSERNAME"
                            type="text"
                            name="USERNAME"
                            readonly
                            class="w-full bg-gray-100 border border-black/80 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 cursor-not-allowed focus:outline-none"
                            value="{{ $errors->editUser->any() ? old('USERNAME') : '' }}"
                        >
                    </div>

                    {{-- PASSWORD (Opsional saat Edit) --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Password <span class="text-xs font-normal text-gray-500">(Kosongi jika tak diubah)</span>
                        </label>
                        <input
                            id="editPASS"
                            type="password"
                            name="PASS"
                            placeholder="Masukkan password baru"
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                        >
                        @error('PASS', 'editUser')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- GROUP 2: Data Diri & Role --}}
            <div class="space-y-3 pt-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- NAMA LENGKAP (DES) --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Lengkap</label>
                        <input
                            id="editDES"
                            type="text"
                            name="DES"
                            required
                            placeholder="Masukkan nama"
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            value="{{ $errors->editUser->any() ? old('DES') : '' }}"
                        >
                        @error('DES', 'editUser')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NOMOR HANDPHONE (HP) --}}
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nomor Handphone</label>
                        <input
                            id="editHP"
                            type="text"
                            name="HP"
                            required
                            placeholder="08123456789"
                            class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                            value="{{ $errors->editUser->any() ? old('HP') : '' }}"
                        >
                        @error('HP', 'editUser')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ROLE --}}
                <div>
                    <label class="block text-sm font-semibold mb-1">Role</label>
                    <select
                        id="editROLE"
                        name="ROLE"
                        required
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db"
                    >
                        <option value="1">Admin</option>
                        <option value="2">Kasir</option>
                    </select>
                    @error('ROLE', 'editUser')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- GROUP 3: Alamat --}}
            <div class="space-y-3 pt-1">
                <div>
                    <label class="block text-sm font-semibold mb-1">Alamat</label>
                    <textarea
                        id="editAL"
                        name="AL"
                        rows="2"
                        placeholder="Masukkan alamat lengkap..."
                        class="w-full bg-white border border-black/80 rounded-xl px-3 py-2 text-sm transition-all duration-200 hover:border-primary-db hover:bg-primary-db/5 focus:outline-none focus:ring-1 focus:ring-primary-db focus:border-primary-db resize-none"
                    >{{ $errors->editUser->any() ? old('AL') : '' }}</textarea>
                    @error('AL', 'editUser')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3">
                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="flex-1 py-2.5 px-4 bg-white border border-black/80 rounded-xl text-sm font-semibold hover:bg-gray-100 cursor-pointer transition-colors"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="flex-1 py-2.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-sm font-semibold cursor-pointer transition-all duration-200 shadow-xs hover:shadow"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
