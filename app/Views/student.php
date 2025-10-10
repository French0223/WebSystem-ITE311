<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <h3 class="mb-3 text-primary">Student Dashboard</h3>
  <p>Welcome, <?= esc($name) ?>!</p>

  <?php
    // Expecting $studentData passed from Auth::dashboard()
    $enrolledCourses  = $studentData['enrolledCourses']  ?? [];
    $availableCourses = $studentData['availableCourses'] ?? [];
  ?>

  <div id="alert-box"></div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Enrolled Courses</div>
        <div class="card-body">
          <ul class="list-group list-group-flush" id="enrolled-list">
            <?php if (empty($enrolledCourses)): ?>
              <li class="list-group-item text-muted">No enrolled courses yet.</li>
            <?php else: ?>
              <?php foreach ($enrolledCourses as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fw-semibold"><?= esc($c['title']) ?></div>
                    <small class="text-muted">Enrolled: <?= esc(date('M d, Y H:i', strtotime($c['enrollment_date']))) ?></small>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Available Courses</div>
        <div class="card-body">
          <ul class="list-group list-group-flush" id="available-list">
            <?php if (empty($availableCourses)): ?>
              <li class="list-group-item text-muted">No available courses.</li>
            <?php else: ?>
              <?php foreach ($availableCourses as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold"><?= esc($c['title']) ?></div>
                    <?php if (!empty($c['description'])): ?>
                      <small class="text-muted d-block"><?= esc($c['description']) ?></small>
                    <?php endif; ?>
                  </div>
                  <button
                    class="btn btn-sm btn-primary enroll-btn"
                    data-course-id="<?= (int) $c['id'] ?>"
                  >
                    Enroll
                  </button>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
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

<!-- jQuery (include if not already in your base template) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
  // CSRF integration (CodeIgniter 4)
  const csrfTokenName = '<?= esc(csrf_token()) ?>';
  const csrfHash      = '<?= esc(csrf_hash()) ?>';

  function showAlert(type, message) {
    $('#alert-box').html(
      '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
      '</div>'
    );
  }

  $(document).on('click', '.enroll-btn', function (e) {
    e.preventDefault();
    const btn = $(this);
    const courseId = parseInt(btn.data('course-id'), 10);

    btn.prop('disabled', true).text('Enrolling...');

    $.post('<?= base_url('/course/enroll') ?>', {
      course_id: courseId,
      [csrfTokenName]: csrfHash
    })
    .done(function (res) {
      if (res && res.status === 'ok') {
        showAlert('success', res.message || 'Enrollment successful.');

        // Move the item from available to enrolled list
        const li = btn.closest('li');
        li.find('button').remove();

        const now = new Date();
        const enrolledLi = $(
          '<li class="list-group-item d-flex justify-content-between align-items-start">' +
            '<div>' +
              '<div class="fw-semibold">' + li.find('.fw-semibold').first().text() + '</div>' +
              '<small class="text-muted">Enrolled: ' + now.toLocaleString() + '</small>' +
            '</div>' +
          '</li>'
        );
        $('#enrolled-list').append(enrolledLi);
        li.remove();
      } else {
        showAlert('warning', (res && res.message) ? res.message : 'Unable to enroll.');
        btn.prop('disabled', false).text('Enroll');
      }
    })
    .fail(function (xhr) {
      let msg = 'Request failed.';
      if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
        msg = xhr.responseJSON.message;
      }
      showAlert('danger', msg);
      btn.prop('disabled', false).text('Enroll');
    });
  });
</script>
<?= $this->endSection() ?>