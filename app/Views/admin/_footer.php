    </main>
  </div>
</div>
<div class="modal fade admin-action-modal" id="admin-action-modal" tabindex="-1" aria-labelledby="admin-action-modal-title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="admin-action-modal-icon" id="admin-action-modal-icon" aria-hidden="true">?</div>
        <h2 class="admin-action-modal-title" id="admin-action-modal-title">Confirm action</h2>
        <p class="admin-action-modal-message" id="admin-action-modal-message">Are you sure you want to continue?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="admin-action-modal-confirm">Confirm</button>
      </div>
    </div>
  </div>
</div>
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/admin.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/admin.js') ?>"></script>
</body></html>
