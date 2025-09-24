<div class="container py-4">
  <h3 class="mb-3 text-primary">Student Dashboard</h3>
  <p>Welcome, <?= esc($name) ?>!</p>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Enrolled Courses</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">No enrolled courses yet.</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Upcoming Deadlines</div>
        <div class="card-body">
          <p class="text-muted mb-0">No deadlines.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mt-3">
    <div class="card-header fw-semibold">Recent Grades</div>
    <div class="card-body">
      <p class="text-muted mb-0">No grades available.</p>
    </div>
  </div>
</div>