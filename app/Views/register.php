<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
<title>Sun Son Solar | Create Account</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url('assets/css/tokens.css') ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/css/register.css') ?>" />
</head>
<body>
  <nav class="navbar">
    <a class="nav-brand" href="#home" aria-label="Sun Son Solar home">
      <span class="logo-icon" aria-hidden="true">&#9728;</span>
      <span class="logo-text">Sun Son Solar</span>
    </a>
    <div class="nav-links" id="navLinks">
      <a href="#services" data-nav>Services</a>
      <a href="#registration" data-nav>Register</a>
      <a href="#contact" data-nav>Contact</a>
    </div>
  </nav>

  <header class="solar-hero" id="home">
    <div class="solar-hero-image" aria-hidden="true"></div>
    <div class="solar-hero-shade" aria-hidden="true"></div>
    <div class="sunrise" aria-hidden="true">
      <span class="sunrise-glow"></span>
      <span class="sunrise-orb"></span>
    </div>
    <div class="hero-content">
      <p class="hero-kicker">Cleaner power for every tomorrow</p>
      <h1>Sun Son Solar</h1>
      <p class="hero-lead">A brighter way to power homes, teams, and the future.</p>
      <a class="hero-cta" href="#registration">Create your account <span aria-hidden="true">&#8595;</span></a>
    </div>
    <div class="hero-scroll-cue" aria-hidden="true">
      <span></span>
      Scroll to register
    </div>
  </header>

  <section class="registration-intro" id="registration">
    <div class="registration-intro-copy">
      <p class="eyebrow">Get started</p>
      <h2>Your solar journey starts here.</h2>
      <p>Choose the account that fits you. Customer accounts are ready to use after registration; employee accounts are reviewed by the Sun Son Solar team.</p>
    </div>
    <div class="role-toggle" id="roleToggle" aria-label="Account type">
      <button type="button" class="active" data-role="customer">Customer</button>
      <button type="button" data-role="employee">Employee</button>
    </div>
  </section>

  <main class="auth">
    <section class="auth-media" id="services">
      <div class="media-photo">
        <img src="<?= base_url('assets/images/solar-rooftop-sunrise.jpg') ?>" alt="Solar panels installed on a rooftop at sunrise" />
        <div class="media-photo-badge"><span aria-hidden="true">&#9889;</span> Solar made simple</div>
      </div>
      <div class="media-stack">
        <div class="media-card primary is-front" id="cardA">
          <span class="media-icon" aria-hidden="true">&#128262;</span>
          <span class="media-card-title">Rooftop Solar Installation</span>
          <span class="media-card-caption">Panels sized and placed for maximum sunlight capture.</span>
        </div>
        <div class="media-card secondary is-back" id="cardB">
          <span class="media-icon" aria-hidden="true">&#9889;</span>
          <span class="media-card-title">Inverter &amp; Battery Setup</span>
          <span class="media-card-caption">Reliable power storage for day and night use.</span>
        </div>
      </div>
      <p class="media-title">Energy that feels <span>effortless.</span></p>
      <p class="media-caption">From planning and installation to aftercare, Sun Son Solar brings every step of clean energy together.</p>
      <div class="media-dots" id="mediaDots">
        <span class="on"></span><span></span><span></span><span></span><span></span>
      </div>
    </section>

    <section>
      <form class="auth-form" id="signupForm" novalidate>
        <h1 class="form-title" id="formTitle">Customer Registration</h1>
        <p class="form-intro">Complete your details below. Fields marked with <span class="req">*</span> are required.</p>

        <div class="form-section">
          <div class="section-header">
            <span class="section-number">01</span>
            <span class="section-title">Personal Information</span>
            <span class="section-line"></span>
          </div>
          <div class="form-row">
            <div class="input-group">
              <label class="input-label" for="firstName">First name<span class="req">*</span></label>
              <input type="text" class="input-field" id="firstName" name="firstName" autocomplete="given-name" placeholder="Ada">
              <div class="field-error">Please enter your first name.</div>
            </div>
            <div class="input-group">
              <label class="input-label" for="middleName">Middle name</label>
              <input type="text" class="input-field" id="middleName" name="middleName" autocomplete="additional-name" placeholder="Marie">
              <div class="field-error"></div>
            </div>
            <div class="input-group">
              <label class="input-label" for="lastName">Last name<span class="req">*</span></label>
              <input type="text" class="input-field" id="lastName" name="lastName" autocomplete="family-name" placeholder="Lovelace">
              <div class="field-error">Please enter your last name.</div>
            </div>
          </div>
          <div class="form-row">
            <div class="input-group">
              <label class="input-label" for="birthdate">Birthdate<span class="req">*</span></label>
              <input type="date" class="input-field" id="birthdate" name="birthdate" autocomplete="bday">
              <div class="field-error">Please select your birthdate.</div>
            </div>
            <div class="input-group">
              <label class="input-label" for="gender">Gender<span class="req">*</span></label>
              <select class="input-field" id="gender" name="gender">
                <option value="" selected disabled>Select gender</option>
                <option>Female</option>
                <option>Male</option>
                <option>Other</option>
              </select>
              <div class="field-error">Please select a gender.</div>
            </div>
          </div>
        </div>

        <div class="form-section dept-section" id="deptSection">
          <div class="section-header">
            <span class="section-number">02</span>
            <span class="section-title">Employment Details</span>
            <span class="section-line"></span>
          </div>
          <div class="input-group">
            <label class="input-label" for="department">Department<span class="req">*</span></label>
            <select class="input-field" id="department" name="department">
              <option value="" selected disabled>Select department</option>
              <option>Administration</option>
              <option>IT</option>
              <option>Despatch</option>
              <option>Accounting</option>
              <option>HR</option>
              <option>Marketing Sales</option>
              <option>Customer Service</option>
            </select>
            <div class="field-error">Please select a department.</div>
          </div>
        </div>

        <div class="form-section">
          <div class="section-header">
            <span class="section-number" id="contactNum">02</span>
            <span class="section-title">Contact and Location</span>
            <span class="section-line"></span>
          </div>
          <div class="form-row">
            <div class="input-group">
              <label class="input-label" for="email">Email address<span class="req">*</span></label>
              <input type="email" class="input-field" id="email" name="email" autocomplete="email" placeholder="ada@example.com">
              <div class="field-error">Please enter a valid email address.</div>
            </div>
            <div class="input-group">
              <label class="input-label" for="phone">Phone number<span class="req">*</span></label>
              <input type="tel" class="input-field" id="phone" name="phone" autocomplete="tel" placeholder="09291230983">
              <div class="field-error">Please enter a valid phone number.</div>
            </div>
          </div>
          <div class="input-group">
            <label class="input-label" for="address">Address<span class="req">*</span></label>
            <textarea class="input-field" id="address" name="address" autocomplete="street-address" placeholder="Street, city, province, postal code"></textarea>
            <div class="field-error">Please enter your address.</div>
          </div>
        </div>

        <div class="form-section">
          <div class="section-header">
            <span class="section-number" id="acctNum">03</span>
            <span class="section-title">Account Credentials</span>
            <span class="section-line"></span>
          </div>
          <div class="input-group">
            <label class="input-label" for="username">Username<span class="req">*</span></label>
            <input type="text" class="input-field" id="username" name="username" autocomplete="username" placeholder="ada.solar">
            <div class="field-error">Please choose a username.</div>
          </div>
          <div class="form-row">
            <div class="input-group">
              <label class="input-label" for="password">Password<span class="req">*</span></label>
              <div class="password-field">
                <input type="password" class="input-field" id="password" name="password" autocomplete="new-password" placeholder="At least 8 characters">
                <button type="button" class="toggle-pass" data-target="password" aria-label="Show password" aria-pressed="false">
                  <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                </button>
              </div>
              <div class="field-error">Password must be at least 8 characters.</div>
            </div>
            <div class="input-group">
              <label class="input-label" for="confirmPassword">Confirm password<span class="req">*</span></label>
              <div class="password-field">
                <input type="password" class="input-field" id="confirmPassword" name="confirmPassword" autocomplete="new-password" placeholder="Re-enter password">
                <button type="button" class="toggle-pass" data-target="confirmPassword" aria-label="Show password" aria-pressed="false">
                  <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                </button>
              </div>
              <div class="field-error">Passwords do not match.</div>
            </div>
          </div>
        </div>

        <button type="submit" class="primary-btn" id="submitBtn">Create customer account</button>
        <div class="form-success" id="formSuccess" role="status" aria-live="polite"></div>
      </form>

      <p class="helper-text" id="contact">Need help with registration? <a href="tel:09291230983" class="helper-link">Call 09291230983</a></p>
    </section>
  </main>

  <script>
    window.REGISTER_ENDPOINTS = {
      csrf: "<?= site_url('register/csrf') ?>",
      submit: "<?= site_url('register') ?>"
    };
  </script>
  <script src="<?= base_url('assets/js/register.js') ?>"></script>
</body>
</html>
