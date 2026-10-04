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

// Pesan validasi bawaan peramban (mis. "Please lengthen this text...") diganti bahasa Indonesia
// untuk semua formulir. Event "invalid" tidak menggelembung, jadi dipasang pada fase capture.
document.addEventListener('invalid', (e) => {
    const el = e.target;
    const v = el.validity;
    el.setCustomValidity('');

    let pesan = '';
    if (v.valueMissing) {
        pesan = el.type === 'checkbox' ? 'Centang kolom ini untuk melanjutkan.'
            : (el.tagName === 'SELECT' ? 'Pilih salah satu opsi.' : 'Kolom ini wajib diisi.');
    } else if (v.tooShort) {
        pesan = `Minimal ${el.minLength} karakter (saat ini ${el.value.length} karakter).`;
    } else if (v.tooLong) {
        pesan = `Maksimal ${el.maxLength} karakter.`;
    } else if (v.patternMismatch) {
        pesan = el.title || 'Format isian tidak sesuai.';
    } else if (v.typeMismatch) {
        pesan = el.type === 'email' ? 'Format email tidak valid.' : 'Format isian tidak valid.';
    } else if (v.rangeUnderflow) {
        pesan = `Nilai minimal ${el.min}.`;
    } else if (v.rangeOverflow) {
        pesan = `Nilai maksimal ${el.max}.`;
    } else if (v.badInput || v.stepMismatch) {
        pesan = 'Isian tidak valid.';
    }

    if (pesan) el.setCustomValidity(pesan);
}, true);

// Pesan khusus dihapus begitu isian diubah, agar validasi dihitung ulang
document.addEventListener('input', (e) => {
    if (typeof e.target.setCustomValidity === 'function') e.target.setCustomValidity('');
}, true);

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