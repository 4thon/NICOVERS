<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
<title>Sun Son Solar | Create Account</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url('assets/css/register.css') ?>" />
</head>
<body>
  <div class="page-preloader" id="pagePreloader" role="status" aria-live="polite">
    <div class="preloader-content">
      <div class="spinner-border" aria-hidden="true"></div>
      <span>Sun Son Solar</span>
    </div>
  </div>

  <nav class="navbar">
    <div class="nav-brand">
      <div class="logo-icon" aria-hidden="true">&#9728;&#65039;</div>
      <div class="logo-text">Sun Son Solar</div>
    </div>
    <div class="nav-links" id="navLinks">
      <a href="#" data-nav>Products</a>
      <a href="#" data-nav>About</a>
      <a href="#" data-nav>Contacts</a>
      <a href="#" data-nav>Sign In</a>
    </div>
  </nav>

  <div class="hero-bar" data-reveal>
    <div class="hero-left">
      <div class="hero-icon" aria-hidden="true">&#9728;&#65039;</div>
      <div>
        <div class="hero-eyebrow">Sun Son Solar</div>
        <div class="hero-title">Create your account</div>
        <div class="hero-subtitle">Join the team powering a brighter tomorrow.</div>
      </div>
    </div>
    <div class="role-toggle" id="roleToggle">
      <button type="button" class="active" data-role="customer">Customer</button>
      <button type="button" data-role="employee">Employee</button>
    </div>
  </div>

  <main class="auth">
    <section class="auth-media" data-reveal>
      <div class="media-stack">
        <div class="media-card primary is-front" id="cardA" style="--card-image:url('<?= base_url('assets/images/solar-rooftop-sunrise.jpg') ?>');">
          <span class="media-icon" aria-hidden="true">&#128262;</span>
          <span class="media-card-title">Rooftop Solar Installation</span>
          <span class="media-card-caption">Panels sized and placed for maximum sunlight capture.</span>
        </div>
        <div class="media-card secondary is-back" id="cardB" style="--card-image:url('<?= base_url('assets/images/solar-installation.jpg') ?>');">
          <span class="media-icon" aria-hidden="true">&#9889;</span>
          <span class="media-card-title">Inverter &amp; Battery Setup</span>
          <span class="media-card-caption">Reliable power storage for day and night use.</span>
        </div>
      </div>
      <p class="media-title">Powering Homes with <span>Solar</span></p>
      <p class="media-caption">From panels to permitting, Sun Son Solar handles every step of your clean energy project.</p>
      <div class="media-dots" id="mediaDots">
        <span class="on"></span><span></span><span></span><span></span><span></span><span></span>
      </div>
    </section>

    <section>
      <form class="auth-form" id="signupForm" novalidate data-reveal>
        <h1 class="form-title" id="formTitle">Customer Registration</h1>

        <div class="form-section">
          <div class="section-header">
            <span class="section-number">01</span>
            <span class="section-title">Personal Information</span>
            <span class="section-line"></span>
          </div>
          <div class="form-row">
            <div class="input-group">
              <label class="input-label" for="firstName">First name<span class="req">*</span></label>
              <input type="text" class="input-field" id="firstName" name="firstName" autocomplete="given-name" placeholder="Katherine">
              <div class="field-error">Please enter your first name.</div>
            </div>
            <div class="input-group">
              <label class="input-label" for="middleName">Middle name</label>
              <input type="text" class="input-field" id="middleName" name="middleName" autocomplete="additional-name" placeholder="Optional">
              <div class="field-error"></div>
            </div>
            <div class="input-group">
              <label class="input-label" for="lastName">Last name<span class="req">*</span></label>
              <input type="text" class="input-field" id="lastName" name="lastName" autocomplete="family-name" placeholder="Sinagaraw">
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
              <input type="email" class="input-field" id="email" name="email" autocomplete="email" placeholder="katherine.sinagaraw@sunsonsolar.com">
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
            <textarea class="input-field" id="address" name="address" autocomplete="street-address" placeholder="Sun Son Solar office, city, province"></textarea>
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
            <input type="text" class="input-field" id="username" name="username" autocomplete="username" placeholder="katherine.sinagaraw">
            <div class="field-error">Please choose a username.</div>
          </div>
          <div class="form-row">
            <div class="input-group">
              <label class="input-label" for="password">Password<span class="req">*</span></label>
              <div class="password-field">
                <input type="password" class="input-field" id="password" name="password" autocomplete="new-password" placeholder="Create a secure password">
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
                <input type="password" class="input-field" id="confirmPassword" name="confirmPassword" autocomplete="new-password" placeholder="Confirm your password">
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

      <p class="helper-text">Already have an account? <a href="#" class="helper-link">Sign In</a></p>
    </section>
  </main>

  <footer class="site-footer" data-reveal>
    <div class="footer-brand">
      <span class="footer-mark" aria-hidden="true">&#9728;</span>
      <div>
        <strong>Sun Son Solar</strong>
        <p>Reliable solar solutions for homes and teams.</p>
      </div>
    </div>
    <div class="footer-meta">
      <a href="tel:09291230983">09291230983</a>
      <span>Clean energy, built to last.</span>
    </div>
  </footer>

  <script>
    window.REGISTER_ENDPOINTS = {
      csrf: "<?= site_url('register/csrf') ?>",
      submit: "<?= site_url('register') ?>"
    };
    window.SOLAR_CAROUSEL_IMAGES = {
      rooftop: "<?= base_url('assets/images/solar-rooftop-sunrise.jpg') ?>",
      installation: "<?= base_url('assets/images/solar-installation.jpg') ?>",
      technician: "<?= base_url('assets/images/solar-technician.jpg') ?>"
    };
  </script>
  <script src="<?= base_url('assets/js/register.js') ?>"></script>
</body>
</html>
