<?php $title = 'Municipal Scholarship Program'; include __DIR__ . '/_header.php'; ?>
<section id="hero" class="hero section">
  <div class="hero-bg"><img src="<?= base_url('assets/img/hero-bg-light.webp') ?>" alt=""></div>
  <div class="container text-center"><div class="d-flex flex-column justify-content-center align-items-center">
    <h1 data-aos="fade-up"><span class="hero-title-location d-block">Santa Ignacia LGU</span><span class="hero-title-program">Municipal Scholarship Program</span></h1>
    <p data-aos="fade-up" data-aos-delay="100">Supporting local students as they pursue their educational goals.</p>
    <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="200">
      <a href="<?= site_url('requirements') ?>" class="btn-cta">View Requirements</a>
      <a href="<?= site_url('apply') ?>" class="btn-cta-outline">Start Application</a>
    </div>
    <img src="<?= base_url('assets/img/hero_img.png') ?>" class="img-fluid hero-img" alt="Scholarship support for students" data-aos="zoom-out" data-aos-delay="300">
  </div></div>
</section>
<section id="about" class="about section"><div class="container"><div class="row gy-4 align-items-center">
    <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100"><h3 class="mb-3">A clearer way to apply</h3><p>We have provided a smoother way for applicants to submit their scholarship applications wherever they are located without getting hassled due to long queues in our office.
</p><p>To our dear applicants, you may click this button to look for more requirements details before submitting yours.</p><a href="<?= site_url('requirements') ?>" class="btn-cta mt-2">See the requirements</a></div>
  <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200"><img src="<?= base_url('assets/img/2nd.webp') ?>" class="system-photo rounded img-fluid" alt="Students exploring scholarship opportunities"></div>
</div></div></section>
<?php include __DIR__ . '/_footer.php'; ?>
