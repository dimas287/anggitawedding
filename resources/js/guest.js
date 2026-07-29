import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();

window.flatpickr = flatpickr;
