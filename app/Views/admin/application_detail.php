<?php
$title = 'Review Application #' . (int) $application['id'];
include __DIR__ . '/_header.php';
$statusStyles = [
    'pending' => 'warning',
    'under_review' => 'info',
    'incomplete' => 'secondary',
    'approved' => 'success',
    'rejected' => 'danger',
];
$statusStyle = $statusStyles[$application['status']] ?? 'secondary';
$detailLabels = [
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
    <a class="review-back-link" href="<?= site_url('administrator/applications') ?>">
        <span aria-hidden="true">&larr;</span> Back to applications
    </a>

    <header class="review-intro">
        <div>
            <p class="review-eyebrow">Application #<?= (int) $application['id'] ?></p>
            <h1><?= esc($application['applicant_name'] ?: 'Applicant') ?></h1>
            <p class="review-program"><?= esc($application['scholarship_title']) ?> <span aria-hidden="true">&middot;</span> <?= esc($application['scholarship_provider']) ?></p>
        </div>
        <span class="badge text-bg-<?= $statusStyle ?> review-status"><?= esc(ucwords(str_replace('_', ' ', $application['status']))) ?></span>
    </header>

    <div class="review-layout">
        <div class="review-main-column">
            <section class="admin-card review-card">
                <div class="review-card-heading">
                    <div>
                        <p class="review-eyebrow">Applicant profile</p>
                        <h2>Application information</h2>
                    </div>
                </div>
                <?php if (empty($application['details'])): ?>
                    <p class="text-muted mb-0">No additional application information is available.</p>
                <?php else: ?>
                    <dl class="review-details-grid mb-0">
                        <?php foreach ($application['details'] as $key => $value): ?>
                            <?php if (is_array($value) || is_object($value) || $key === 'csrf_test_name' || $key === 'declaration') continue ?>
                            <div class="review-detail">
                                <dt><?= esc($detailLabels[$key] ?? ucwords(str_replace('_', ' ', $key))) ?></dt>
                                <dd><?= $value !== '' ? nl2br(esc((string) $value)) : '<span class="text-muted">Not provided</span>' ?></dd>
                            </div>
                        <?php endforeach ?>
                    </dl>
                <?php endif ?>
            </section>

            <section class="admin-card review-card">
                <div class="review-card-heading">
                    <div>
                        <p class="review-eyebrow">Submitted files</p>
                        <h2>Documents &amp; attachments</h2>
                    </div>
                    <span class="review-count"><?= count($application['documents']) ?> file<?= count($application['documents']) === 1 ? '' : 's' ?></span>
                </div>
                <?php if (empty($application['documents'])): ?>
                    <div class="review-empty-state">No files were attached to this application.</div>
                <?php else: ?>
                    <div class="review-document-list">
                        <?php foreach ($application['documents'] as $document): ?>
                            <?php
                            $mimeType = strtolower((string) $document['mime_type']);
                            $isImage = in_array($mimeType, ['image/jpeg', 'image/png'], true);
                            $isPdf = $mimeType === 'application/pdf';
                            $previewUrl = site_url('administrator/document/' . (int) $document['id'] . '/preview');
                            ?>
                            <article class="review-document">
                                <div class="review-document-heading">
                                    <div class="review-file-mark" aria-hidden="true"><?= $isImage ? 'IMG' : ($isPdf ? 'PDF' : 'FILE') ?></div>
                                    <div class="review-document-meta">
                                        <h3><?= esc($document['requirement_name_snapshot']) ?></h3>
                                        <p><?= esc($document['original_filename']) ?></p>
                                        <span><?= esc(strtoupper(str_replace('image/', '', str_replace('application/', '', $mimeType)))) ?> <span aria-hidden="true">&middot;</span> <?= number_format(((int) $document['file_size']) / 1024, 0) ?> KB</span>
                                    </div>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= site_url('administrator/document/' . (int) $document['id']) ?>">Download</a>
                                </div>

                                <?php if ($isImage): ?>
                                    <a class="review-image-preview" href="<?= esc($previewUrl) ?>" target="_blank" rel="noopener" aria-label="Open image preview in a new tab">
                                        <img src="<?= esc($previewUrl) ?>" alt="Preview of <?= esc($document['requirement_name_snapshot']) ?>" loading="lazy">
                                    </a>
                                <?php elseif ($isPdf): ?>
                                    <iframe class="review-pdf-preview" src="<?= esc($previewUrl) ?>" title="PDF preview: <?= esc($document['requirement_name_snapshot']) ?>" loading="lazy"></iframe>
                                <?php else: ?>
                                    <p class="review-preview-unavailable mb-0">Preview is not available for this file type. Download the attachment to review it.</p>
                                <?php endif ?>

                                <div class="review-document-review">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="verified_documents[]" value="<?= (int) $document['id'] ?>" id="verified-<?= (int) $document['id'] ?>" form="review-form" <?= ! empty($document['admin_verified']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="verified-<?= (int) $document['id'] ?>">Document verified</label>
                                    </div>
                                    <label class="form-label" for="doc-remarks-<?= (int) $document['id'] ?>">Document notes</label>
                                    <textarea class="form-control" rows="2" name="document_remarks[<?= (int) $document['id'] ?>]" id="doc-remarks-<?= (int) $document['id'] ?>" form="review-form" placeholder="Add a note about this document"><?= esc($document['admin_remarks'] ?? '') ?></textarea>
                                </div>
                            </article>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </section>
        </div>

        <aside class="review-side-column">
            <form id="review-form" action="<?= site_url('administrator/update/' . (int) $application['id']) ?>" method="post" class="admin-card review-card review-decision">
                <?= csrf_field() ?>
                <p class="review-eyebrow">Administrator</p>
                <h2>Review decision</h2>
                <label class="form-label" for="application-status">Application status</label>
                <select class="form-select mb-3" id="application-status" name="status" required>
                    <?php foreach (['pending' => 'Pending', 'under_review' => 'Under review', 'incomplete' => 'Incomplete', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= $application['status'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach ?>
                </select>
                <label class="form-label" for="application-remarks">Notes for applicant</label>
                <textarea class="form-control mb-3" id="application-remarks" name="remarks" rows="5" maxlength="5000" placeholder="Share any next steps or feedback"><?= esc($application['remarks'] ?? '') ?></textarea>
                <button class="btn btn-primary w-100" type="submit">Save review</button>
                <p class="small text-muted mt-3 mb-0">Document verification and notes are saved with this review.</p>
            </form>

            <?php if (! empty($application['checklist'])): ?>
                <section class="admin-card review-card review-checklist">
                    <p class="review-eyebrow">Document progress</p>
                    <h2>Requirements checklist</h2>
                    <?php foreach ($application['checklist'] as $item): ?>
                        <?php $isVerified = ! empty($item['admin_verified']); ?>
                        <div class="review-checklist-item">
                            <span><?= esc($item['requirement_name_snapshot']) ?><?= ! empty($item['is_required']) ? ' <span class="text-danger">*</span>' : '' ?></span>
                            <span class="review-checklist-status <?= $isVerified ? 'is-verified' : '' ?>"><?= $isVerified ? 'Verified' : (! empty($item['student_marked_ready']) ? 'Submitted' : 'Not submitted') ?></span>
                        </div>
                    <?php endforeach ?>
                </section>
            <?php endif ?>
        </aside>
    </div>
</div>
<?php include __DIR__ . '/_footer.php'; ?>
