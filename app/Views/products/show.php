<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#f6f1ea">
  <title>Sun Son Solar | <?= esc($product['name']) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/products.css') ?>">
</head>
<body>
  <header class="site-nav"><a class="brand" href="<?= site_url('/') ?>" aria-label="Sun Son Solar home"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a><nav aria-label="Main navigation"><a href="<?= site_url('products') ?>">All products</a><a href="<?= site_url('/') ?>#services">Services</a><a class="sign-in" href="<?= site_url('login') ?>">Sign in</a></nav></header>

  <main class="detail-shell">
    <a class="back-link" href="<?= site_url('products') ?>">&#8592; Back to products</a>
    <section class="product-detail">
      <div class="viewer-column"><div class="viewer-heading"><span class="eyebrow">Interactive product view</span><button class="viewer-control" type="button" data-reset-view>Reset view</button></div><div class="product-viewer" id="productViewer" data-product-type="<?= esc($product['category_code']) ?>" aria-label="Interactive 3D model of <?= esc($product['name']) ?>"></div><p class="viewer-note">Drag to rotate. Scroll or pinch to zoom.</p></div>
      <div class="detail-copy"><p class="eyebrow"><?= esc($product['category_name']) ?></p><h1><?= esc($product['name']) ?></h1><p class="detail-description"><?= esc($product['description']) ?></p><div class="spec-list"><p class="spec-title">Product information</p><?php foreach ($specs as $spec): ?><div><span><?= esc($spec['spec_name']) ?></span><strong><?= esc($spec['spec_value']) ?></strong></div><?php endforeach; ?><?php if ($product['warranty_years'] !== null): ?><div><span>Warranty</span><strong><?= esc((string) $product['warranty_years']) ?> years</strong></div><?php endif; ?></div><button class="quote-button" type="button" data-open-inquiry>Inquire about this product <span aria-hidden="true">&#8594;</span></button><p class="quote-note">A Sun Son Solar team member will confirm final compatibility, pricing, and availability for your project.</p></div>
    </section>
  </main>
  <footer class="site-footer"><div><a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a><p>Clear solar solutions for homes and businesses.</p></div><div><a href="tel:09291230983">09291230983</a><a href="<?= site_url('/') ?>#contact">Contact us</a></div></footer>
  <div class="inquiry-modal" id="inquiryModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="inquiryTitle">
    <div class="inquiry-dialog">
      <button class="modal-close" type="button" aria-label="Close inquiry form" data-close-inquiry>&times;</button>
      <section class="inquiry-preview"><p class="eyebrow">Your selected product</p><h2><?= esc($product['name']) ?></h2><div class="modal-product-viewer" data-product-type="<?= esc($product['category_code']) ?>" aria-label="3D product view of <?= esc($product['name']) ?>"></div><div class="inquiry-product-meta"><span><?= esc($product['category_name']) ?></span><span>Rotate and zoom to inspect</span></div></section>
      <section class="inquiry-form-panel"><p class="eyebrow">Product inquiry</p><h2 id="inquiryTitle">Tell us about your solar plan.</h2><p>Our team will use these details to prepare the right next step.</p><form id="productInquiryForm" novalidate><input type="hidden" name="productId" value="<?= esc((string) $product['id']) ?>"><label>Full name<input name="name" autocomplete="name" placeholder="Your full name"><span class="inquiry-error"></span></label><div class="inquiry-fields"><label>Email<input name="email" type="email" autocomplete="email" placeholder="you@example.com"><span class="inquiry-error"></span></label><label>Phone<input name="phone" type="tel" autocomplete="tel" placeholder="09291230983"><span class="inquiry-error"></span></label></div><label>Project address <small>(optional)</small><input name="address" autocomplete="street-address" placeholder="City, province"></label><label>What would you like to know? <small>(optional)</small><textarea name="message" maxlength="1500" placeholder="Tell us about your property, energy goals, or questions."></textarea><span class="inquiry-error"></span></label><button class="quote-button" type="submit" id="inquirySubmit">Send inquiry <span aria-hidden="true">&#8594;</span></button><p class="inquiry-message" id="inquiryMessage" role="status"></p></form></section>
    </div>
  </div>
  <script type="importmap">
    { "imports": { "three": "https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.js" } }
  </script>
  <script type="module" src="<?= base_url('assets/js/product3d.js') ?>"></script>
  <script>window.PRODUCT_INQUIRY = { endpoint: "<?= site_url('products/inquiry') ?>", csrfToken: "<?= esc($inquiryToken) ?>" };</script>
  <script src="<?= base_url('assets/js/product-inquiry.js') ?>"></script>
</body>
</html>
