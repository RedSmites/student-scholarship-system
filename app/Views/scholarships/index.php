<?php $title = 'Scholarship programs'; include APPPATH . 'Views/admin/_header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="text-uppercase small text-primary fw-bold mb-1">Administrator</p>
        <h1 class="h2 mb-1">Scholarship programs</h1>
        <p class="text-muted mb-0">Manage the programs and document requirements shown on the student application form.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url('scholarships/new') ?>">Add scholarship</a>
</div>

<?php if (empty($scholarships)): ?>
    <section class="admin-card text-center py-5">
        <h2 class="h5">No scholarship programs yet</h2>
        <p class="text-muted">Add a scholarship program to make it available on the student application form.</p>
        <a class="btn btn-primary" href="<?= site_url('scholarships/new') ?>">Add scholarship</a>
    </section>
<?php else: ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Scholarship</th>
                    <th scope="col">Deadline</th>
                    <th scope="col">Status</th>
                    <th scope="col">Student form</th>
                    <th scope="col">Applications</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scholarships as $scholarship): ?>
                    <?php
                    $isExpired = ! empty($scholarship['deadline']) && $scholarship['deadline'] < date('Y-m-d');
                    $isAvailable = $scholarship['status'] === 'open' && ! $isExpired;
                    ?>
                    <tr>
                        <td>
                            <strong><?= esc($scholarship['title']) ?></strong>
                            <div class="small text-muted"><?= esc($scholarship['provider']) ?></div>
                        </td>
                        <td><?= ! empty($scholarship['deadline']) ? esc(date('M j, Y', strtotime($scholarship['deadline']))) : 'No deadline' ?></td>
                        <td>
                            <span class="badge text-bg-<?= $isAvailable ? 'success' : 'secondary' ?>">
                                <?= $isAvailable ? 'Open to students' : ($isExpired && $scholarship['status'] === 'open' ? 'Deadline passed' : 'Closed') ?>
                            </span>
                        </td>
                        <td><?= (int) $scholarship['requirements_count'] ?> active document<?= (int) $scholarship['requirements_count'] === 1 ? '' : 's' ?></td>
                        <td><?= (int) $scholarship['applications_count'] ?></td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('scholarships/' . (int) $scholarship['id'] . '/edit') ?>">Edit</a>
                                <?php if ((int) $scholarship['applications_count'] === 0): ?>
                                    <form action="<?= site_url('scholarships/' . (int) $scholarship['id'] . '/delete') ?>" method="post" data-confirm="Delete this scholarship and its document requirements?" data-confirm-title="Delete scholarship?" data-confirm-button="Delete scholarship" data-confirm-variant="danger">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" disabled title="Close this scholarship instead to preserve its applications">Delete</button>
                                <?php endif ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>

<?php include APPPATH . 'Views/admin/_footer.php'; ?>
