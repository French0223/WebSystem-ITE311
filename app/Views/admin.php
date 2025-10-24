<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <h3 class="mb-3 text-primary">Admin Dashboard</h3>
  <p>Welcome, <?= esc($name) ?>!</p>

  <div class="row g-3">
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-1">Total Users</h6>
          <div class="fs-4 fw-semibold">—</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="text-muted mb-1">Total Courses</h6>
          <div class="fs-4 fw-semibold">—</div>
        </div>
      </div>
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

  <div class="card mt-4 shadow-sm">
    <div class="card-header fw-semibold">Recent Activity</div>
    <div class="card-body">
      <p class="text-muted mb-0">No recent activity to show.</p>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
