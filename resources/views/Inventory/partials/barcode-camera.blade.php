@if(false)
{{--
    =========================================================================
    PARTIAL: Scanner Barcode via Kamera
    =========================================================================
    Simpan di: resources/views/partials/barcode-camera.blade.php

    Pemakaian:
        @include('partials.barcode-camera', ['cameraId' => 'stok'])

    Hasil scan dikirim lewat event DOM 'barcode:scan' pada elemen #bcam-{id}:
        document.getElementById('bcam-stok').addEventListener('barcode:scan', e => {
            const kode = e.detail.code;     // SELALU string (leading zero aman)
            const sumber = e.detail.source; // 'live' | 'foto'
        });

    API global:
        BarcodeCamera.start('stok')                      // nyalakan kamera live
        BarcodeCamera.stop('stok')                       // matikan kamera
        BarcodeCamera.stop('stok', {keepResume: true})   // matikan tapi nyala lagi setelah reload
        BarcodeCamera.stopAll()

    DUA MODE:
    1. LIVE   -> pakai getUserMedia. Dicoba langsung tanpa cek isSecureContext,
                 sehingga jalan di HTTP maupun HTTPS. Kalau browser menolak,
                 pesan error yang ramah akan ditampilkan.
    2. FOTO   -> <input type="file" capture="environment"> membuka aplikasi kamera
                 bawaan HP. TIDAK butuh HTTPS. Foto lalu di-decode di browser.

    Scanner fisik (keyboard wedge) tetap jalan lewat input teks biasa di halaman
    pemanggil; partial ini tidak mengganggunya.

    LIBRARY: html5-qrcode 2.3.8 (di-host lokal supaya jalan tanpa internet).
    =========================================================================
--}}
@php
    $cid    = $cameraId ?? 'cam';
    $libSrc = asset('js/html5-qrcode.min.js');
@endphp

<div id="bcam-{{ $cid }}" class="bcam w-full rounded-2xl overflow-hidden border border-slate-300 bg-white shadow-inner" data-bcam="{{ $cid }}">

    {{-- Area preview kamera live --}}
    <div id="bcam-{{ $cid }}-reader" class="hidden w-full bg-black min-h-[260px] md:min-h-[340px] flex items-center justify-center overflow-hidden"></div>

    {{-- Tampilan saat kamera belum aktif --}}
    <div data-bcam-idle class="h-48 md:h-64 bg-slate-100 flex flex-col items-center justify-center gap-3 text-slate-500 px-4 text-center">
        <div class="w-14 h-14 rounded-full bg-slate-200 flex items-center justify-center text-slate-600">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-700">Area Pemindaian Barcode</p>
            <span class="text-xs text-slate-500">Klik "Kamera live" untuk menyalakan pemindai kamera</span>
        </div>
    </div>

    {{-- Tombol kontrol --}}
    <div class="flex items-center gap-3 p-3 border-t border-slate-200 bg-slate-50">
        <button type="button" data-bcam-live
                class="flex-1 py-2.5 px-4 rounded-xl text-xs md:text-sm font-semibold text-white bg-[#0D1C43] hover:bg-[#22396F] transition-colors cursor-pointer shadow-sm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
            </svg>
            Kamera live
        </button>
        <button type="button" data-bcam-photo
                class="flex-1 py-2.5 px-4 rounded-xl text-xs md:text-sm font-semibold text-[#0D1C43] bg-white border border-slate-300 hover:bg-slate-100 transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            Foto barcode
        </button>
        <button type="button" data-bcam-stop
                class="hidden py-2.5 px-4 rounded-xl text-xs md:text-sm font-semibold text-white bg-[#D02231] hover:bg-red-700 transition-colors cursor-pointer shadow-sm">
            Stop Kamera
        </button>
    </div>

    {{-- Pesan status --}}
    <p data-bcam-msg class="hidden px-4 py-2 text-xs font-medium leading-snug bg-slate-50 border-t border-slate-100"></p>

    {{-- Input foto: membuka kamera bawaan HP, jalan di HTTP --}}
    <input type="file" accept="image/*" capture="environment" class="hidden" data-bcam-file>

    {{-- Host tersembunyi yang dibutuhkan library untuk decode foto --}}
    <div id="bcam-{{ $cid }}-filehost" class="hidden"></div>
</div>

<style>
    .bcam video {
        width: 100% !important;
        height: auto !important;
        max-height: 380px !important;
        object-fit: cover !important;
        display: block !important;
    }
    .bcam [id$="-reader"] {
        border-radius: 0.5rem;
    }
</style>

@once
<script src="{{ $libSrc }}"></script>
<script>
(function () {
    if (window.BarcodeCamera) return;

    // Polyfill: di HTTP, navigator.mediaDevices mungkin undefined.
    // Kita coba inject supaya getUserMedia tetap bisa dicoba.
    if (!navigator.mediaDevices) {
        navigator.mediaDevices = {};
    }
    if (!navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia = function(constraints) {
            var gUM = navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
            if (!gUM) return Promise.reject(new Error('getUserMedia tidak didukung browser ini.'));
            return new Promise(function(resolve, reject) {
                gUM.call(navigator, constraints, resolve, reject);
            });
        };
    }
    var LIVE_OK    = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
    var COOLDOWN   = 2000; // ms: kode yang sama diabaikan selama jeda ini
    var states     = {};

    // ---------- util ----------
    function beep() {
        try {
            var AC = window.AudioContext || window.webkitAudioContext;
            var ctx = new AC(), osc = ctx.createOscillator();
            osc.frequency.value = 1100;
            osc.connect(ctx.destination);
            osc.start();
            setTimeout(function () { osc.stop(); ctx.close(); }, 110);
        } catch (e) {}
        if (navigator.vibrate) navigator.vibrate(70);
    }

    function msg(st, text, kind) {
        var el = st.msg;
        if (!text) { el.classList.add('hidden'); el.textContent = ''; return; }
        var warna = {
            info:  'text-slate-500',
            ok:    'text-[#327437]',
            error: 'text-[#D02231]'
        };
        el.className = 'px-3 pb-2 text-[11px] leading-snug ' + (warna[kind] || warna.info);
        el.textContent = text;
    }

    // Barcode SELALU diperlakukan sebagai string: tanpa Number(), tanpa parseInt.
    // Leading zero ("00123") dan huruf ("INV-001") tetap utuh.
    function emit(st, raw, source) {
        var code = String(raw == null ? '' : raw).replace(/[\u0000-\u001F\u007F]/g, '').trim();
        if (!code) return;

        var now = Date.now();
        if (code === st.last && (now - st.lastAt) < COOLDOWN) return;
        st.last = code;
        st.lastAt = now;

        beep();
        msg(st, 'Terbaca: ' + code, 'ok');
        st.root.dispatchEvent(new CustomEvent('barcode:scan', {
            bubbles: true,
            detail: { code: code, source: source }
        }));
    }

    function resumeKey(id) { return 'bcam:resume:' + id; }
    function setResume(id, on) {
        try { on ? sessionStorage.setItem(resumeKey(id), '1') : sessionStorage.removeItem(resumeKey(id)); } catch (e) {}
    }
    function getResume(id) {
        try { return sessionStorage.getItem(resumeKey(id)) === '1'; } catch (e) { return false; }
    }

    function setRunningUI(st, running) {
        st.reader.classList.toggle('hidden', !running);
        st.idle.classList.toggle('hidden', running);
        st.btnStop.classList.toggle('hidden', !running);
        st.btnLive.classList.toggle('hidden', running);
        st.btnPhoto.classList.toggle('hidden', running);
    }

    // ---------- kamera live ----------
    function start(id) {
        var st = states[id];
        if (!st || st.running) return Promise.resolve();

        if (!LIVE_OK) {
            msg(st, 'Browser tidak mendukung kamera. Pakai "Foto barcode" untuk membuka kamera HP.', 'error');
            return Promise.resolve();
        }
        if (typeof Html5Qrcode === 'undefined') {
            msg(st, 'Library scanner belum termuat. Periksa koneksi atau path file JS.', 'error');
            return Promise.resolve();
        }

        st.live = new Html5Qrcode(st.reader.id, {
            verbose: false,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        });

        // Kotak scan melebar: cocok untuk barcode 1D panjang maupun QR code
        var cfg = {
            fps: 15,
            qrbox: function (w, h) {
                var boxWidth = Math.floor(w * 0.90);
                var boxHeight = Math.floor(Math.min(h * 0.75, Math.max(160, w * 0.50)));
                return { width: boxWidth, height: boxHeight };
            },
            aspectRatio: 1.777778
        };

        setRunningUI(st, true);
        msg(st, 'Menyalakan kamera...', 'info');

        return st.live.start(
            { facingMode: 'environment' },
            cfg,
            function (text) { emit(st, text, 'live'); },
            function () { /* frame tanpa barcode: abaikan */ }
        ).then(function () {
            st.running = true;
            setResume(id, true);
            msg(st, 'Kamera aktif. Dekatkan barcode ke kotak.', 'info');
        }).catch(function (err) {
            st.running = false;
            st.live = null;
            setRunningUI(st, false);
            var nama = (err && err.name) || '';
            if (nama === 'NotAllowedError' || /permission/i.test(String(err))) {
                msg(st, 'Izin kamera ditolak. Izinkan akses kamera di pengaturan browser.', 'error');
            } else if (nama === 'NotFoundError') {
                msg(st, 'Kamera tidak ditemukan di perangkat ini.', 'error');
            } else {
                msg(st, 'Kamera gagal dinyalakan: ' + (err && err.message ? err.message : err), 'error');
            }
        });
    }

    function stop(id, opt) {
        var st = states[id];
        if (!st) return Promise.resolve();
        if (!(opt && opt.keepResume)) setResume(id, false);

        var live = st.live;
        st.live = null;
        st.running = false;
        setRunningUI(st, false);
        msg(st, '', 'info');
        if (!live) return Promise.resolve();

        return live.stop().catch(function () {}).then(function () {
            try { live.clear(); } catch (e) {}
        });
    }

    function stopAll(opt) {
        Object.keys(states).forEach(function (id) { stop(id, opt); });
    }

    // ---------- scan dari foto (tanpa HTTPS) ----------
    // Perkecil foto HP (12 MP) agar decode lebih cepat dan stabil.
    function downscale(file, max) {
        if (!window.createImageBitmap) return Promise.resolve(file);
        return createImageBitmap(file).then(function (bmp) {
            var s = Math.min(1, max / Math.max(bmp.width, bmp.height));
            if (s === 1) return file;
            var c = document.createElement('canvas');
            c.width = Math.round(bmp.width * s);
            c.height = Math.round(bmp.height * s);
            c.getContext('2d').drawImage(bmp, 0, 0, c.width, c.height);
            return new Promise(function (resolve) {
                c.toBlob(function (b) {
                    resolve(b ? new File([b], 'scan.jpg', { type: 'image/jpeg' }) : file);
                }, 'image/jpeg', 0.92);
            });
        }).catch(function () { return file; });
    }

    function scanFoto(st, file) {
        if (!file) return;
        if (typeof Html5Qrcode === 'undefined') {
            msg(st, 'Library scanner belum termuat. Periksa koneksi atau path file JS.', 'error');
            return;
        }
        msg(st, 'Membaca foto...', 'info');

        downscale(file, 1600).then(function (f) {
            var h = new Html5Qrcode(st.filehost.id, { verbose: false });
            return h.scanFile(f, false).then(function (text) {
                try { h.clear(); } catch (e) {}
                emit(st, text, 'foto');
            }).catch(function () {
                try { h.clear(); } catch (e) {}
                msg(st, 'Barcode tidak terbaca. Foto lebih dekat, posisi lurus, dan pastikan terang.', 'error');
            });
        }).then(function () {
            st.file.value = ''; // supaya foto yang sama bisa dipilih lagi
        });
    }

    // ---------- inisialisasi tiap instance ----------
    function init(root) {
        var id = root.getAttribute('data-bcam');
        if (states[id]) return;

        var st = {
            id: id, root: root,
            reader:   root.querySelector('#bcam-' + id + '-reader'),
            filehost: root.querySelector('#bcam-' + id + '-filehost'),
            idle:     root.querySelector('[data-bcam-idle]'),
            msg:      root.querySelector('[data-bcam-msg]'),
            btnLive:  root.querySelector('[data-bcam-live]'),
            btnPhoto: root.querySelector('[data-bcam-photo]'),
            btnStop:  root.querySelector('[data-bcam-stop]'),
            file:     root.querySelector('[data-bcam-file]'),
            live: null, running: false, last: '', lastAt: 0
        };
        states[id] = st;

        if (!LIVE_OK) {
            st.btnLive.disabled = true;
            st.btnLive.title = 'Browser tidak mendukung getUserMedia';
            msg(st, 'Browser tidak mendukung kamera live. Pakai "Foto barcode" (membuka kamera HP).', 'info');
        }

        st.btnLive.addEventListener('click', function () { start(id); });
        st.btnStop.addEventListener('click', function () { stop(id); });
        st.btnPhoto.addEventListener('click', function () { st.file.click(); });
        st.file.addEventListener('change', function () { scanFoto(st, st.file.files && st.file.files[0]); });

        // Lanjutkan kamera setelah halaman reload (mis. setelah simpan stok),
        // hanya kalau areanya sedang terlihat (modal terbuka).
        if (LIVE_OK && getResume(id) && root.offsetParent !== null) start(id);
    }

    function initAll() {
        document.querySelectorAll('.bcam').forEach(init);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll);
    else initAll();

    // Matikan kamera saat tab disembunyikan / halaman ditinggalkan
    window.addEventListener('pagehide', function () { stopAll({ keepResume: true }); });

    window.BarcodeCamera = { start: start, stop: stop, stopAll: stopAll, liveSupported: LIVE_OK };
})();
</script>
@endonce
@endif