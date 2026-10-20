<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#f6f1ea">
  <title>Sun Son Solar | Products</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/products.css') ?>">
</head>
<body>
  <header class="site-nav">
    <a class="brand" href="<?= site_url('/') ?>" aria-label="Sun Son Solar home"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a>
    <nav aria-label="Main navigation"><a href="<?= site_url('/') ?>#services">Services</a><a class="active" href="<?= site_url('products') ?>">Products</a><a href="<?= site_url('/') ?>#faq">FAQ</a><a class="sign-in" href="<?= site_url('login') ?>">Sign in</a></nav>
  </header>

  <main>
    <section class="catalogue-hero">
      <div><p class="eyebrow">Sun Son Solar catalogue</p><h1>Choose the building blocks of your solar future.</h1><p>Explore the product groups used in solar system planning, then request a quote built around your property and energy goals.</p></div>
      <div class="hero-panel"><span>Panels</span><span>Inverters</span><span>Storage</span><span>Mounting</span><span>Wiring</span></div>
    </section>

    <section class="catalogue-shell" aria-labelledby="catalogueTitle">
      <div class="catalogue-heading"><div><p class="eyebrow">Product explorer</p><h2 id="catalogueTitle">Find the right component.</h2></div><p>Final specifications, pricing, and warranty terms are confirmed through a Sun Son Solar consultation.</p></div>
      <form class="catalogue-filter" method="get" action="<?= site_url('products') ?>">
        <label class="search-field"><span class="sr-only">Search products</span><input type="search" name="q" value="<?= esc($query) ?>" placeholder="Search products"></label>
        <label class="select-field"><span class="sr-only">Filter category</span><select name="category"><option value="">All categories</option><?php foreach ($categories as $category): ?><option value="<?= esc($category['code']) ?>" <?= $selectedCategory === $category['code'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option><?php endforeach; ?></select></label>
        <button type="submit">Apply filter</button>
      </form>

      <?php if ($products === []): ?>
        <section class="empty-state"><p class="eyebrow">No match yet</p><h2>Try another product or category.</h2><a href="<?= site_url('products') ?>">Clear filters</a></section>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach ($products as $product): ?>
            <article class="product-card">
              <a class="product-image" href="<?= site_url('products/' . $product['slug']) ?>" style="--product-image:url('<?= esc(base_url($product['image_path'] ?: 'assets/images/solar-rooftop-sunrise.jpg')) ?>')"><span><?= esc($product['category_name']) ?></span></a>
              <div class="product-copy"><p><?= esc($product['category_name']) ?></p><h3><a href="<?= site_url('products/' . $product['slug']) ?>"><?= esc($product['name']) ?></a></h3><p class="product-description"><?= esc($product['description']) ?></p><div class="card-footer"><span><?= $product['warranty_years'] !== null ? esc((string) $product['warranty_years']) . '-year warranty' : 'Specs on consultation' ?></span><a href="<?= site_url('products/' . $product['slug']) ?>" aria-label="View <?= esc($product['name']) ?>">View <b aria-hidden="true">&#8594;</b></a></div></div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <footer class="site-footer"><div><a class="brand" href="<?= site_url('/') ?>"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a><p>Clear solar solutions for homes and businesses.</p></div><div><a href="tel:09291230983">09291230983</a><a href="<?= site_url('/') ?>#contact">Contact us</a></div></footer>
</body>
</html>
