<?php $title = 'Scholarship programs'; include APPPATH . 'Views/admin/_header.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="admin-page-eyebrow">Administrator</p>
        <h1 class="admin-page-title">Scholarship programs</h1>
        <p class="admin-page-description">Manage the programs and document requirements shown on the student application form.</p>
    </div>
    <a class="btn btn-primary admin-icon-action admin-add-action" href="<?= site_url('scholarships/new') ?>" data-tooltip="Add scholarship" aria-label="Add scholarship" title="Add scholarship"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v4h4v2h-4v4h-2v-4H7v-2h4V7h2Z"/></svg><span class="visually-hidden">Add scholarship</span></a>
</div>

<?php if (empty($scholarships)): ?>
    <section class="admin-card text-center py-5">
        <h2 class="h5">No scholarship programs yet</h2>
        <p class="text-muted">Add a scholarship program to make it available on the student application form.</p>
        <a class="btn btn-primary admin-icon-action admin-add-action" href="<?= site_url('scholarships/new') ?>" data-tooltip="Add scholarship" aria-label="Add scholarship" title="Add scholarship"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v4h4v2h-4v4h-2v-4H7v-2h4V7h2Z"/></svg><span class="visually-hidden">Add scholarship</span></a>
    </section>
<?php else: ?>
    <div class="table-responsive bg-white rounded shadow-sm admin-table-wrap">
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
                                <a class="btn btn-sm btn-outline-primary admin-icon-action" href="<?= site_url('scholarships/' . (int) $scholarship['id'] . '/edit') ?>" data-tooltip="Edit scholarship" aria-label="Edit scholarship" title="Edit scholarship"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25Zm17.7-10.05a1 1 0 0 0 0-1.41l-2.49-2.49a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.98-1.68Z"/></svg><span class="visually-hidden">Edit scholarship</span></a>
                                <?php if ((int) $scholarship['applications_count'] === 0): ?>
                                    <form class="admin-icon-tooltip" data-tooltip="Delete scholarship" action="<?= site_url('scholarships/' . (int) $scholarship['id'] . '/delete') ?>" method="post" data-confirm="Delete this scholarship and its document requirements?" data-confirm-title="Delete scholarship?" data-confirm-button="Delete scholarship" data-confirm-variant="danger">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger admin-icon-action" type="submit" data-tooltip="Delete scholarship" aria-label="Delete scholarship" title="Delete scholarship"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6 7h12l-1 13H7L6 7Zm3-3h6l1 2H8l1-2ZM4 5h16v2H4V5Z"/></svg><span class="visually-hidden">Delete scholarship</span></button>
                                    </form>
                                <?php else: ?>
                                    <span class="admin-icon-tooltip" data-tooltip="Close this scholarship instead to preserve its applications"><button class="btn btn-sm btn-outline-secondary admin-icon-action" type="button" disabled aria-label="Delete scholarship unavailable"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M6 7h12l-1 13H7L6 7Zm3-3h6l1 2H8l1-2ZM4 5h16v2H4V5Z"/></svg><span class="visually-hidden">Delete</span></button></span>
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
