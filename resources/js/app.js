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
// VALIDASI FORMULIR LIVE SERAGAM
// Gelembung validasi bawaan peramban (bahasa mengikuti peramban, tampilannya tidak bisa diubah)
// dimatikan. Sebagai gantinya, di bawah kolom muncul "pil" status seperti pada halaman registrasi:
// abu-abu (aturan yang harus dipenuhi), hijau dengan centang (sudah benar) dan merah dengan silang
// (salah), diperbarui langsung saat pengguna mengetik. Aturannya diambil dari atribut HTML
// (required, minlength, pattern + title, type=email), jadi formulir tidak perlu diubah satu per satu.
// Kolom yang sudah punya pil sendiri (halaman registrasi, modal tambah pengguna) tidak ditambahi.
// =========================================================================
const IKON_PIL = {
    netral: '<span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>',
    benar: '<svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
    salah: '<svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
};
const KELAS_PIL = {
    netral: 'bg-gray-50 text-gray-500 border border-gray-200/70',
    benar: 'bg-emerald-50 text-emerald-700 border border-emerald-200/70 font-medium',
    salah: 'bg-rose-50 text-rose-600 border border-rose-200/70 font-medium',
};
const KELAS_KOLOM_SALAH = ['!border-rose-400', '!ring-2', '!ring-rose-100'];
const TIPE_DIABAIKAN = ['hidden', 'checkbox', 'radio', 'file', 'submit', 'button', 'reset', 'image'];

function kolomBisaDivalidasi(el) {
    // Formulir ber-atribut novalidate (mis. halaman login) mengurus validasinya sendiri, jadi dilewati
    return el && el.matches && el.matches('input, select, textarea')
        && !TIPE_DIABAIKAN.includes(el.type) && !el.disabled && el.form && !el.form.noValidate;
}

// Kalimat aturan yang tampil pada pil abu-abu/hijau; kosong bila kolom hanya "wajib diisi"
// Kolom angka: satuan (mis. "%") dan keterangan tambahan (mis. "sisa progress") dibaca dari atribut
// data-satuan dan data-keterangan pada kolomnya
function teksBatas(el, nilai) {
    const satuan = el.dataset.satuan || '';
    return `${nilai}${satuan}`;
}
function keteranganKolom(el) {
    return el.dataset.keterangan ? ` (${el.dataset.keterangan})` : '';
}

function aturanKolom(el) {
    if (el.pattern) return el.title || 'Format isian sesuai';
    if (el.type === 'email') return 'Format email valid';
    if (el.minLength > 0) return `Minimal ${el.minLength} karakter`;
    if (el.type === 'number' && el.min !== '' && el.max !== '') {
        return `Nilai ${teksBatas(el, el.min)} sampai ${teksBatas(el, el.max)}${keteranganKolom(el)}`;
    }
    return '';
}

function pesanValidasi(el) {
    const v = el.validity;
    if (v.valueMissing) return el.tagName === 'SELECT' ? 'Pilih salah satu opsi' : 'Kolom ini wajib diisi';
    if (v.tooShort) return `Minimal ${el.minLength} karakter (saat ini ${el.value.length} karakter)`;
    if (v.tooLong) return `Maksimal ${el.maxLength} karakter`;
    if (v.patternMismatch) return el.title || 'Format isian tidak sesuai';
    if (v.typeMismatch) return el.type === 'email' ? 'Format email tidak valid' : 'Format isian tidak valid';
    if (v.rangeUnderflow) return `Nilai minimal ${teksBatas(el, el.min)}`;
    if (v.rangeOverflow) return `Nilai maksimal ${teksBatas(el, el.max)}${keteranganKolom(el)}`;
    return el.validationMessage || 'Isian tidak valid';
}

// Pil diletakkan setelah pembungkus kolom bila kolom berada di dalam pembungkus berposisi relatif
// (mis. kolom dengan ikon di dalamnya), agar letak ikon tidak bergeser.
function titikPil(el) {
    const induk = el.parentElement;
    if (induk && getComputedStyle(induk).position === 'relative'
        && induk.querySelectorAll('input, select, textarea').length === 1) {
        return induk;
    }
    return el;
}

function pilKita(el) {
    const n = titikPil(el).nextElementSibling;
    return n && n.classList.contains('proxis-pil') ? n : null;
}

function sudahPunyaPilSendiri(el) {
    const n = titikPil(el).nextElementSibling;
    return !!(n && !n.classList.contains('proxis-pil') && n.querySelector(':scope > span.rounded-full'));
}

function gambarPil(el, status, teks) {
    let c = pilKita(el);
    if (!c) {
        c = document.createElement('div');
        c.className = 'proxis-pil mt-1.5 flex flex-wrap items-center gap-1.5';
        titikPil(el).insertAdjacentElement('afterend', c);
    }
    c.innerHTML = `<span class="inline-flex items-center justify-center gap-1.5 w-fit max-w-full whitespace-nowrap px-3 py-0.5 rounded-full text-[11px] transition-all duration-200 ${KELAS_PIL[status]}">${IKON_PIL[status]}<span></span></span>`;
    c.querySelector('span > span:last-child').textContent = teks;

    if (status === 'salah') el.classList.add(...KELAS_KOLOM_SALAH);
    else el.classList.remove(...KELAS_KOLOM_SALAH);
}

function hapusPil(el) {
    pilKita(el)?.remove();
    el.classList.remove(...KELAS_KOLOM_SALAH);
}

// Menentukan tampilan pil untuk satu kolom pada saat ini
function perbaruiPil(el) {
    if (!kolomBisaDivalidasi(el) || sudahPunyaPilSendiri(el)) return;

    const aturan = aturanKolom(el);
    const dipaksa = el.dataset.pilPaksa === '1';       // sudah gagal saat Simpan ditekan
    const selesaiDiisi = el.dataset.pilBlur === '1';   // pengguna sudah meninggalkan kolom
    const kosong = el.value === '';
    const v = el.validity;

    // Kolom hanya "wajib diisi" (tanpa aturan lain): tidak ada pil sampai Simpan ditekan
    if (!aturan && !dipaksa) {
        hapusPil(el);
        return;
    }

    if (el.checkValidity()) {
        delete el.dataset.pilPaksa;
        if (!aturan) {                                      // kolom hanya "wajib diisi": tidak perlu pil
            hapusPil(el);
            return;
        }
        if (kosong) gambarPil(el, 'netral', aturan);       // kolom opsional yang kosong
        else gambarPil(el, 'benar', aturan);
        return;
    }

    if (dipaksa && v.valueMissing) {
        // Kolom wajib yang dikosongkan: cukup garis merah di kolomnya, karena tanda * pada label
        // sudah menjelaskan bahwa kolom ini wajib diisi (tidak perlu kalimat tambahan)
        if (aturan) gambarPil(el, 'netral', aturan);
        else hapusPil(el);
        el.classList.add(...KELAS_KOLOM_SALAH);
    } else if (dipaksa) {
        gambarPil(el, 'salah', pesanValidasi(el));
    } else if (kosong || v.tooShort) {
        const hitung = v.tooShort ? ` (${el.value.length}/${el.minLength})` : '';
        gambarPil(el, 'netral', aturan + hitung);
    } else if (v.typeMismatch || v.rangeOverflow || v.rangeUnderflow || (v.patternMismatch && selesaiDiisi)) {
        gambarPil(el, 'salah', pesanValidasi(el));
    } else {
        gambarPil(el, 'netral', aturan);
    }
}

// Saat kolom disentuh atau diisi, pil langsung tampil dan diperbarui
['focusin', 'input', 'change'].forEach((nama) => {
    document.addEventListener(nama, (e) => perbaruiPil(e.target), true);
});
document.addEventListener('focusout', (e) => {
    if (!kolomBisaDivalidasi(e.target)) return;
    e.target.dataset.pilBlur = '1';
    if (pilKita(e.target) || aturanKolom(e.target)) perbaruiPil(e.target);
}, true);

// Event "invalid" tidak menggelembung, jadi dipasang pada fase capture. preventDefault()
// mematikan gelembung bawaan peramban; fokus dipindah manual ke kolom pertama yang bermasalah.
let sudahFokus = false;
document.addEventListener('invalid', (e) => {
    const el = e.target;
    e.preventDefault();
    el.dataset.pilPaksa = '1';
    el.dataset.pilBlur = '1';
    perbaruiPil(el);

    if (!sudahFokus) {
        sudahFokus = true;
        el.focus({ preventScroll: true });
        el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        setTimeout(() => { sudahFokus = false; }, 0);
    }
}, true);

// Pil pada kolom yang sudah tidak terlihat (mis. modal ditutup lalu dibuka lagi) dibersihkan,
// beserta penanda "sudah gagal" agar formulir yang dibuka ulang kembali bersih
function setelPilTersembunyi() {
    document.querySelectorAll('.proxis-pil').forEach((c) => {
        const kolom = c.previousElementSibling;
        const el = kolom && kolom.matches && kolom.matches('input, select, textarea')
            ? kolom
            : kolom?.querySelector?.('input, select, textarea');
        if (!el) {
            c.remove();
        } else if (el.offsetParent === null) {
            // Dikembalikan ke keadaan awal (abu-abu berisi aturan) agar formulir dibuka ulang bersih
            delete el.dataset.pilPaksa;
            delete el.dataset.pilBlur;
            perbaruiPil(el);
        } else {
            // Batas kolom bisa berubah tiap formulir dibuka (mis. sisa progress), jadi pil disegarkan
            perbaruiPil(el);
        }
    });
}
document.addEventListener('click', () => setTimeout(setelPilTersembunyi, 50));
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') setTimeout(setelPilTersembunyi, 50);
});

// Pil aturan dipasang sejak halaman dibuka (tidak menunggu kolom diklik), termasuk untuk
// kolom di dalam modal yang masih tersembunyi dan kolom yang baru muncul belakangan
function pasangPilAwal() {
    document.querySelectorAll('input, select, textarea').forEach((el) => {
        if (kolomBisaDivalidasi(el) && aturanKolom(el) && !sudahPunyaPilSendiri(el) && !pilKita(el)) {
            perbaruiPil(el);
        }
    });
}
let jadwalPilAwal = null;
new MutationObserver(() => {
    clearTimeout(jadwalPilAwal);
    jadwalPilAwal = setTimeout(pasangPilAwal, 100);
}).observe(document.documentElement, { childList: true, subtree: true });
document.addEventListener('DOMContentLoaded', () => setTimeout(pasangPilAwal, 0));

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
