</main>
<footer id="footer" class="footer position-relative light-background">
  <div class="container copyright text-center">
    <p>&copy; <?= date('Y') ?> <strong class="px-1 sitename">Municipal Scholarship Program</strong><span> All rights reserved.</span></p>
    <div class="credits">For application questions, contact your Municipal Scholarship Office.</div>
  </div>
</footer>
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" aria-label="Scroll to top"><i class="bi bi-arrow-up-short"></i></a>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<script src="<?= base_url('assets/js/custom.js') ?>"></script>
<?php if (! empty($pageScript)): ?><script src="<?= base_url($pageScript) ?>"></script><?php endif ?>
</body>
</html>
