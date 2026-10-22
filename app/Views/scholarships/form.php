<?php
$title = $formTitle;
include APPPATH . 'Views/admin/_header.php';
$oldRequirements = old('requirement_name');
if (is_array($oldRequirements)) {
    $requirementRows = [];
    $oldDescriptions = old('requirement_description', []);
    $oldRequired = old('requirement_required', []);
    $oldIds = old('requirement_id', []);
    foreach ($oldRequirements as $index => $name) {
        $requirementRows[] = [
            'id' => $oldIds[$index] ?? '',
            'requirement_name' => $name,
            'description' => $oldDescriptions[$index] ?? '',
            'is_required' => $oldRequired[$index] ?? '1',
        ];
    }
} else {
    $requirementRows = $requirements;
}
$formErrors = session()->getFlashdata('form_errors') ?? [];
?>
<div class="mb-4">
    <p class="text-uppercase small text-primary fw-bold mb-1"><a href="<?= site_url('scholarships') ?>">Scholarship programs</a></p>
    <h1 class="h2 mb-1"><?= esc($formTitle) ?></h1>
    <p class="text-muted mb-0">These details appear in the public scholarship listing and student application choices.</p>
</div>

<?php if ($formErrors): ?>
    <div class="alert alert-danger">
        <p class="fw-bold mb-1">Please correct the following:</p>
        <ul class="mb-0"><?php foreach ($formErrors as $message): ?><li><?= esc($message) ?></li><?php endforeach ?></ul>
    </div>
<?php endif ?>

<form action="<?= esc($formAction) ?>" method="post">
    <?= csrf_field() ?>
    <section class="admin-card mb-4">
        <h2 class="h5 mb-3">Scholarship information</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="scholarship-title">Scholarship name <span class="text-danger">*</span></label>
                <input class="form-control" id="scholarship-title" name="title" maxlength="180" required value="<?= esc(old('title', $scholarship['title'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="scholarship-provider">Provider <span class="text-danger">*</span></label>
                <input class="form-control" id="scholarship-provider" name="provider" maxlength="180" required value="<?= esc(old('provider', $scholarship['provider'] ?? '')) ?>">
            </div>
            <div class="col-12">
                <label class="form-label" for="scholarship-description">Description</label>
                <textarea class="form-control" id="scholarship-description" name="description" rows="3" maxlength="5000"><?= esc(old('description', $scholarship['description'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="scholarship-eligibility">Eligibility</label>
                <textarea class="form-control" id="scholarship-eligibility" name="eligibility" rows="4" maxlength="5000"><?= esc(old('eligibility', $scholarship['eligibility'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="scholarship-benefits">Benefits</label>
                <textarea class="form-control" id="scholarship-benefits" name="benefits" rows="4" maxlength="5000"><?= esc(old('benefits', $scholarship['benefits'] ?? '')) ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="scholarship-deadline">Application deadline</label>
                <input class="form-control" id="scholarship-deadline" type="date" name="deadline" value="<?= esc(old('deadline', $scholarship['deadline'] ?? '')) ?>">
                <div class="form-text">Leave blank if there is no deadline.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="scholarship-status">Availability</label>
                <?php $status = old('status', $scholarship['status'] ?? 'open'); ?>
                <select class="form-select" id="scholarship-status" name="status" required>
                    <option value="open" <?= $status === 'open' ? 'selected' : '' ?>>Open for applications</option>
                    <option value="closed" <?= $status === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
                <div class="form-text">Closed scholarships are hidden from the student application form.</div>
            </div>
        </div>
    </section>

    <section class="admin-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div>
                <h2 class="h5 mb-1">Application document requirements</h2>
                <p class="small text-muted mb-0">These are the PDF upload fields displayed for this scholarship on the student form.</p>
            </div>
            <button class="btn btn-sm btn-outline-primary" type="button" id="add-requirement">Add document</button>
        </div>
        <div id="requirement-list" class="d-grid gap-3">
            <?php foreach ($requirementRows as $index => $requirement): ?>
                <fieldset class="border rounded p-3 requirement-item">
                    <legend class="float-none w-auto px-2 small fw-bold">Document <span class="requirement-number"><?= $index + 1 ?></span></legend>
                    <input type="hidden" name="requirement_id[]" value="<?= esc((string) ($requirement['id'] ?? '')) ?>">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4">
                            <label class="form-label">Document name</label>
                            <input class="form-control" name="requirement_name[]" maxlength="150" value="<?= esc($requirement['requirement_name'] ?? '') ?>" placeholder="e.g. Certificate of enrollment">
                        </div>
                        <div class="col-lg-5">
                            <label class="form-label">Instructions for students</label>
                            <input class="form-control" name="requirement_description[]" maxlength="2000" value="<?= esc($requirement['description'] ?? '') ?>" placeholder="Optional upload guidance">
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <label class="form-label">Submission</label>
                            <?php $isRequired = (string) ($requirement['is_required'] ?? '1'); ?>
                            <select class="form-select" name="requirement_required[]">
                                <option value="1" <?= $isRequired === '1' ? 'selected' : '' ?>>Required</option>
                                <option value="0" <?= $isRequired === '0' ? 'selected' : '' ?>>Optional</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-1">
                            <button class="btn btn-outline-danger w-100 remove-requirement" type="button" aria-label="Remove document requirement">Remove</button>
                        </div>
                    </div>
                </fieldset>
            <?php endforeach ?>
        </div>
        <p class="small text-muted mt-3 mb-0">Removing a saved requirement hides it from new applications while preserving existing application records.</p>
    </section>

    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-primary" type="submit"><?= $scholarship ? 'Save changes' : 'Add scholarship' ?></button>
        <a class="btn btn-outline-secondary" href="<?= site_url('scholarships') ?>">Cancel</a>
    </div>
</form>

<template id="requirement-template">
    <fieldset class="border rounded p-3 requirement-item">
        <legend class="float-none w-auto px-2 small fw-bold">Document <span class="requirement-number"></span></legend>
        <input type="hidden" name="requirement_id[]" value="">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label">Document name</label>
                <input class="form-control" name="requirement_name[]" maxlength="150" placeholder="e.g. Certificate of enrollment">
            </div>
            <div class="col-lg-5">
                <label class="form-label">Instructions for students</label>
                <input class="form-control" name="requirement_description[]" maxlength="2000" placeholder="Optional upload guidance">
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label">Submission</label>
                <select class="form-select" name="requirement_required[]">
                    <option value="1">Required</option>
                    <option value="0">Optional</option>
                </select>
            </div>
            <div class="col-sm-6 col-lg-1">
                <button class="btn btn-outline-danger w-100 remove-requirement" type="button" aria-label="Remove document requirement">Remove</button>
            </div>
        </div>
    </fieldset>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('requirement-list');
    var template = document.getElementById('requirement-template');

    function updateRequirementNumbers() {
        list.querySelectorAll('.requirement-number').forEach(function (number, index) {
            number.textContent = String(index + 1);
        });
    }

    document.getElementById('add-requirement').addEventListener('click', function () {
        list.appendChild(template.content.cloneNode(true));
        updateRequirementNumbers();
    });

    list.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-requirement')) {
            event.target.closest('.requirement-item').remove();
            updateRequirementNumbers();
        }
    });

    updateRequirementNumbers();
});
</script>
<?php include APPPATH . 'Views/admin/_footer.php'; ?>
