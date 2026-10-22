
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Scholarship | Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 900px;">
    <div class="mb-4">
        <a href="<?= site_url('admin/scholarships') ?>"
           class="text-decoration-none">
            &larr; Back to Scholarships
        </a>

        <h2 class="fw-bold mt-3">Edit Scholarship</h2>
        <p class="text-secondary">
            Update the details for this scholarship record.
        </p>
    </div>

    <?php if (isset($validation) && $validation->getErrors()): ?>
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">

            <form action="<?= site_url('admin/scholarships/update/' . $scholarship['id']) ?>"
                  method="post">

                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">
                        Scholarship Title
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        maxlength="180"
                        value="<?= esc(old('title', $scholarship['title'])) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="provider" class="form-label fw-semibold">
                        Provider / Organization
                    </label>
                    <input
                        type="text"
                        id="provider"
                        name="provider"
                        class="form-control"
                        maxlength="180"
                        value="<?= esc(old('provider', $scholarship['provider'])) ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="4"
                        required
                    ><?= esc(old('description', $scholarship['description'])) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="eligibility" class="form-label fw-semibold">
                        Eligibility Requirements
                    </label>
                    <textarea
                        id="eligibility"
                        name="eligibility"
                        class="form-control"
                        rows="3"
                        required
                    ><?= esc(old('eligibility', $scholarship['eligibility'])) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="benefits" class="form-label fw-semibold">
                        Scholarship Benefits
                    </label>
                    <textarea
                        id="benefits"
                        name="benefits"
                        class="form-control"
                        rows="3"
                        required
                    ><?= esc(old('benefits', $scholarship['benefits'])) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="deadline" class="form-label fw-semibold">
                            Application Deadline
                        </label>
                        <input
                            type="date"
                            id="deadline"
                            name="deadline"
                            class="form-control"
                            value="<?= esc(old('deadline', $scholarship['deadline'] ?? '')) ?>"
                        >
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-semibold">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="form-select"
                            required
                        >
                            <option value="open"
                                <?= old('status', $scholarship['status']) === 'open' ? 'selected' : '' ?>>
                                Open
                            </option>
                            <option value="closed"
                                <?= old('status', $scholarship['status']) === 'closed' ? 'selected' : '' ?>>
                                Closed
                            </option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        Save Changes
                    </button>

                    <a href="<?= site_url('admin/scholarships') ?>"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
