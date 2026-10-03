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

// Alpine hanya dijalankan sekali; pengecekan ini mencegah Alpine ganda bila
// halaman juga memuat Alpine dari CDN (layoutfull.blade.php).
if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}