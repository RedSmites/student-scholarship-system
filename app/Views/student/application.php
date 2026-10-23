<?php
$title = 'Scholarship Application';
include __DIR__ . '/_header.php';
$old = $old ?? [];
$v = static fn (string $key): string => esc($old[$key] ?? '');
$errors = session()->getFlashdata('form_errors') ?? [];
$pageScript = 'assets/js/application.js';
$selectedScholarshipId = (int) ($selectedScholarshipId ?? 0);
$barangays = [
    'Baldios', 'Botbotones', 'Caanamongan', 'Cabaruan', 'Cabugbugan', 'Caduldulaoan',
    'Calipayan', 'Macaguing', 'Nambalan', 'Padapada', 'Pilpila', 'Pinpinas',
    'Poblacion East (Urban)', 'Poblacion West (Urban)', 'Pugo-Cecilio', 'San Francisco',
    'San Sotero', 'San Vicente', 'Santa Ines Centro', 'Santa Ines East', 'Santa Ines West',
    'Taguiporo', 'Timmaguab', 'Vargas',
];
?>
<div class="page-title text-center" data-aos="fade"><div class="container"><h1 class="mb-2">Scholarship Application Form</h1></div></div>
<section class="application-section"><div class="container application-wrap">
<?php if (session()->getFlashdata('error')): ?><div class="form-alert"><?= esc(session()->getFlashdata('error')) ?></div><?php endif ?>
<form id="application-form" action="<?= site_url('apply') ?>" method="post" enctype="multipart/form-data" novalidate data-server-errors="<?= esc(json_encode($errors), 'attr') ?>">
<?= csrf_field() ?>
<section class="form-card scholarship-selection"><div class="form-card-title"><i class="bi bi-award"></i><div><h2>Select a scholarship</h2><p>Choose a program to load its current document requirements.</p></div></div><label>Scholarship program *<select name="scholarship_id" id="scholarship-select" required><option value="">Select an open scholarship</option><?php foreach (($scholarships ?? []) as $scholarship): ?><option value="<?= (int) $scholarship['id'] ?>" <?= $selectedScholarshipId === (int) $scholarship['id'] ? 'selected' : '' ?>><?= esc($scholarship['title']) ?> — <?= esc($scholarship['provider']) ?></option><?php endforeach ?></select></label><?php if (empty($scholarships)): ?><p class="text-danger small mt-2 mb-0">No open scholarships are currently available.</p><?php endif ?></section>

<section class="form-card"><div class="form-card-title"><i class="bi bi-person-vcard"></i><div><h2>Personal information</h2><p>Student information and contact details.</p></div></div>
<div class="personal-details-layout">
  <div class="field-grid personal-fields-grid">
    <label>Last name *<input name="last_name" value="<?= $v('last_name') ?>" maxlength="100" required></label>
    <label>First name *<input name="first_name" value="<?= $v('first_name') ?>" maxlength="100" required></label>
    <label>Middle name<input name="middle_name" value="<?= $v('middle_name') ?>" maxlength="100"></label>
    <label>Sex *<select name="sex" required><option value="">Select</option><?php foreach (['Male','Female'] as $option): ?><option value="<?= $option ?>" <?= ($old['sex'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach ?></select></label>
    <label>Date of birth *<input id="date-of-birth" type="date" name="date_of_birth" value="<?= $v('date_of_birth') ?>" required></label>
    <label>Place of birth *<input name="place_of_birth" value="<?= $v('place_of_birth') ?>" maxlength="150" required></label>
    <label>Civil status *<select name="civil_status" required><option value="">Select</option><?php foreach (['Single','Married','Widowed','Separated'] as $option): ?><option value="<?= $option ?>" <?= ($old['civil_status'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach ?></select></label>
    <label>Citizenship *<input name="citizenship" value="<?= $v('citizenship') ?>" maxlength="100" required></label>
    <label>Religion *<input name="religion" value="<?= $v('religion') ?>" maxlength="100" required></label>
  </div>
  <div class="photo-upload">
    <label for="applicant-photo">Applicant photo *</label>
    <div class="photo-frame"><div id="applicant-photo-placeholder" class="photo-placeholder"><i class="bi bi-person-square"></i><span>Image preview</span></div><img id="applicant-photo-preview" class="photo-preview d-none" alt="Applicant photo preview"></div>
    <input id="applicant-photo" type="file" name="applicant_photo" accept="image/png,image/jpeg" required>
    <small>JPG or PNG, maximum 5 MB.</small>
  </div>
</div>

<div class="address-subsection"><h3>Address</h3><div class="field-grid three address-fields-grid">
  <label>Barangay *<select name="barangay" required><option value="">Select barangay</option><?php foreach ($barangays as $barangay): ?><option value="<?= esc($barangay) ?>" <?= ($old['barangay'] ?? '') === $barangay ? 'selected' : '' ?>><?= esc($barangay) ?></option><?php endforeach ?></select></label>
  <label>Street *<input name="street" value="<?= $v('street') ?>" maxlength="150" required></label>
  <label>Unit No. / House No.<input name="house_number" value="<?= $v('house_number') ?>" maxlength="80"></label>
</div></div>
<div class="contact-information-heading"><h3>Contact information</h3></div>
<div class="field-grid three contact-fields-grid">
  <label>Contact number *<input type="tel" class="digits-only" inputmode="numeric" name="mobile_number" value="<?= $v('mobile_number') ?>" maxlength="11" required pattern="09[0-9]{9}" data-format-message="Enter an 11-digit contact number starting with 09."></label>
  <label>Email address *<input type="email" name="email" value="<?= $v('email') ?>" maxlength="190" required data-format-message="Enter an email address in name@example.com format."></label>
  <label>Facebook account link<input type="url" name="facebook_link" value="<?= $v('facebook_link') ?>" maxlength="255"></label>
</div></section>

<section class="form-card"><div class="form-card-title"><i class="bi bi-mortarboard"></i><div><h2>School information</h2><p>Enter the applicant's current school and enrollment details.</p></div></div>
  <label class="school-level-select">Academic level *<select id="academic-level" name="academic_level" required><option value="">Select</option><option value="Senior High" <?= ($old['academic_level'] ?? '') === 'Senior High' ? 'selected' : '' ?>>Senior high</option><option value="College Level" <?= ($old['academic_level'] ?? '') === 'College Level' ? 'selected' : '' ?>>College level</option></select></label>
  <div id="school-level-fields" class="field-grid two school-level-fields" hidden>
  <label>Current school name *<input name="school_name" value="<?= $v('school_name') ?>" maxlength="180" required></label>
  <label>School type *<select name="school_type" required><option value="">Select</option><?php foreach (['Public','Private'] as $option): ?><option value="<?= $option ?>" <?= ($old['school_type'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach ?></select></label>
  <div class="field-grid four school-address-grid field-wide">
    <label>Street *<input name="school_street" value="<?= $v('school_street') ?>" maxlength="150" required></label>
    <label>Barangay *<input name="school_barangay" value="<?= $v('school_barangay') ?>" maxlength="120" required></label>
    <label>Municipality *<input name="school_municipality" value="<?= $v('school_municipality') ?>" maxlength="120" required></label>
    <label>Province *<input name="school_province" value="<?= $v('school_province') ?>" maxlength="120" required></label>
  </div>
  <div class="field-grid two conditional-school-fields" id="intended-school-fields" data-academic-level="Senior High">
    <label>School intended to enroll in *<input name="intended_school" value="<?= $v('intended_school') ?>" maxlength="180" required></label>
    <label>Type of intended school *<select name="intended_school_type" required><option value="">Select</option><?php foreach (['Public','Private'] as $option): ?><option value="<?= $option ?>" <?= ($old['intended_school_type'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach ?></select></label>
    <div class="field-grid four school-address-grid field-wide intended-address-fields">
      <label>Street *<input name="intended_school_street" value="<?= $v('intended_school_street') ?>" maxlength="150" required></label>
      <label>Barangay *<input name="intended_school_barangay" value="<?= $v('intended_school_barangay') ?>" maxlength="120" required></label>
      <label>Municipality *<input name="intended_school_municipality" value="<?= $v('intended_school_municipality') ?>" maxlength="120" required></label>
      <label>Province *<input name="intended_school_province" value="<?= $v('intended_school_province') ?>" maxlength="120" required></label>
    </div>
  </div>
  <label class="field-wide">Course / program *<input name="course" value="<?= $v('course') ?>" maxlength="180" required></label>
  </div>
</section>

<section class="form-card"><div class="form-card-title"><i class="bi bi-people"></i><div><h2>Family information</h2><p>Provide parent and guardian information.</p></div></div><div class="field-grid two family-information-grid">
<?php foreach (['father'=>'Father','mother'=>'Mother'] as $key => $label): ?><div class="parent-block"><h3><?= $label ?></h3><label><?= $label ?> status *<select name="<?= $key ?>_status" required><option value="">Select</option><?php foreach (['Living','Deceased'] as $option): ?><option value="<?= $option ?>" <?= ($old[$key . '_status'] ?? '') === $option ? 'selected' : '' ?>><?= $option ?></option><?php endforeach ?></select></label><div class="field-grid three family-name-grid"><?php foreach (['last_name'=>'Last name','first_name'=>'First name','middle_name'=>'Middle name'] as $part => $partLabel): $nameRequired = in_array($part, ['last_name', 'first_name'], true); ?><label><?= $partLabel ?><?= $nameRequired ? ' *' : '' ?><input name="<?= $key ?>_<?= $part ?>" value="<?= $v($key . '_' . $part) ?>" maxlength="100" <?= $nameRequired ? 'required' : '' ?>></label><?php endforeach ?></div><label>Occupation *<input name="<?= $key ?>_occupation" value="<?= $v($key . '_occupation') ?>" maxlength="150" required></label><label>Educational attainment *<input name="<?= $key ?>_education" value="<?= $v($key . '_education') ?>" maxlength="150" required></label><label>Contact number<input type="tel" class="digits-only" inputmode="numeric" name="<?= $key ?>_contact_number" value="<?= $v($key . '_contact_number') ?>" maxlength="15" pattern="[0-9]{7,15}" data-format-message="Enter 7–15 digits only."></label><div class="field-grid two family-address-grid"><label>House no.<input name="<?= $key ?>_house_number" value="<?= $v($key . '_house_number') ?>" maxlength="80"></label><label>Street *<input name="<?= $key ?>_street" value="<?= $v($key . '_street') ?>" maxlength="150" required></label><label>Barangay *<input name="<?= $key ?>_barangay" value="<?= $v($key . '_barangay') ?>" maxlength="120" required></label><label>Municipality *<input name="<?= $key ?>_municipality" value="<?= $v($key . '_municipality') ?>" maxlength="120" required></label><label>Province *<input name="<?= $key ?>_province" value="<?= $v($key . '_province') ?>" maxlength="120" required></label></div></div><?php endforeach ?>
  <label>Total parents' gross monthly income (₱) *<input type="text" class="decimal-only" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?" data-format-message="Enter a non-negative amount with up to 2 decimal places." name="parents_income" value="<?= $v('parents_income') ?>" required></label>
  <div class="parent-block guardian-block field-wide"><h3>Guardian</h3><div class="field-grid three family-name-grid"><?php foreach (['last_name'=>'Last name','first_name'=>'First name','middle_name'=>'Middle name'] as $part => $partLabel): ?><label><?= $partLabel ?><input name="guardian_<?= $part ?>" value="<?= $v('guardian_' . $part) ?>" maxlength="100"></label><?php endforeach ?></div><div class="field-grid two guardian-details-grid"><label>Occupation<input name="guardian_occupation" value="<?= $v('guardian_occupation') ?>" maxlength="150"></label><label>Educational attainment<input name="guardian_education" value="<?= $v('guardian_education') ?>" maxlength="150"></label><label>Contact number<input type="tel" class="digits-only" inputmode="numeric" name="guardian_contact_number" value="<?= $v('guardian_contact_number') ?>" maxlength="15" pattern="[0-9]{7,15}" data-format-message="Enter 7–15 digits only."></label></div><h4 class="guardian-address-heading">Address</h4><div class="field-grid four guardian-address-grid"><label>House no.<input name="guardian_house_number" value="<?= $v('guardian_house_number') ?>" maxlength="80"></label><label>Street<input name="guardian_street" value="<?= $v('guardian_street') ?>" maxlength="150"></label><label>Barangay<input name="guardian_barangay" value="<?= $v('guardian_barangay') ?>" maxlength="120"></label><label>Municipality<input name="guardian_municipality" value="<?= $v('guardian_municipality') ?>" maxlength="120"></label><label>Province<input name="guardian_province" value="<?= $v('guardian_province') ?>" maxlength="120"></label></div></div>
  <label class="guardian-income-field">Guardian monthly income<input type="text" class="decimal-only" inputmode="decimal" pattern="[0-9]+([.][0-9]{1,2})?" data-format-message="Enter a non-negative amount with up to 2 decimal places." name="guardian_monthly_income" value="<?= $v('guardian_monthly_income') ?>"></label>
</div></section>

<section class="form-card"><div class="form-card-title"><i class="bi bi-file-earmark-arrow-up"></i><div><h2>Requirements</h2><p>Upload the PDF documents required for the selected scholarship.</p></div></div>
<?php foreach (($scholarships ?? []) as $scholarship): $scholarshipId = (int) $scholarship['id']; $scholarshipRequirements = $requirementsByScholarship[$scholarshipId] ?? []; ?>
  <div class="scholarship-document-group" data-scholarship-id="<?= $scholarshipId ?>" hidden>
  <?php if ($scholarshipRequirements): foreach ($scholarshipRequirements as $requirement): $fieldName = 'requirement_document_' . (int) $requirement['id']; ?>
    <label class="document-upload-row"><span class="document-requirement-name"><?= esc($requirement['requirement_name']) ?><?= ! empty($requirement['is_required']) ? ' *' : ' (optional)' ?></span><span class="document-file-control">Choose file<input type="file" name="<?= esc($fieldName) ?>" accept="application/pdf,.pdf" <?= ! empty($requirement['is_required']) ? 'required' : '' ?>><small><?= esc($requirement['description'] ?? '') ?> PDF only; maximum 10 MB.</small></span></label>
  <?php endforeach; else: ?><label class="document-upload-row"><span class="document-requirement-name">Supporting document *</span><span class="document-file-control">Choose file<input type="file" name="documents[]" accept="application/pdf,.pdf" multiple required><small>Attach at least one PDF. Maximum 10 MB per file.</small></span></label><?php endif ?>
  </div>
<?php endforeach ?>
<p id="select-scholarship-hint" class="small text-muted mb-0" <?= $selectedScholarshipId ? 'hidden' : '' ?>>Select a scholarship above to see its requirements.</p></section>

<section class="form-card"><div class="form-card-title"><i class="bi bi-shield-check"></i><div><h2>Declaration &amp; signature</h2><p>Read the declaration carefully and attach all required signature images.</p></div></div>
<div class="declaration-copy">I hereby certify that the foregoing statements are true and correct. Any misinformation or withholding of information will automatically disqualify me from the Municipal Scholarship Program. I am willing to refund the financial benefits received if such information is discovered after acceptance of the award.<br><br>I hereby affirm my right to be informed, object to processing, access and rectify, suspend or withdraw my personal data and be indemnified in case of damages pursuant to the provisions of Republic Act No. 10173, the Data Privacy Act of 2012, and its corresponding Implementing Rules and Regulations.</div>
<label class="consent"><input type="checkbox" name="declaration" value="yes" required <?= ($old['declaration'] ?? '') === 'yes' ? 'checked' : '' ?>> I certify that the information above is true and agree to the declaration. *</label>
<div class="signature-grid">
<?php foreach (['applicant'=>'Applicant','parent'=>'Parent'] as $key => $label): ?><div class="signature-upload"><label for="signature-<?= $key ?>"><?= $label ?> signature *</label><div class="signature-frame"><div class="signature-placeholder" data-signature-placeholder="<?= $key ?>"><i class="bi bi-pen"></i><span>Signature preview</span></div><img class="signature-preview d-none" data-signature-preview="<?= $key ?>" alt="<?= esc($label) ?> signature preview"></div><input id="signature-<?= $key ?>" type="file" name="signature_<?= $key ?>" accept="image/png,image/jpeg,.png,.jpg,.jpeg" required><small>JPG or PNG only, maximum 5 MB.</small></div><?php endforeach ?>
</div></section>
<div class="submit-row"><button type="button" class="btn-cta" id="review-application">Review application <span>→</span></button></div>
</form>
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="review-title" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content review-modal"><div class="modal-body"><div class="review-icon">✓</div><h2 id="review-title">Review your application</h2><p>Check your information and uploaded files. Confirm when everything is correct.</p><div id="review-details" class="review-details"></div></div><div class="modal-footer"><button type="button" class="btn-cta-outline" data-bs-dismiss="modal">Go back and edit</button><button type="button" class="btn-cta" id="confirm-submit">Confirm &amp; submit <span>→</span></button></div></div></div></div>
</div></section>
<?php include __DIR__ . '/_footer.php'; ?>
