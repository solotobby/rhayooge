window.addEventListener('scroll', () => {
  const header = document.getElementById('header');
  if (!header) return;
  header.classList.toggle('is-top', window.scrollY < 24);
}, { passive: true });

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('header')?.classList.toggle('is-top', window.scrollY < 24);
});

document.addEventListener('livewire:navigated', () => {
  document.getElementById('header')?.classList.toggle('is-top', window.scrollY < 24);
});
