<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#f6f1ea">
  <title>Sun Son Solar | Power Your Future with Solar</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
</head>
<body>
  <div class="site-loader" id="siteLoader" aria-hidden="true"><span></span></div>
  <header class="topbar">
    <a class="brand" href="<?= site_url('/') ?>" aria-label="Sun Son Solar home"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a>
    <button class="nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" data-nav-toggle><span aria-hidden="true">&#9776;</span></button>
    <nav class="main-nav" data-main-nav>
      <a href="<?= site_url('products') ?>">Products</a>
      <a href="#services">Services</a>
      <a href="#about">Why solar</a>
      <a href="#stories">Stories</a>
      <a href="#faq">FAQ</a>
      <a class="nav-login" href="<?= site_url('login') ?>">Sign in <i data-lucide="arrow-up-right"></i></a>
    </nav>
  </header>

  <main>
    <section class="home-hero" id="about">
      <div class="hero-photo" data-parallax></div>
      <div class="hero-shade"></div>
      <div class="hero-content" data-reveal>
        <p class="overline">Solar made practical</p>
        <h1>Power your future<br>with <span>solar.</span></h1>
        <p class="hero-copy">A clear path to lower energy costs, dependable power, and a more sustainable home or business.</p>
        <div class="hero-actions">
          <button class="button button-sun" type="button" data-open-modal="quote">Request a free quote <i data-lucide="arrow-right"></i></button>
          <a class="button button-ghost" href="#services">Explore services</a>
        </div>
      </div>
      <aside class="hero-proof" data-reveal>
        <i data-lucide="shield-check"></i>
        <span>From assessment to aftercare, your solar project stays in capable hands.</span>
      </aside>
    </section>

    <section class="benefits section-shell" aria-label="Solar benefits" data-reveal>
      <div><i data-lucide="badge-dollar-sign"></i><strong>Lower costs</strong><span>Generate more of your own energy.</span></div>
      <div><i data-lucide="leaf"></i><strong>Cleaner living</strong><span>Choose a more sustainable energy source.</span></div>
      <div><i data-lucide="battery-charging"></i><strong>More independence</strong><span>Build confidence in your energy plan.</span></div>
    </section>

    <section class="section-shell services" id="services">
      <div class="section-heading" data-reveal><p class="overline">What we do</p><h2>Solar support for every stage.</h2><p>Start with the right advice, then keep your system performing with a team that stays close to the work.</p></div>
      <div class="service-grid">
        <article class="service-card" data-reveal><span class="service-index">01</span><i data-lucide="message-square-more"></i><h3>Consultation</h3><p>Discuss your goals, property, and practical next steps before making a decision.</p></article>
        <article class="service-card" data-reveal><span class="service-index">02</span><i data-lucide="solar-panel"></i><h3>Installation</h3><p>Plan and install a solar setup designed around your site and energy needs.</p></article>
        <article class="service-card" data-reveal><span class="service-index">03</span><i data-lucide="wrench"></i><h3>Maintenance</h3><p>Keep performance visible through scheduled service, inspection, and repair support.</p></article>
      </div>
    </section>

    <section class="section-shell story-band" id="stories">
      <div class="story-image" data-reveal></div>
      <div class="story-copy" data-reveal><p class="overline">Built around people</p><h2>Make the switch with clarity.</h2><blockquote>“The process felt straightforward from our first consultation through installation.”</blockquote><p>Our customer experience is designed to be easy to follow: request a quote, approve your schedule, and keep track of service updates in one place.</p><button class="text-action" type="button" data-open-modal="quote">Plan your solar project <i data-lucide="arrow-up-right"></i></button></div>
    </section>

    <section class="section-shell account-band" id="login" data-reveal>
      <div><p class="overline">Your account</p><h2>Everything for your system, in one secure place.</h2></div>
      <div class="account-points"><span><i data-lucide="calendar-days"></i>Schedules and service updates</span><span><i data-lucide="bell-ring"></i>Reminders and offers you control</span><span><i data-lucide="receipt-text"></i>Service history and receipts</span></div>
      <a class="button button-dark" href="<?= site_url('login') ?>">Customer and staff sign in <i data-lucide="lock-keyhole"></i></a>
    </section>

    <section class="section-shell faq" id="faq">
      <div class="section-heading" data-reveal><p class="overline">Helpful answers</p><h2>Questions before you begin?</h2></div>
      <div class="faq-list" data-reveal>
        <details><summary>What happens after I request a quote?<i data-lucide="plus"></i></summary><p>Our team reviews your details, contacts you to discuss your needs, and confirms the best next step.</p></details>
        <details><summary>Can I track scheduled services?<i data-lucide="plus"></i></summary><p>Yes. Signed-in customers will be able to see consultation, installation, and maintenance schedules from their dashboard.</p></details>
        <details><summary>How do I get help after installation?<i data-lucide="plus"></i></summary><p>Your account will include service history and a support area for questions, concerns, and follow-up requests.</p></details>
      </div>
    </section>
  </main>

  <footer class="home-footer" id="contact">
    <div class="footer-brand"><a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a><p>Clear solar solutions for homes and businesses.</p></div>
    <div class="footer-contact"><a href="tel:09291230983">09291230983</a><a href="mailto:info@sunsonsolar.com">info@sunsonsolar.com</a></div>
    <div class="footer-legal"><span>&copy; <?= date('Y') ?> Sun Son Solar</span><a href="#">Privacy</a><a href="#">Terms</a></div>
  </footer>

  <div class="modal-layer" id="quoteModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="quoteModalTitle">
    <div class="modal-card modal-quote"><button class="modal-close" type="button" aria-label="Close quote form" data-close-modal><i data-lucide="x"></i></button><p class="overline">Start a conversation</p><h2 id="quoteModalTitle">Request a free quote</h2><p class="modal-intro">Tell us a little about your project. We will follow up to confirm your consultation.</p>
      <form id="quoteForm" novalidate>
        <div class="modal-grid"><label>Full name<input id="quoteName" name="name" autocomplete="name" placeholder="Your full name"><span class="modal-error"></span></label><label>Email<input id="quoteEmail" name="email" autocomplete="email" placeholder="you@example.com"><span class="modal-error"></span></label></div>
        <div class="modal-grid"><label>Phone<input id="quotePhone" name="phone" autocomplete="tel" placeholder="09291230983"><span class="modal-error"></span></label><label>Service<select id="quoteService" name="service"><option value="" selected disabled>Select a service</option><option value="consultation">Consultation</option><option value="installation">Installation</option><option value="maintenance">Maintenance</option></select><span class="modal-error"></span></label></div>
        <label>Service address<input id="quoteAddress" name="address" autocomplete="street-address" placeholder="City, province"><span class="modal-error"></span></label>
        <div class="modal-grid"><label>Preferred date <small>(optional)</small><input id="quotePreferredDate" name="preferredDate" type="date"><span class="modal-error"></span></label><label>Project details <small>(optional)</small><input id="quoteDetails" name="details" maxlength="1500" placeholder="Your goals or questions"><span class="modal-error"></span></label></div>
        <button class="button button-sun" type="submit" id="quoteSubmit">Send request <i data-lucide="arrow-right"></i></button><p class="form-message" id="quoteMessage" role="status"></p>
      </form>
    </div>
  </div>

  <script>window.HOME_ENDPOINTS = { csrf: "<?= site_url('quote/csrf') ?>", quote: "<?= site_url('quote') ?>" };</script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="<?= base_url('assets/js/home.js') ?>"></script>
</body>
</html>
