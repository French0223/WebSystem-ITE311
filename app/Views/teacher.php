<div class="container py-4">
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

  <div class="card shadow-sm">
    <div class="card-header fw-semibold">New Submissions</div>
    <div class="card-body">
      <p class="text-muted mb-0">No new submissions.</p>
    </div>
  </div>
</div>