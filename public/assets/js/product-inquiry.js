(function () {
  const modal = document.getElementById('inquiryModal');
  const open = document.querySelector('[data-open-inquiry]');
  const close = document.querySelector('[data-close-inquiry]');
  const form = document.getElementById('productInquiryForm');
  const message = document.getElementById('inquiryMessage');
  if (!modal || !open || !close || !form) return;

  function closeModal() { modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
  open.addEventListener('click', () => { modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; window.setTimeout(() => form.elements.name.focus(), 180); });
  close.addEventListener('click', closeModal);
  modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeModal(); });

  function error(field, text) {
    const input = form.elements[field];
    if (!input) return;
    input.classList.toggle('is-invalid', Boolean(text));
    const label = input.closest('label');
    const output = label?.querySelector('.inquiry-error');
    if (output) output.textContent = text || '';
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    ['name', 'email', 'phone', 'address', 'message'].forEach((field) => error(field, ''));
    message.textContent = '';
    message.classList.remove('is-error');
    const submit = document.getElementById('inquirySubmit');
    const payload = Object.fromEntries(new FormData(form).entries());
    submit.disabled = true;
    submit.textContent = 'Sending inquiry...';
    try {
      const response = await fetch(window.PRODUCT_INQUIRY.endpoint, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': window.PRODUCT_INQUIRY.csrfToken },
        body: JSON.stringify(payload),
      });
      const result = await response.json();
      if (!response.ok) {
        Object.entries(result.errors || {}).forEach(([field, text]) => error(field, text));
        throw new Error(result.message || 'We could not send your inquiry.');
      }
      form.reset();
      message.textContent = result.message;
    } catch (exception) {
      message.textContent = exception.message || 'We could not send your inquiry.';
      message.classList.add('is-error');
    } finally {
      submit.disabled = false;
      submit.innerHTML = 'Send inquiry <span aria-hidden="true">&#8594;</span>';
    }
  });
})();
