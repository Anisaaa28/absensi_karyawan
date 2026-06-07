import './bootstrap';
import './alpine-utilities';

document.addEventListener('turbo:load', () => {
  if (window.AlpineUtilities) {
    window.AlpineUtilities.attachReveal();
    window.AlpineUtilities.attachTilt();
    window.AlpineUtilities.attachConfirmables();
  }
});
