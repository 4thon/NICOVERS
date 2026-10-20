(function(){
  if(window.lucide){ window.lucide.createIcons(); }
  var loader = document.getElementById('siteLoader');
  window.addEventListener('load', function(){ window.setTimeout(function(){ loader.classList.add('is-hidden'); }, 220); });

  var navToggle = document.querySelector('[data-nav-toggle]');
  var nav = document.querySelector('[data-main-nav]');
  if(navToggle && nav){ navToggle.addEventListener('click', function(){ var open = nav.classList.toggle('is-open'); navToggle.setAttribute('aria-expanded', String(open)); }); }

  var activeModal = null;
  function openModal(id){ var modal = document.getElementById(id + 'Modal'); if(!modal) return; activeModal = modal; modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; var input = modal.querySelector('input, select, button'); if(input) input.focus(); }
  function closeModal(){ if(!activeModal) return; activeModal.classList.remove('is-open'); activeModal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; activeModal = null; }
  document.querySelectorAll('[data-open-modal]').forEach(function(button){ button.addEventListener('click', function(){ openModal(button.getAttribute('data-open-modal')); }); });
  document.querySelectorAll('[data-close-modal]').forEach(function(button){ button.addEventListener('click', closeModal); });
  document.querySelectorAll('.modal-layer').forEach(function(modal){ modal.addEventListener('click', function(event){ if(event.target === modal) closeModal(); }); });
  document.addEventListener('keydown', function(event){ if(event.key === 'Escape') closeModal(); });
  var quoteParams = new URLSearchParams(window.location.search);
  if(quoteParams.get('quote') === '1'){
    openModal('quote');
    var productDetails = document.getElementById('quoteDetails');
    if(productDetails && quoteParams.get('product')) productDetails.value = 'Product enquiry: ' + quoteParams.get('product');
  }

  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var revealElements = document.querySelectorAll('[data-reveal]');
  if(!reduceMotion && 'IntersectionObserver' in window){
    revealElements.forEach(function(element){ element.classList.add('reveal-ready'); });
    var observer = new IntersectionObserver(function(entries){ entries.forEach(function(entry){ if(entry.isIntersecting){ entry.target.classList.add('is-visible'); observer.unobserve(entry.target); } }); }, {threshold:.12});
    revealElements.forEach(function(element){ observer.observe(element); });
  } else { revealElements.forEach(function(element){ element.classList.add('is-visible'); }); }

  var parallax = document.querySelector('[data-parallax]');
  if(parallax && !reduceMotion){ var queued = false; window.addEventListener('scroll', function(){ if(queued) return; queued = true; window.requestAnimationFrame(function(){ parallax.style.transform = 'scale(1.06) translate3d(0,' + Math.min(window.scrollY * .09, 56) + 'px,0)'; queued = false; }); }, {passive:true}); }

  var form = document.getElementById('quoteForm');
  if(!form) return;
  var message = document.getElementById('quoteMessage');
  function setError(field, text){ var input = document.getElementById('quote' + field.charAt(0).toUpperCase() + field.slice(1)); if(!input) return; input.classList.toggle('is-invalid', Boolean(text)); var error = input.parentElement.querySelector('.modal-error'); if(error) error.textContent = text || ''; }
  form.addEventListener('submit', async function(event){
    event.preventDefault(); message.textContent = ''; message.classList.remove('is-error'); ['name','email','phone','address','service','preferredDate','details'].forEach(function(field){ setError(field, ''); });
    var data = {
      name: form.elements['name'].value.trim(),
      email: form.elements['email'].value.trim(),
      phone: form.elements['phone'].value.trim(),
      address: form.elements['address'].value.trim(),
      service: form.elements['service'].value,
      preferredDate: form.elements['preferredDate'].value,
      details: form.elements['details'].value.trim()
    };
    var button = document.getElementById('quoteSubmit'); button.disabled = true; button.textContent = 'Sending request...';
    try{
      var endpoints = window.HOME_ENDPOINTS || {}; var tokenResponse = await fetch(endpoints.csrf, {credentials:'same-origin'}); var token = await tokenResponse.json(); if(!tokenResponse.ok || !token.csrfToken) throw new Error('Could not start a secure request.');
      var response = await fetch(endpoints.quote, {method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json','X-CSRF-Token':token.csrfToken}, body:JSON.stringify(data)}); var result = await response.json();
      if(!response.ok){ Object.keys(result.errors || {}).forEach(function(field){ setError(field, result.errors[field]); }); throw new Error(result.message || 'Could not send your request.'); }
      form.reset(); message.textContent = result.message;
    }catch(error){ message.textContent = error.message || 'Could not send your request.'; message.classList.add('is-error'); }
    finally{ button.disabled = false; button.innerHTML = 'Send request <i data-lucide="arrow-right"></i>'; if(window.lucide){ window.lucide.createIcons({nodes:[button]}); } }
  });
})();
