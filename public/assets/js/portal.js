(() => {
  const sections = [...document.querySelectorAll('.portal-page > section')];
  if ('IntersectionObserver' in window) {
    document.body.classList.add('portal-motion');
    sections.forEach((section) => section.classList.add('portal-reveal'));
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08 });
    sections.forEach((section) => observer.observe(section));
  }

  document.querySelectorAll('[data-location-fill]').forEach((button) => {
    button.addEventListener('click', () => {
      const form = button.closest('form');
      if (!navigator.geolocation || !form) {
        button.textContent = 'Location is not available on this device';
        return;
      }
      button.disabled = true;
      button.textContent = 'Getting current location...';
      navigator.geolocation.getCurrentPosition((position) => {
        form.elements.latitude.value = position.coords.latitude.toFixed(7);
        form.elements.longitude.value = position.coords.longitude.toFixed(7);
        form.elements.accuracy.value = Math.round(position.coords.accuracy);
        button.textContent = 'Current location ready';
        button.disabled = false;
      }, () => {
        button.textContent = 'Location permission was not granted';
        button.disabled = false;
      }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 30000 });
    });
  });
})();
