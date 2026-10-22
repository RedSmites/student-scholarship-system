<<<<<<< HEAD
    </main>
  </div>
</div>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script>
(() => {
  const toggle = document.getElementById('admin-sidebar-toggle');
  const backdrop = document.getElementById('admin-sidebar-backdrop');
  if (!toggle || !backdrop) return;
  const mobile = window.matchMedia('(max-width: 991.98px)');
  const closeDrawer = () => document.body.classList.remove('admin-sidebar-open');
  toggle.addEventListener('click', () => {
    if (mobile.matches) {
      document.body.classList.toggle('admin-sidebar-open');
      toggle.setAttribute('aria-expanded', document.body.classList.contains('admin-sidebar-open') ? 'true' : 'false');
    } else {
      const collapsed = document.body.classList.toggle('admin-sidebar-collapsed');
      toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
      try { localStorage.setItem('scholarship-admin-sidebar-collapsed', collapsed ? '1' : '0'); } catch (error) {}
    }
  });
  backdrop.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeDrawer(); });
  mobile.addEventListener('change', closeDrawer);
  if (!mobile.matches) {
    try {
      if (localStorage.getItem('scholarship-admin-sidebar-collapsed') === '1') {
        document.body.classList.add('admin-sidebar-collapsed');
        toggle.setAttribute('aria-expanded', 'false');
      }
    } catch (error) {}
  }
})();
</script>
</body></html>
=======
</main><script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script><script src="<?= base_url('assets/js/admin.js') ?>"></script></body></html>
>>>>>>> 06a59cbe467985b9066497ce222b50e461966c1b
