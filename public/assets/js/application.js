document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('application-form');
  if (!form || !window.bootstrap) return;

  const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
  const photoInput = form.querySelector('input[name="applicant_photo"]');
  const photoPreview = document.getElementById('applicant-photo-preview');
  const photoPlaceholder = document.getElementById('applicant-photo-placeholder');
  const signatureInputs = Array.from(form.querySelectorAll('input[name^="signature_"]'));
  const scholarshipSelect = document.getElementById('scholarship-select');
  const scholarshipGroups = Array.from(form.querySelectorAll('.scholarship-document-group'));
  const scholarshipHint = document.getElementById('select-scholarship-hint');
  const academicLevel = document.getElementById('academic-level');
  const schoolLevelFields = document.getElementById('school-level-fields');
  const intendedSchoolFields = document.getElementById('intended-school-fields');
  const birthDateInput = document.getElementById('date-of-birth');

  function getInlineMessage(control) {
    const validity = control.validity;
    if (validity.customError) return control.validationMessage;
    if (validity.valueMissing) return 'This field is required.';
    if (validity.typeMismatch && control.type === 'email') return control.dataset.formatMessage || 'Enter a valid email address.';
    if (validity.typeMismatch && control.type === 'url') return 'Enter a valid URL, including https://.';
    if (validity.patternMismatch) return control.dataset.formatMessage || 'Enter a value in the requested format.';
    if (validity.badInput) return 'Enter a valid value.';
    if (validity.rangeUnderflow) return 'Enter a value of at least ' + control.min + '.';
    if (validity.rangeOverflow) return 'Enter a value no greater than ' + control.max + '.';
    if (validity.stepMismatch) return 'Enter a valid amount.';
    if (validity.tooLong) return 'Use no more than ' + control.maxLength + ' characters.';
    if (validity.tooShort) return 'Enter at least ' + control.minLength + ' characters.';
    return '';
  }

  function setInlineMessage(control, message) {
    let feedback = control.parentElement.querySelector(':scope > .field-validation-message');
    if (!feedback) {
      feedback = document.createElement('small');
      feedback.className = 'field-validation-message';
      control.insertAdjacentElement('afterend', feedback);
    }
    feedback.textContent = message;
    feedback.hidden = !message;
    control.setAttribute('aria-invalid', message ? 'true' : 'false');
  }

  function validateControl(control) {
    if (control.disabled || control.type === 'hidden' || control.closest('[hidden]')) {
      setInlineMessage(control, '');
      return true;
    }
    const message = control.dataset.serverError || getInlineMessage(control);
    setInlineMessage(control, message);
    return !message;
  }

  function validateAll() {
    const controls = Array.from(form.elements).filter(control => control.willValidate);
    return controls.map(validateControl).every(Boolean);
  }

  function focusFirstInvalid() {
    const firstInvalid = form.querySelector('[aria-invalid="true"]');
    if (firstInvalid) firstInvalid.focus();
  }

  form.querySelectorAll('.digits-only, .decimal-only').forEach(function (control) {
    control.dataset.lastValidValue = control.value;
    control.addEventListener('beforeinput', function (event) {
      if (event.data === null) return;
      const start = control.selectionStart ?? control.value.length;
      const end = control.selectionEnd ?? control.value.length;
      const nextValue = control.value.slice(0, start) + event.data + control.value.slice(end);
      const valid = control.classList.contains('digits-only')
        ? /^\d*$/.test(nextValue)
        : /^\d*(?:\.\d{0,2})?$/.test(nextValue);
      if (!valid) event.preventDefault();
    });
    control.addEventListener('input', function () {
      const valid = control.classList.contains('digits-only')
        ? /^\d*$/.test(control.value)
        : /^\d*(?:\.\d{0,2})?$/.test(control.value);
      if (!valid) control.value = control.dataset.lastValidValue || '';
      else control.dataset.lastValidValue = control.value;
    });
  });

  Array.from(form.elements).filter(control => control.willValidate).forEach(function (control) {
    control.addEventListener('blur', function () { validateControl(control); });
    ['input', 'change'].forEach(function (eventName) {
      control.addEventListener(eventName, function () {
        delete control.dataset.serverError;
        if (control.parentElement.querySelector(':scope > .field-validation-message')) validateControl(control);
      });
    });
  });

  try {
    const serverErrors = JSON.parse(form.dataset.serverErrors || '{}');
    Object.entries(serverErrors).forEach(function ([name, message]) {
      const control = form.elements.namedItem(name);
      if (control instanceof Element) {
        control.dataset.serverError = message;
        validateControl(control);
      }
    });
  } catch (error) {
    // Ignore malformed server error data and continue with browser validation.
  }

  function previewImage(input, image) {
    const file = input.files[0];
    if (!file) {
      image.removeAttribute('src');
      image.classList.add('d-none');
      return;
    }
    if (image.dataset.objectUrl) URL.revokeObjectURL(image.dataset.objectUrl);
    image.dataset.objectUrl = URL.createObjectURL(file);
    image.src = image.dataset.objectUrl;
    image.classList.remove('d-none');
  }

  photoInput.addEventListener('change', function () {
    photoInput.setCustomValidity('');
    previewImage(photoInput, photoPreview);
    if (photoPlaceholder) photoPlaceholder.hidden = Boolean(photoInput.files.length);
    validateControl(photoInput);
  });
  signatureInputs.forEach(function (input) {
    input.addEventListener('change', function () {
      input.setCustomValidity('');
      const key = input.name.replace('signature_', '');
      const preview = form.querySelector('[data-signature-preview="' + key + '"]');
      const placeholder = form.querySelector('[data-signature-placeholder="' + key + '"]');
      if (preview) previewImage(input, preview);
      if (placeholder) placeholder.hidden = Boolean(input.files.length);
      validateControl(input);
    });
  });

  function updateScholarshipDocuments() {
    scholarshipGroups.forEach(function (group) {
      const active = group.dataset.scholarshipId === scholarshipSelect.value;
      group.hidden = !active;
      group.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.disabled = !active;
        if (!active) {
          input.setCustomValidity('');
          validateControl(input);
        }
      });
    });
    if (scholarshipHint) scholarshipHint.hidden = Boolean(scholarshipSelect.value);
  }
  scholarshipSelect.addEventListener('change', updateScholarshipDocuments);
  updateScholarshipDocuments();

  function updateAcademicLevelFields() {
    const hasAcademicLevel = Boolean(academicLevel.value);
    schoolLevelFields.hidden = !hasAcademicLevel;
    schoolLevelFields.querySelectorAll('input,select,textarea').forEach(function (control) {
      control.disabled = !hasAcademicLevel;
    });
    const showIntendedSchool = academicLevel.value === intendedSchoolFields.dataset.academicLevel;
    intendedSchoolFields.hidden = !showIntendedSchool;
    intendedSchoolFields.querySelectorAll('input,select,textarea').forEach(function (control) {
      control.disabled = !showIntendedSchool;
      control.required = showIntendedSchool;
    });
  }
  academicLevel.addEventListener('change', updateAcademicLevelFields);
  updateAcademicLevelFields();

  function updateAge() {
    if (!birthDateInput.value) return '';
    const parts = birthDateInput.value.split('-').map(Number);
    const today = new Date();
    let age = today.getFullYear() - parts[0];
    if (today.getMonth() + 1 < parts[1] || (today.getMonth() + 1 === parts[1] && today.getDate() < parts[2])) age--;
    return age >= 0 ? String(age) : '';
  }
  const localToday = new Date();
  birthDateInput.max = [localToday.getFullYear(), String(localToday.getMonth() + 1).padStart(2, '0'), String(localToday.getDate()).padStart(2, '0')].join('-');
  birthDateInput.addEventListener('change', updateAge);
  updateAge();

  const genericDocuments = form.querySelector('input[name="documents[]"]');
  if (genericDocuments) genericDocuments.addEventListener('change', function () {
    genericDocuments.setCustomValidity('');
    validateControl(genericDocuments);
  });

  document.getElementById('review-application').addEventListener('click', function () {
    form.classList.add('was-validated');
    if (!validateAll()) {
      focusFirstInvalid();
      return;
    }

    const photo = photoInput.files[0];
    if (!['image/jpeg', 'image/png'].includes(photo.type) || photo.size > 5 * 1024 * 1024) {
      photoInput.setCustomValidity('Applicant photo must be a JPG or PNG no larger than 5 MB.');
      validateControl(photoInput);
      photoInput.focus();
      return;
    }
    photoInput.setCustomValidity('');

    for (const input of signatureInputs) {
      const file = input.files[0];
      if (!['image/jpeg', 'image/png'].includes(file.type) || file.size > 5 * 1024 * 1024) {
        input.setCustomValidity('Signature must be a JPG or PNG no larger than 5 MB.');
        validateControl(input);
        input.focus();
        return;
      }
      input.setCustomValidity('');
    }

    const docsInputs = Array.from(form.querySelectorAll('.scholarship-document-group:not([hidden]) input[type="file"]'));
    const docs = docsInputs.flatMap(input => Array.from(input.files));
    const invalidDocumentInput = docsInputs.find(input => Array.from(input.files).some(file => file.type !== 'application/pdf' || file.size > 10 * 1024 * 1024));
    if (invalidDocumentInput) {
      invalidDocumentInput.setCustomValidity('Each requirement must be a PDF no larger than 10 MB.');
      validateControl(invalidDocumentInput);
      invalidDocumentInput.focus();
      return;
    }
    docsInputs.forEach(input => input.setCustomValidity(''));

    fillReview(photo, signatureInputs, docs);
    reviewModal.show();
  });

  function fillReview(photo, signatures, docs) {
    const summary = document.getElementById('review-details');
    summary.replaceChildren();
    const labels = new Map(Array.from(form.querySelectorAll('label')).map(function (label) {
      const control = label.querySelector('input[name],select[name],textarea[name]');
      return control ? [control.name, label.childNodes[0].textContent.trim().replace(/\s*\*$/, '')] : ['', ''];
    }));
    const seen = new Set();
    Array.from(form.elements).forEach(function (control) {
      if (!control.name || ['csrf_test_name', 'applicant_photo', 'signature_applicant', 'signature_parent', 'documents[]'].includes(control.name)) return;
      if (['hidden', 'submit', 'button', 'file', 'checkbox'].includes(control.type) || control.disabled || seen.has(control.name)) return;
      seen.add(control.name);
      const value = control.tagName === 'SELECT' ? (control.selectedOptions[0]?.textContent.trim() || '') : control.value.trim();
      if (value) addReviewRow(summary, labels.get(control.name) || control.name.replaceAll('_', ' '), value);
    });
    const age = updateAge();
    if (age) addReviewRow(summary, 'Age', age);
    addReviewRow(summary, 'Applicant photo', photo.name);
    signatures.forEach(function (input) {
      const label = input.closest('.signature-upload').querySelector('label').textContent.replace(/\s*\*$/, '');
      addReviewRow(summary, label, input.files[0].name);
    });
    docs.forEach(function (file, index) { addReviewRow(summary, 'Requirement document ' + (index + 1), file.name); });
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
    if (!validateAll()) {
      reviewModal.hide();
      focusFirstInvalid();
      return;
    }
    HTMLFormElement.prototype.submit.call(form);
  });
});
