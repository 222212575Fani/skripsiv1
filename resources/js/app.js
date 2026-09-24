import './bootstrap';
import '../css/app.css';
import Swal from 'sweetalert2';
import Alpine from 'alpinejs';

window.Swal = Swal;

if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}