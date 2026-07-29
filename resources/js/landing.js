import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';
import Flip from 'gsap/Flip';
import './process-stack-init.jsx';

gsap.registerPlugin(ScrollTrigger, Flip);

window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;
window.Flip = Flip;
