/**
 * =========================================================================
 * PENGATURAN AXIOS
 * Setiap permintaan axios membawa header X-Requested-With sehingga server
 * (mis. controller yang memakai $request->expectsJson()) mengenalinya sebagai
 * permintaan AJAX.
 * =========================================================================
 */
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
