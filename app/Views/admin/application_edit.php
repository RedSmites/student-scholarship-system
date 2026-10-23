<?php
$title = 'Edit Application #' . (int) $application['id'];
include __DIR__ . '/_header.php';
$details = $application['details'];
$oldDetails = old('details');
if (! is_array($oldDetails)) $oldDetails = [];
$skipDetails = ['csrf_test_name', 'declaration', 'email', 'mobile_number', 'scholarship_id'];
$labels = [
    'date_of_birth' => 'Date of birth',
    'place_of_birth' => 'Place of birth',
    'mailing_address' => 'Mailing address',
    'school_name' => 'Current school',
    'school_address' => 'School address',
    'intended_school' => 'Intended school',
    'intended_school_address' => 'Intended school address',
    'intended_school_type' => 'Intended school type',
    'highest_grade' => 'Highest grade completed',
    'parents_income' => 'Parents’ monthly income',
    'siblings' => 'Siblings',
];
?>
<div class="review-page">
    <a class="review-back-link" href="<?= site_url('administrator/' . (int) $application['id']) ?>"><span aria-hidden="true">&larr;</span> Back to application</a>
    <header class="review-intro">
        <div>
            <p class="review-eyebrow">Application #<?= (int) $application['id'] ?></p>
            <h1>Edit application</h1>
            <p class="review-program"><?= esc($application['scholarship_title']) ?></p>
        </div>
    </header>

    <?php $errors = session()->getFlashdata('form_errors') ?? []; ?>
    <?php if ($errors !== []): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Check the following fields:</strong>
            <ul class="mb-0 mt-2"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
        </div>
    <?php endif ?>

    <form action="<?= site_url('administrator/applications/' . (int) $application['id'] . '/edit') ?>" method="post">
        <?= csrf_field() ?>
        <section class="admin-card review-card mb-4">
            <p class="review-eyebrow">Applicant contact</p>
            <h2>Contact details</h2>
            <div class="row g-3">
                <?php if (! isset($details['first_name'], $details['last_name'])): ?>
                    <div class="col-12">
                        <label class="form-label" for="applicant-name">Applicant name</label>
                        <input class="form-control" id="applicant-name" name="applicant_name" maxlength="255" required value="<?= esc(old('applicant_name', $application['applicant_name'] ?? '')) ?>">
                    </div>
                <?php endif ?>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="application-email">Email address</label>
                    <input class="form-control" id="application-email" type="email" name="email" maxlength="190" required value="<?= esc(old('email', $application['email'] ?? '')) ?>">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="application-mobile">Mobile number</label>
                    <input class="form-control" id="application-mobile" name="mobile_number" maxlength="30" required value="<?= esc(old('mobile_number', $application['mobile_number'] ?? '')) ?>">
                </div>
            </div>
        </section>

        <section class="admin-card review-card mb-4">
            <p class="review-eyebrow">Submitted information</p>
            <h2>Application details</h2>
            <p class="text-muted small">Changes apply to the submitted form data. Uploaded files and scholarship selection are not changed here.</p>
            <div class="row g-3">
                <?php foreach ($details as $key => $value): ?>
                    <?php
                    if (in_array($key, $skipDetails, true) || ! is_scalar($value)) continue;
                    $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
                    $fieldId = 'application-detail-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) $key);
                    $fieldValue = array_key_exists($key, $oldDetails) && is_scalar($oldDetails[$key]) ? (string) $oldDetails[$key] : (string) $value;
                    $isLong = strlen($fieldValue) > 90 || str_contains($fieldValue, "\n");
                    ?>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="<?= esc($fieldId) ?>"><?= esc($label) ?></label>
                        <?php if ($isLong): ?>
                            <textarea class="form-control" id="<?= esc($fieldId) ?>" name="details[<?= esc($key) ?>]" rows="3" maxlength="5000"><?= esc($fieldValue) ?></textarea>
                        <?php else: ?>
                            <input class="form-control" id="<?= esc($fieldId) ?>" name="details[<?= esc($key) ?>]" type="<?= $key === 'date_of_birth' ? 'date' : 'text' ?>" maxlength="5000" value="<?= esc($fieldValue) ?>">
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        </section>
        <div class="d-flex justify-content-end gap-2">
            <a class="btn btn-outline-secondary" href="<?= site_url('administrator/' . (int) $application['id']) ?>">Cancel</a>
            <button class="btn btn-primary" type="submit">Save application</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/_footer.php'; ?>
