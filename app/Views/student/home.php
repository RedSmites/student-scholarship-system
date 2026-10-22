<?php $title = 'Municipal Scholarship Program'; include __DIR__ . '/_header.php'; ?>
<section id="hero" class="hero section">
  <div class="hero-bg"><img src="<?= base_url('assets/img/hero-bg-light.webp') ?>" alt=""></div>
  <div class="container text-center"><div class="d-flex flex-column justify-content-center align-items-center">
    <h1 data-aos="fade-up">Municipal <span>Scholarship Program</span></h1>
    <p data-aos="fade-up" data-aos-delay="100">Supporting local students as they pursue their educational goals.</p>
    <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="200">
      <a href="<?= site_url('requirements') ?>" class="btn-cta">View Requirements</a>
      <a href="<?= site_url('apply') ?>" class="btn-cta-outline">Start Application</a>
    </div>
    <img src="<?= base_url('assets/img/hero-services-img.webp') ?>" class="img-fluid hero-img" alt="Students preparing for their studies" data-aos="zoom-out" data-aos-delay="300">
  </div></div>
</section>
<section id="about" class="about section"><div class="container"><div class="row gy-4 align-items-center">
  <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100"><h3 class="mb-3">A clearer way to apply</h3><p>Use this portal to review the scholarship requirements, provide your personal and school information, and submit your application to the municipal scholarship office.</p><p>You can check your entries and uploaded files before confirming submission.</p><a href="<?= site_url('requirements') ?>" class="btn-cta mt-2">See the requirements</a></div>
  <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200"><img src="<?= base_url('assets/img/donation.png') ?>" class="system-photo rounded img-fluid" alt="Education and student support"></div>
</div></div></section>
<section class="section light-background"><div class="container"><div class="section-title"><h2>How to apply</h2><p>Three steps to submit your application</p></div><div class="row gy-4"><div class="col-md-4"><div class="service-item position-relative"><i class="bi bi-card-checklist"></i><h3>Review requirements</h3><p>Check what information and documents you need to prepare.</p></div></div><div class="col-md-4"><div class="service-item position-relative"><i class="bi bi-pencil-square"></i><h3>Complete the form</h3><p>Fill in your personal, school, and family background details.</p></div></div><div class="col-md-4"><div class="service-item position-relative"><i class="bi bi-send"></i><h3>Review and submit</h3><p>Confirm your answers and attached documents before submission.</p></div></div></div></div></section>
<?php include __DIR__ . '/_footer.php'; ?>
