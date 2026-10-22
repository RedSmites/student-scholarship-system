document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('application-form');
  if (!form || !window.bootstrap) return;

  const modalElement = document.getElementById('reviewModal');
  const reviewModal = new bootstrap.Modal(modalElement);
  const signatureInput = document.getElementById('signature-image');
  const signaturePreview = document.getElementById('signature-preview');
  const fileInput = form.querySelector('input[name="applicant_photo"]');
  fileInput.addEventListener('change', function () { fileInput.setCustomValidity(''); });
  signatureInput.addEventListener('change', function () { signatureInput.setCustomValidity(''); });
  const docsField = form.querySelector('input[name="documents[]"]');
  if (docsField) docsField.addEventListener('change', function () { docsField.setCustomValidity(''); });

  const scholarshipSelect = document.getElementById('scholarship-select');
  const scholarshipGroups = Array.from(form.querySelectorAll('.scholarship-document-group'));
  const scholarshipHint = document.getElementById('select-scholarship-hint');
  function updateScholarshipDocuments() {
    scholarshipGroups.forEach(function (group) {
      const active = group.dataset.scholarshipId === scholarshipSelect.value;
      group.hidden = !active;
      group.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.disabled = !active;
        if (!active) input.setCustomValidity('');
      });
    });
    if (scholarshipHint) scholarshipHint.hidden = Boolean(scholarshipSelect.value);
  }
  scholarshipSelect.addEventListener('change', updateScholarshipDocuments);
  updateScholarshipDocuments();

  function showImagePreview(file) {
    if (!file) {
      signaturePreview.src = '';
      signaturePreview.classList.add('d-none');
      return;
    }
    signaturePreview.src = URL.createObjectURL(file);
    signaturePreview.classList.remove('d-none');
  }

  signatureInput.addEventListener('change', function () {
    showImagePreview(signatureInput.files[0]);
  });

  document.getElementById('review-application').addEventListener('click', function () {
    form.classList.add('was-validated');
    if (!form.reportValidity()) return;

    const photo = fileInput.files[0];
    const signature = signatureInput.files[0];
    const docsInputs = Array.from(form.querySelectorAll('.scholarship-document-group:not([hidden]) input[type="file"]'));
    const docs = docsInputs.flatMap(input => Array.from(input.files));
    if (photo && (!['image/jpeg', 'image/png'].includes(photo.type) || photo.size > 5 * 1024 * 1024)) {
      photoInputError('Applicant photo must be a JPG or PNG no larger than 5 MB.');
      return;
    }
    if (signature && (!['image/jpeg', 'image/png'].includes(signature.type) || signature.size > 5 * 1024 * 1024)) {
      signatureInput.setCustomValidity('Attach a JPG or PNG signature image no larger than 5 MB.');
      signatureInput.reportValidity();
      return;
    }
    signatureInput.setCustomValidity('');
    const invalidDocumentInput = docsInputs.find(input => Array.from(input.files).some(file => file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024));
    if (invalidDocumentInput) {
      invalidDocumentInput.setCustomValidity('Each supporting document must be a PDF no larger than 10 MB.');
      invalidDocumentInput.reportValidity();
      return;
    }
    docsInputs.forEach(input => input.setCustomValidity(''));

    fillReview(photo, signature, docs);
    reviewModal.show();
  });

  function photoInputError(message) {
    fileInput.setCustomValidity(message);
    fileInput.reportValidity();
  }

  function fillReview(photo, signature, docs) {
    const summary = document.getElementById('review-details');
    summary.replaceChildren();
    const labels = new Map(Array.from(form.querySelectorAll('label')).map(function (label) {
      const control = label.querySelector('input[name],select[name],textarea[name]');
      return control ? [control.name, label.childNodes[0].textContent.trim().replace(/\s*\*$/, '')] : ['', ''];
    }));
    const seen = new Set();
    Array.from(form.elements).forEach(function (control) {
      if (!control.name || ['csrf_test_name', 'signature_image', 'documents[]', 'applicant_photo'].includes(control.name)) return;
      if (['hidden', 'submit', 'button', 'file', 'checkbox'].includes(control.type) || seen.has(control.name)) return;
      seen.add(control.name);
      const value = control.tagName === 'SELECT'
        ? (control.selectedOptions[0]?.textContent.trim() || '')
        : control.value.trim();
      if (!value) return;
      addReviewRow(summary, labels.get(control.name) || control.name.replaceAll('_', ' '), value);
    });
    addReviewRow(summary, 'Applicant photo', photo.name);
    addReviewRow(summary, 'Signature image', signature.name);
    docs.forEach(function (file, index) { addReviewRow(summary, 'Supporting document ' + (index + 1), file.name); });
  }

  function addReviewRow(parent, label, value) {
    const row = document.createElement('div');
    row.className = 'review-detail';
    const key = document.createElement('b');
    key.textContent = label;
    const text = document.createElement('span');
    text.textContent = value;
    row.append(key, text);
    parent.append(row);
  }

  document.getElementById('confirm-submit').addEventListener('click', function () {
    if (!form.reportValidity()) {
      reviewModal.hide();
      return;
    }
    HTMLFormElement.prototype.submit.call(form);
  });
});
