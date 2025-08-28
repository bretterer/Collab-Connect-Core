import './bootstrap';

// Import toaster
import '../../vendor/masmerise/livewire-toaster/resources/js';

// Import html2canvas for screenshot functionality
import html2canvas from 'html2canvas';

// Make html2canvas globally available
window.html2canvas = html2canvas;

// Import images so Vite can process them
import.meta.glob([
  '../images/**',
]);