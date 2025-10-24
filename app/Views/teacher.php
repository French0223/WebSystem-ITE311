<?= $this->extend('template') ?>

<?= $this->section('content') ?><div class="container py-4">
  <h3 class="mb-3 text-primary">Teacher Dashboard</h3>
  <p>Welcome, <?= esc($name) ?>!</p>

  <div class="card shadow-sm mb-3">
    <div class="card-header fw-semibold">My Courses</div>
    <div class="card-body">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">No courses yet.</li>
      </ul>
      <a href="#" class="btn btn-primary btn-sm mt-3">Create New Course</a>
    </div>
  </div>

  <div class="card mt-4 shadow-sm">
    <div class="card-header fw-semibold">Quick Upload</div>
    <div class="card-body">
      <?php $activeCourses = $activeCourses ?? []; ?>
      <?php if (empty($activeCourses)): ?>
        <div class="text-muted">No active courses available.</div>
      <?php else: ?>
        <div class="row g-2 align-items-end">
          <div class="col-md-8">
            <label class="form-label">Select Course</label>
            <select id="quickUploadCourse" class="form-select">
              <?php foreach ($activeCourses as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= esc($c['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <button type="button" class="btn btn-primary w-100"
              onclick="(function(){var id=document.getElementById('quickUploadCourse').value; window.location='<?= base_url('admin/course') ?>/'+id+'/upload';})()">
              Go to Upload
            </button>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header fw-semibold">New Submissions</div>
    <div class="card-body">
      <p class="text-muted mb-0">No new submissions.</p>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
