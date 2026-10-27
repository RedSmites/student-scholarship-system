</main>
<footer id="footer" class="footer position-relative light-background">
  <div class="container copyright text-center">
    <p>&copy; <?= date('Y') ?> <strong class="px-1 sitename">Municipal Scholarship Program</strong><span> All rights reserved.</span></p>
    <div class="credits">For application questions, contact your Municipal Scholarship Office.</div>
  </div>
</footer>
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" aria-label="Scroll to top"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 19V5m0 0L5.5 11.5M12 5l6.5 6.5"/></svg></a>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/main.js') ?>?v=<?= substr(md5_file(FCPATH . 'assets/js/main.js'), 0, 8) ?>"></script>
<script src="<?= base_url('assets/js/custom.js') ?>"></script>
<?php if (! empty($pageScript)): ?><script src="<?= base_url($pageScript) ?>"></script><?php endif ?>
</body>
</html>
