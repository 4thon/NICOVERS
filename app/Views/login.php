<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#f6f1ea">
  <title>Sun Son Solar | Sign In</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
</head>
<body>
  <header class="site-nav">
    <a class="brand" href="<?= site_url('/') ?>" aria-label="Sun Son Solar home"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a>
    <nav aria-label="Main navigation"><a href="<?= site_url('/') ?>#services">Services</a><a href="<?= site_url('/') ?>#about">About</a><a href="<?= site_url('register') ?>">Create account</a></nav>
  </header>

  <main class="login-page">
    <section class="welcome-panel">
      <p class="eyebrow">Sun Son Solar account</p>
      <h1>Welcome back to a brighter plan.</h1>
      <p>Use one secure sign in for your customer, employee, or administrator account.</p>
      <div class="solar-image" role="img" aria-label="Solar panels on a rooftop at sunrise"></div>
      <ul class="access-list">
        <li><span>01</span><div><strong>Customers</strong><small>View services, updates, history, and support.</small></div></li>
        <li><span>02</span><div><strong>Employees</strong><small>Access assigned work and field updates.</small></div></li>
        <li><span>03</span><div><strong>Administrators</strong><small>Manage accounts, schedules, and operations.</small></div></li>
      </ul>
    </section>

    <section class="form-panel">
      <div class="form-card">
        <p class="eyebrow">Secure access</p>
        <h2>Sign in</h2>
        <p class="form-intro">Enter your email address or username and password.</p>

        <?php if (session('login_error')): ?>
          <div class="form-message is-error" role="alert"><?= esc(session('login_error')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('login') ?>" novalidate>
          <input type="hidden" name="csrfToken" value="<?= esc($csrfToken) ?>">
          <label for="identifier">Email address or username</label>
          <input id="identifier" name="identifier" type="text" value="<?= esc(old('identifier')) ?>" autocomplete="username" required autofocus>
          <label for="password">Password</label>
          <div class="password-wrap"><input id="password" name="password" type="password" autocomplete="current-password" required><button type="button" class="show-password" aria-label="Show password" aria-pressed="false">Show</button></div>
          <button class="submit-button" type="submit">Sign in securely <span aria-hidden="true">&#8594;</span></button>
        </form>

        <p class="form-note">Customer new here? <a href="<?= site_url('register') ?>">Create your account</a></p>
        <p class="staff-note">Employee accounts are created and activated by HR or an administrator.</p>
      </div>
    </section>
  </main>

  <script>
    const password = document.querySelector('#password');
    const toggle = document.querySelector('.show-password');
    toggle.addEventListener('click', () => {
      const visible = password.type === 'text';
      password.type = visible ? 'password' : 'text';
      toggle.textContent = visible ? 'Show' : 'Hide';
      toggle.setAttribute('aria-pressed', String(!visible));
    });
  </script>
</body>
</html>
