<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<?php $session = session(); ?>
<div class="container py-4">
    <div class="d-flex align-items-center mb-3">
        <div class="me-2" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;background:#e9f2ff;color:#0d6efd;border-radius:8px;">
            <i class="fa-solid fa-file-arrow-up"></i>
        </div>
        <div>
            <h5 class="m-0">Upload Course Material</h5>
            <?php if (!empty($course)): ?>
                <small class="text-muted">
                    <?= esc($course['title'] ?? 'Course #' . ($courseId ?? '')) ?>
                    • Code: <?= esc($course['course_code'] ?? 'N/A') ?>
                </small>
            <?php else: ?>
                <small class="text-muted">Course #<?= esc($courseId ?? '') ?></small>
            <?php endif; ?>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php if ($session->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc($session->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <?php if ($session->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc($session->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="material" class="form-label fw-semibold">Select a file to upload</label>
                            <div class="border rounded-3 p-4 text-center bg-light-subtle" style="border-style:dashed;">
                                <input type="file" name="material" id="material" class="form-control" required>
                            </div>
                            <div class="mt-2 small">
                                <span class="badge text-bg-light border me-1">PDF</span>
                                <span class="badge text-bg-light border me-1">PPT/PPTX</span>
                                <span class="badge text-bg-light border me-1">DOC/DOCX</span>
                                <span class="badge text-bg-light border me-1">ZIP</span>
                                <span class="ms-2 text-muted">Max size: 10MB</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="<?= !empty($course) ? base_url('courses?mine=1') : base_url('dashboard') ?>" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-upload me-1"></i> Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
