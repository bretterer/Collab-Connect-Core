import './bootstrap';

// Import toaster
import '../../vendor/masmerise/livewire-toaster/resources/js';

import { TextStyleKit } from '@tiptap/extension-text-style';

// Import Plyr for video player
import Plyr from 'plyr';
import 'plyr/dist/plyr.css';
window.Plyr = Plyr;

// Import Stripe
import { loadStripe } from '@stripe/stripe-js';
window.Stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);

// Notify that Stripe is loaded and ready
document.dispatchEvent(new CustomEvent('stripe:loaded'));

// Import images so Vite can process them
import.meta.glob([
  '../images/**',
]);

document.addEventListener('flux:editor', (e) => {
  e.detail.registerExtensions([
    TextStyleKit,
  ]);
});