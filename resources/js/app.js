import './bootstrap';

// Import toaster
import '../../vendor/masmerise/livewire-toaster/resources/js';

// Import Stripe
import { loadStripe } from '@stripe/stripe-js';
window.Stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);

// Notify that Stripe is loaded and ready
document.dispatchEvent(new CustomEvent('stripe:loaded'));


// Import images so Vite can process them
import.meta.glob([
  '../images/**',
]);