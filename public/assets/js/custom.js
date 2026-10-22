document.addEventListener('DOMContentLoaded', function () {

  var yesRadio = document.getElementById('affected_yes');
  var noRadio = document.getElementById('affected_no');
  var impactSection = document.getElementById('impact-section');

  if (yesRadio && noRadio && impactSection) {
    var toggleImpactSection = function () {
      impactSection.classList.toggle('d-none', !yesRadio.checked);
    };

    yesRadio.addEventListener('change', toggleImpactSection);
    noRadio.addEventListener('change', toggleImpactSection);
    toggleImpactSection();
  }

  var otherToggles = [
    { checkbox: 'ls_other', wrapper: 'ls_other_wrap' },
    { checkbox: 'an_other', wrapper: 'an_other_wrap' }
  ];

  otherToggles.forEach(function (pair) {
    var checkbox = document.getElementById(pair.checkbox);
    var wrapper = document.getElementById(pair.wrapper);

    if (!checkbox || !wrapper) {
      return;
    }

    var toggleOtherField = function () {
      wrapper.classList.toggle('d-none', !checkbox.checked);
    };

    checkbox.addEventListener('change', toggleOtherField);
    toggleOtherField();
  });

  var consentCheckbox = document.getElementById('privacy_agreement');
  var submitButton = document.getElementById('submit-request-btn');

  if (consentCheckbox && submitButton) {
    var toggleSubmitButton = function () {
      submitButton.disabled = !consentCheckbox.checked;
    };

    consentCheckbox.addEventListener('change', toggleSubmitButton);
    toggleSubmitButton();
  }

  var assistanceForm = document.getElementById('assistance-request-form');
  var confirmModalEl = document.getElementById('confirmSubmitModal');

  if (assistanceForm && submitButton && confirmModalEl && window.bootstrap) {
    var confirmModal = new bootstrap.Modal(confirmModalEl);

    submitButton.addEventListener('click', function (e) {
      e.preventDefault();
      confirmModal.show();
    });

    var confirmYesBtn = document.getElementById('confirmSubmitYes');
    if (confirmYesBtn) {
      confirmYesBtn.addEventListener('click', function () {
        confirmModal.hide();
        assistanceForm.submit();
      });
    }
  }

  var submittedModalEl = document.getElementById('submittedModal');
  if (submittedModalEl && window.bootstrap) {
    new bootstrap.Modal(submittedModalEl).show();
  }

  var logoutTrigger = document.getElementById('logout-trigger');
  var logoutModalEl = document.getElementById('logoutConfirmModal');

  if (logoutTrigger && logoutModalEl && window.bootstrap) {
    var logoutModal = new bootstrap.Modal(logoutModalEl);

    logoutTrigger.addEventListener('click', function (e) {
      e.preventDefault();
      logoutModal.show();
    });
  }

  var submissionDetailsModalEl = document.getElementById('submissionDetailsModal');
  var detailRows = {
    reference: document.getElementById('detailReference'),
    studentId: document.getElementById('detailStudentId'),
    fullName: document.getElementById('detailFullName'),
    program: document.getElementById('detailProgram'),
    contactNumber: document.getElementById('detailContactNumber'),
    status: document.getElementById('detailStatus'),
    living: document.getElementById('detailLiving'),
    assistance: document.getElementById('detailAssistance'),
    location: document.getElementById('detailLocation'),
    description: document.getElementById('detailDescription'),
    submittedAt: document.getElementById('detailSubmittedAt')
  };

  if (submissionDetailsModalEl && window.bootstrap) {
    var submissionDetailsModal = new bootstrap.Modal(submissionDetailsModalEl);

    var showSubmissionDetails = function (row) {
      detailRows.reference.textContent = row.dataset.reference;
      detailRows.studentId.textContent = row.dataset.studentId;
      detailRows.fullName.textContent = row.dataset.fullName;
      detailRows.program.textContent = row.dataset.programLabel + ' - ' + row.dataset.yearLevel + ' (' + row.dataset.section + ')';
      detailRows.contactNumber.textContent = row.dataset.contactNumber;
      detailRows.status.textContent = row.dataset.affectedStatus;
      detailRows.living.textContent = row.dataset.livingLabel || 'N/A';
      if (row.dataset.livingOther) {
        detailRows.living.textContent += ' - Other: ' + row.dataset.livingOther;
      }
      detailRows.assistance.textContent = row.dataset.assistanceLabel || 'N/A';
      if (row.dataset.assistanceOther) {
        detailRows.assistance.textContent += ' - Other: ' + row.dataset.assistanceOther;
      }
      detailRows.location.textContent = row.dataset.locationLabel;
      detailRows.description.textContent = row.dataset.description || 'No description provided.';
      detailRows.submittedAt.textContent = row.dataset.submittedAt;
      submissionDetailsModal.show();
    };

    document.querySelectorAll('.submission-row').forEach(function (row) {
      row.addEventListener('click', function () {
        showSubmissionDetails(row);
      });
      row.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          showSubmissionDetails(row);
        }
      });
    });
  }

  var searchInput = document.getElementById('submissionSearch');
  var statusFilter = document.getElementById('submissionStatusFilter');
  var livingFilter = document.getElementById('submissionLivingFilter');
  var assistanceFilter = document.getElementById('submissionAssistanceFilter');
  var dateFilter = document.getElementById('submissionDateFilter');
  var submissionRows = document.querySelectorAll('.submission-row');
  var resultCount = document.getElementById('submissionResultCount');
  var noMatchRow = document.getElementById('noMatchRow');

  if (submissionRows.length) {
    var applySubmissionFilters = function () {
      var query = (searchInput ? searchInput.value : '').trim().toLowerCase();
      var status = statusFilter ? statusFilter.value : 'all';
      var living = livingFilter ? livingFilter.value : 'all';
      var assistance = assistanceFilter ? assistanceFilter.value : 'all';
      var date = dateFilter ? dateFilter.value : '';
      var visibleCount = 0;

      submissionRows.forEach(function (row) {
        var searchable = row.dataset.name + ' ' + row.dataset.program + ' ' + row.dataset.location;
        var matchesQuery = !query || searchable.indexOf(query) !== -1;
        var matchesStatus = status === 'all' || row.dataset.status === status;
        var matchesLiving = living === 'all' || row.dataset.living.split(',').indexOf(living) !== -1;
        var matchesAssistance = assistance === 'all' || row.dataset.assistance.split(',').indexOf(assistance) !== -1;
        var matchesDate = !date || row.dataset.date === date;
        var isVisible = matchesQuery && matchesStatus && matchesLiving && matchesAssistance && matchesDate;

        row.classList.toggle('d-none', !isVisible);
        if (isVisible) {
          visibleCount++;
        }
      });

      if (noMatchRow) {
        noMatchRow.classList.toggle('d-none', visibleCount !== 0);
      }

      if (resultCount) {
        resultCount.textContent = 'Showing ' + visibleCount + ' of ' + submissionRows.length + ' submissions';
      }
    };

    [searchInput, statusFilter, livingFilter, assistanceFilter, dateFilter].forEach(function (control) {
      if (control) {
        control.addEventListener('input', applySubmissionFilters);
        control.addEventListener('change', applySubmissionFilters);
      }
    });

    applySubmissionFilters();
  }

});
