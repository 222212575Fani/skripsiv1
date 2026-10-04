/**
 * =========================================================================
 * TITIK MASUK JAVASCRIPT (dibangun oleh Vite, lihat @vite di layout)
 * - bootstrap.js : mengatur pengiriman permintaan HTTP (axios)
 * - app.css      : gaya Tailwind CSS v4
 * - SweetAlert2  : dialog konfirmasi dan pesan (window.Swal)
 * - Alpine.js    : interaksi di tampilan (modal, dropdown, filter, polling lonceng)
 * =========================================================================
 */
import './bootstrap';
import '../css/app.css';
import Swal from 'sweetalert2';
import Alpine from 'alpinejs';

window.Swal = Swal;

// =========================================================================
// VALIDASI FORMULIR SERAGAM
// Gelembung validasi bawaan peramban (bahasa mengikuti peramban, tampilannya tidak bisa diubah)
// dimatikan. Sebagai gantinya muncul pesan merah kecil berbahasa Indonesia tepat di bawah kolom
// yang bermasalah, di semua formulir. Aturannya tetap dari atribut HTML (required, minlength,
// pattern, type=email, dst.), jadi formulir tidak perlu diubah satu per satu.
// =========================================================================
const IKON_ERROR = '<svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
const KELAS_KOLOM_ERROR = ['!border-rose-400', '!ring-2', '!ring-rose-100'];

function pesanValidasi(el) {
    const v = el.validity;
    if (v.valueMissing) {
        if (el.type === 'checkbox') return 'Centang kolom ini untuk melanjutkan.';
        return el.tagName === 'SELECT' ? 'Pilih salah satu opsi.' : 'Kolom ini wajib diisi.';
    }
    if (v.tooShort) return `Minimal ${el.minLength} karakter (saat ini ${el.value.length} karakter).`;
    if (v.tooLong) return `Maksimal ${el.maxLength} karakter.`;
    if (v.patternMismatch) return el.title || 'Format isian tidak sesuai.';
    if (v.typeMismatch) return el.type === 'email' ? 'Format email tidak valid.' : 'Format isian tidak valid.';
    if (v.rangeUnderflow) return `Nilai minimal ${el.min}.`;
    if (v.rangeOverflow) return `Nilai maksimal ${el.max}.`;
    if (v.badInput || v.stepMismatch) return 'Isian tidak valid.';
    return el.validationMessage || 'Isian tidak valid.';
}

// Pesan diletakkan setelah pembungkus kolom bila kolom berada di dalam pembungkus berposisi relatif
// (mis. kolom dengan ikon di dalamnya), agar letak ikon tidak bergeser.
function titikPesan(el) {
    const induk = el.parentElement;
    if (induk && getComputedStyle(induk).position === 'relative'
        && induk.querySelectorAll('input, select, textarea').length === 1) {
        return induk;
    }
    return el;
}

function pesanDi(el) {
    return titikPesan(el).nextElementSibling?.classList.contains('proxis-error')
        ? titikPesan(el).nextElementSibling
        : null;
}

function tampilkanError(el) {
    const teks = pesanValidasi(el);
    let p = pesanDi(el);
    if (!p) {
        p = document.createElement('p');
        p.className = 'proxis-error mt-1.5 flex items-center gap-1 text-[11px] font-normal text-rose-600';
        p.setAttribute('role', 'alert');
        titikPesan(el).insertAdjacentElement('afterend', p);
    }
    p.innerHTML = `${IKON_ERROR}<span></span>`;
    p.querySelector('span').textContent = teks;
    el.classList.add(...KELAS_KOLOM_ERROR);
}

function hapusError(el) {
    pesanDi(el)?.remove();
    el.classList.remove(...KELAS_KOLOM_ERROR);
}

// Event "invalid" tidak menggelembung, jadi dipasang pada fase capture. preventDefault()
// mematikan gelembung bawaan peramban; fokus dipindah manual ke kolom pertama yang bermasalah.
let sudahFokus = false;
document.addEventListener('invalid', (e) => {
    const el = e.target;
    e.preventDefault();
    tampilkanError(el);

    if (!sudahFokus) {
        sudahFokus = true;
        el.focus({ preventScroll: true });
        el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        setTimeout(() => { sudahFokus = false; }, 0);
    }
}, true);

// Saat pengguna mengetik: pesan hilang bila sudah benar, atau diperbarui bila masih salah
['input', 'change'].forEach((nama) => {
    document.addEventListener(nama, (e) => {
        const el = e.target;
        if (!el.classList || !el.classList.contains('!border-rose-400') || typeof el.checkValidity !== 'function') return;
        if (el.checkValidity()) hapusError(el);
        else tampilkanError(el);
    }, true);
});

// Pesan sisa pada kolom yang sudah tidak terlihat (mis. modal ditutup lalu dibuka lagi) dibersihkan
function bersihkanErrorTersembunyi() {
    document.querySelectorAll('.proxis-error').forEach((p) => {
        const kolom = p.previousElementSibling;
        const el = kolom && kolom.matches && kolom.matches('input, select, textarea')
            ? kolom
            : kolom?.querySelector?.('input, select, textarea');
        if (!el || el.offsetParent === null) {
            p.remove();
            el?.classList.remove(...KELAS_KOLOM_ERROR);
        }
    });
}
document.addEventListener('click', () => setTimeout(bersihkanErrorTersembunyi, 50));
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') setTimeout(bersihkanErrorTersembunyi, 50);
});

// Mencegah formulir POST terkirim dua kali (klik ganda tombol simpan), yang membuat data
// tersimpan sekali lalu muncul pesan gagal karena data yang sama dikirim lagi.
// Formulir yang ditangani Alpine/fetch (event sudah di-preventDefault) tidak ikut dikunci.
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (e.defaultPrevented || (form.method || '').toLowerCase() !== 'post') return;

    if (form.dataset.terkirim === '1') {
        e.preventDefault();
        return;
    }

    form.dataset.terkirim = '1';
    const tombol = form.querySelectorAll('[type="submit"]');
    tombol.forEach((b) => b.classList.add('opacity-60', 'pointer-events-none'));

    // Jaga-jaga bila respons berupa unduhan (halaman tidak berpindah): buka kunci setelah 10 detik
    setTimeout(() => {
        form.dataset.terkirim = '0';
        tombol.forEach((b) => b.classList.remove('opacity-60', 'pointer-events-none'));
    }, 10000);
});

// Alpine hanya dijalankan sekali; pengecekan ini mencegah Alpine ganda bila
// halaman juga memuat Alpine dari CDN (layoutfull.blade.php).
if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}