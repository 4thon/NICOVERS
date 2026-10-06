  var currentRole = 'customer';

  // ---- Carousel: cards alternate front/back, content cycles through 5 slides ----
  var carouselImages = window.SOLAR_CAROUSEL_IMAGES || {};
  var slides = [
    { icon:'&#128262;', title:'Rooftop Solar Installation', caption:'Panels sized and placed for maximum sunlight capture.', image:carouselImages.rooftop },
    { icon:'&#9889;', title:'Inverter & Battery Setup', caption:'Reliable power storage for day and night use.', image:carouselImages.installation },
    { icon:'&#128295;&#65039;', title:'On-Site Technician Support', caption:'Trained technicians handle setup, checks, and repairs.', image:carouselImages.technician },
    { icon:'&#128208;', title:'Custom System Design', caption:'Layouts planned around your roof and energy needs.', image:carouselImages.rooftop },
    { icon:'&#128202;', title:'Live Production Monitoring', caption:'Track energy output and savings in real time.', image:carouselImages.installation },
    { icon:'&#128161;', title:'Energy Consultation', caption:'Clear guidance before you choose a solar system.', image:carouselImages.technician }
  ];
  var cardA = document.getElementById('cardA');
  var cardB = document.getElementById('cardB');
  var dots = document.querySelectorAll('#mediaDots span');
  var slideIndex = 1; // slide 0 is on cardA, slide 1 is on cardB already
  var aIsFront = true;

  function fillCard(card, slide){
    card.style.setProperty('--card-image', slide.image ? 'url("' + slide.image + '")' : 'none');
    card.innerHTML = '<span class="media-icon">' + slide.icon + '</span>' +
      '<span class="media-card-title">' + slide.title + '</span>' +
      '<span class="media-card-caption">' + slide.caption + '</span>';
  }

  function setDot(i){
    dots.forEach(function(d, idx){ d.classList.toggle('on', idx === (i % dots.length)); });
  }

  setInterval(function(){
    aIsFront = !aIsFront;
    var front = aIsFront ? cardA : cardB;
    var back = aIsFront ? cardB : cardA;
    front.classList.remove('is-back');
    front.classList.add('is-front');
    back.classList.remove('is-front');
    back.classList.add('is-back');
    setDot(slideIndex);
    // once the "back" card has faded out of view, refresh it with the next slide
    setTimeout(function(){
      slideIndex = (slideIndex + 1) % slides.length;
      fillCard(back, slides[slideIndex]);
    }, 900);
  }, 3200);

  // ---- Nav active indicator ----
  document.querySelectorAll('[data-nav]').forEach(function(link){
    link.addEventListener('click', function(e){
      if(link.getAttribute('href') === '#') e.preventDefault();
      document.querySelectorAll('[data-nav]').forEach(function(l){ l.classList.remove('active'); });
      link.classList.add('active');
    });
  });

  // ---- Subtle media parallax ----
  var mediaStack = document.querySelector('.media-stack');
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if(mediaStack && !reduceMotion){
    var parallaxQueued = false;
    window.addEventListener('scroll', function(){
      if(parallaxQueued) return;
      parallaxQueued = true;
      window.requestAnimationFrame(function(){
        var offset = Math.min(window.scrollY * 0.025, 16);
        mediaStack.style.transform = 'translate3d(0, ' + offset + 'px, 0)';
        parallaxQueued = false;
      });
    }, { passive: true });
  }

  // ---- Preloader ----
  var preloader = document.getElementById('pagePreloader');
  function hidePreloader(){
    if(!preloader) return;
    preloader.classList.add('is-hidden');
    window.setTimeout(function(){ preloader.remove(); }, 500);
  }
  window.addEventListener('load', function(){ window.setTimeout(hidePreloader, 250); });

  // ---- Scroll reveal ----
  var revealElements = document.querySelectorAll('[data-reveal]');
  if(!reduceMotion && 'IntersectionObserver' in window){
    revealElements.forEach(function(element){ element.classList.add('reveal-ready'); });
    var revealObserver = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold:0.12 });
    revealElements.forEach(function(element){ revealObserver.observe(element); });
  } else {
    revealElements.forEach(function(element){ element.classList.add('is-visible'); });
  }

  // ---- Role toggle ----
  var deptSection = document.getElementById('deptSection');
  var formTitle = document.getElementById('formTitle');
  var submitBtn = document.getElementById('submitBtn');
  var contactNum = document.getElementById('contactNum');
  var acctNum = document.getElementById('acctNum');

  function renumberSections(){
    if(currentRole === 'employee'){
      deptSection.classList.add('show');
      contactNum.textContent = '03';
      acctNum.textContent = '04';
    } else {
      deptSection.classList.remove('show');
      contactNum.textContent = '02';
      acctNum.textContent = '03';
    }
  }

  document.querySelectorAll('#roleToggle button').forEach(function(btn){
    btn.addEventListener('click', function(){
      document.querySelectorAll('#roleToggle button').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      currentRole = btn.getAttribute('data-role');
      formTitle.textContent = currentRole === 'employee' ? 'Employee Registration' : 'Customer Registration';
      submitBtn.textContent = currentRole === 'employee' ? 'Create employee account' : 'Create customer account';
      renumberSections();
    });
  });
  renumberSections();

  // ---- Password eye toggle ----
  document.querySelectorAll('.toggle-pass').forEach(function(btn){
    btn.addEventListener('click', function(){
      var input = document.getElementById(btn.getAttribute('data-target'));
      input.type = input.type === 'password' ? 'text' : 'password';
      var isVisible = input.type === 'text';
      btn.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
      btn.setAttribute('aria-pressed', String(isVisible));
    });
  });

  // ---- Validation ----
  function setInvalid(el, invalid){
    var group = el.closest('.input-group');
    var err = group.querySelector('.field-error');
    if(invalid){
      el.classList.add('invalid');
      if(err) err.classList.add('active');
    } else {
      el.classList.remove('invalid');
      if(err) err.classList.remove('active');
    }
  }

  function validate(shouldFocus){
    var ok = true;
    var firstInvalid = null;

    function req(id){
      var el = document.getElementById(id);
      var valid = el.value.trim().length > 0;
      setInvalid(el, !valid);
      if(!valid){ ok = false; if(!firstInvalid) firstInvalid = el; }
      return valid;
    }

    req('firstName');
    req('lastName');
    req('birthdate');
    req('gender');
    if(currentRole === 'employee') req('department');

    var email = document.getElementById('email');
    var emailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim());
    setInvalid(email, !emailValid);
    if(!emailValid){ ok = false; if(!firstInvalid) firstInvalid = email; }

    var phone = document.getElementById('phone');
    var phoneValid = /^[0-9+\-\s()]{7,}$/.test(phone.value.trim());
    setInvalid(phone, !phoneValid);
    if(!phoneValid){ ok = false; if(!firstInvalid) firstInvalid = phone; }

    req('address');

    var username = document.getElementById('username');
    var usernameValid = /^[A-Za-z0-9._-]{3,50}$/.test(username.value.trim());
    setInvalid(username, !usernameValid);
    if(!usernameValid){ ok = false; if(!firstInvalid) firstInvalid = username; }

    var pass = document.getElementById('password');
    var passValid = pass.value.length >= 8;
    setInvalid(pass, !passValid);
    if(!passValid){ ok = false; if(!firstInvalid) firstInvalid = pass; }

    var confirm = document.getElementById('confirmPassword');
    var confirmValid = confirm.value.length > 0 && confirm.value === pass.value;
    setInvalid(confirm, !confirmValid);
    if(!confirmValid){ ok = false; if(!firstInvalid) firstInvalid = confirm; }

    if(shouldFocus && firstInvalid) firstInvalid.focus();
    return ok;
  }

  // live validation as user types/leaves field
  ['firstName','lastName','birthdate','gender','department','email','phone','address','username','password','confirmPassword']
    .forEach(function(id){
      var el = document.getElementById(id);
      if(el) el.addEventListener('blur', function(){ validate(false); });
    });

  var registerEndpoints = window.REGISTER_ENDPOINTS || {
    csrf: 'register/csrf',
    submit: 'register'
  };

  document.getElementById('signupForm').addEventListener('submit', async function(e){
    e.preventDefault();
    var success = document.getElementById('formSuccess');
    success.classList.remove('show');
    success.classList.remove('form-error');
    success.textContent = '';
    if(!validate(true)) return;

    var button = document.getElementById('submitBtn');
    button.disabled = true;
    button.textContent = 'Creating account...';
    try {
      var tokenResponse = await fetch(registerEndpoints.csrf, { credentials: 'same-origin' });
      var tokenData = await tokenResponse.json();
      if(!tokenResponse.ok || !tokenData.csrfToken) throw new Error(tokenData.message || 'Could not start a secure registration session.');

      var payload = {
        firstName: document.getElementById('firstName').value.trim(),
        middleName: document.getElementById('middleName').value.trim(),
        lastName: document.getElementById('lastName').value.trim(),
        birthdate: document.getElementById('birthdate').value,
        gender: document.getElementById('gender').value,
        role: currentRole,
        department: document.getElementById('department').value,
        email: document.getElementById('email').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        address: document.getElementById('address').value.trim(),
        username: document.getElementById('username').value.trim(),
        password: document.getElementById('password').value
      };
      var response = await fetch(registerEndpoints.submit, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': tokenData.csrfToken },
        body: JSON.stringify(payload)
      });
      var result = await response.json();
      if(!response.ok){
        Object.keys(result.errors || {}).forEach(function(id){
          var field = document.getElementById(id);
          if(field) setInvalid(field, true);
        });
        throw new Error(result.message || 'Registration could not be completed.');
      }
      success.textContent = result.message;
      success.classList.add('show');
      document.getElementById('signupForm').reset();
      document.querySelectorAll('.input-field.invalid').forEach(function(field){ setInvalid(field, false); });
    } catch(error) {
      success.textContent = error.message || 'Could not connect to the registration service. Please try again.';
      success.classList.add('show');
      success.classList.add('form-error');
    } finally {
      button.disabled = false;
      button.textContent = currentRole === 'employee' ? 'Create employee account' : 'Create customer account';
    }
  });
