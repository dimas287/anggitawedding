import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import Sortable from 'sortablejs';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();

window.gsap = gsap;
gsap.registerPlugin(ScrollTrigger);
window.ScrollTrigger = ScrollTrigger;

window.flatpickr = flatpickr;

window.Sortable = Sortable;

window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    listPlugin,
};

import './process-stack-init';
