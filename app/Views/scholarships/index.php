
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Available Scholarships</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('dashboard') ?>">
            Scholarship System
        </a>
        <a class="btn btn-outline-light btn-sm"
           href="<?= site_url('dashboard') ?>">
            Dashboard
        </a>
    </div>
</nav>

<main class="container py-5">
    <h1 class="h2 mb-2">Available Scholarships</h1>
    <p class="text-muted mb-4">
        Explore scholarship opportunities currently open for applications.
    </p>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($scholarships)): ?>
        <div class="alert alert-info">
            There are currently no open scholarships available.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($scholarships as $scholarship): ?>
                <?php $modalId = 'applyModal-' . (int) $scholarship['id']; ?>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">

                            <span class="badge text-bg-success align-self-start mb-2">
                                Open
                            </span>

                            <h2 class="h5">
                                <?= esc($scholarship['title']) ?>
                            </h2>

                            <p class="text-muted mb-2">
                                Provider: <?= esc($scholarship['provider']) ?>
                            </p>

                            <p>
                                <?= nl2br(esc($scholarship['description'])) ?>
                            </p>

                            <h3 class="h6">Eligibility</h3>
                            <p>
                                <?= nl2br(esc($scholarship['eligibility'])) ?>
                            </p>

                            <h3 class="h6">Benefits</h3>
                            <p>
                                <?= nl2br(esc($scholarship['benefits'])) ?>
                            </p>

                            <p class="mt-auto mb-3">
                                <strong>Deadline:</strong>
                                <?php if (! empty($scholarship['deadline'])): ?>
                                    <?= esc(date(
                                        'M j, Y',
                                        strtotime($scholarship['deadline'])
                                    )) ?>
                                <?php else: ?>
                                    No deadline specified
                                <?php endif; ?>
                            </p>

                            <button
                                type="button"
                                class="btn btn-primary w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#<?= esc($modalId) ?>">
                                <span class="me-1">+</span> Apply Now
                            </button>

                        </div>
                    </div>
                </div>

                <!-- Application popup for this scholarship -->
                <div
                    class="modal fade"
                    id="<?= esc($modalId) ?>"
                    tabindex="-1"
                    aria-labelledby="<?= esc($modalId) ?>Label"
                    aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">

                            <div class="modal-header bg-primary text-white">
                                <div>
                                    <h2
                                        class="modal-title fs-5"
                                        id="<?= esc($modalId) ?>Label">
                                        Apply for Scholarship
                                    </h2>
                                    <small>Review your application details</small>
                                </div>

                                <button
                                    type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"
                                    aria-label="Close">
                                </button>
                            </div>

                            <form
                                action="<?= site_url('applications/apply') ?>"
                                method="post">

                                <?= csrf_field() ?>

                                <div class="modal-body p-4">

                                    <input
                                        type="hidden"
                                        name="scholarship_id"
                                        value="<?= (int) $scholarship['id'] ?>">

                                    <div class="mb-3">
                                        <label class="form-label text-muted">
                                            Selected Scholarship
                                        </label>
                                        <div class="fw-semibold">
                                            <?= esc($scholarship['title']) ?>
                                        </div>
                                        <small class="text-muted">
                                            <?= esc($scholarship['provider']) ?>
                                        </small>
                                    </div>

                                    <div class="alert alert-info small">
                                        Please explain why you are applying.
                                        Your application will be linked to your
                                        logged-in student account.
                                    </div>

                                    <div class="mb-3">
                                        <label
                                            for="<?= esc($modalId) ?>-remarks"
                                            class="form-label">
                                            Application Statement
                                        </label>

                                        <textarea
                                            class="form-control"
                                            id="<?= esc($modalId) ?>-remarks"
                                            name="remarks"
                                            rows="4"
                                            maxlength="2000"
                                            placeholder="Describe your educational goals and why you are applying..."
                                            required></textarea>

                                        <div class="form-text">
                                            Maximum 2,000 characters.
                                        </div>
                                    </div>

                                    <p class="small text-muted mb-0">
                                        Demo scholarships are for system
                                        demonstration only and are not
                                        confirmation of real funding.
                                    </p>

                                </div>

                                <div class="modal-footer">
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">
                                        Cancel
                                    </button>

                                    <button
                                        type="submit"
                                        class="btn btn-primary">
                                        Submit Application
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
