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
      <div class="card shadow-sm h-100 border-0">
        <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
          <span><i class="fa-solid fa-graduation-cap text-primary me-2"></i>Enrolled Courses</span>
          <span class="badge text-bg-light border"><?= count($enrolledCourses ?? []) ?></span>
        </div>
        <div class="card-body" style="max-height:380px; overflow:auto;">
          <ul class="list-group list-group-flush" id="enrolled-list">
            <?php if (empty($enrolledCourses)): ?>
              <li class="list-group-item text-center py-4" id="enrolled-empty">
                <div class="text-muted"><i class="fa-regular fa-folder-open me-1"></i>No enrolled courses yet.</div>
              </li>
            <?php else: ?>
              <?php foreach ($enrolledCourses as $c): ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold"><?= esc($c['title']) ?></div>
                      <small class="text-muted">Enrolled: <?= esc(date('M d, Y H:i', strtotime($c['enrollment_date']))) ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#m-<?= (int) $c['id'] ?>">
                      <i class="fa-regular fa-folder-open me-1"></i>Materials
                    </button>
                  </div>

                  <?php $cid = (int) $c['id']; $materials = $studentData['materialsByCourse'][$cid] ?? []; ?>
                  <div id="m-<?= (int) $c['id'] ?>" class="collapse mt-2">
                    <?php if (!empty($materials)): ?>
                      <div class="small fw-semibold text-uppercase text-muted">Materials</div>
                      <ul class="list-unstyled mb-0">
                        <?php foreach ($materials as $m): ?>
                          <li class="d-flex justify-content-between align-items-center py-1 border-top">
                            <span><?= esc($m['file_name']) ?></span>
                            <a class="btn btn-sm btn-outline-primary" href="<?= base_url('materials/download/' . (int) $m['id']) ?>">Download</a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php else: ?>
                      <div class="text-muted small"><i class="fa-regular fa-file-lines me-1"></i>No materials yet.</div>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
          <span><i class="fa-solid fa-book-open text-primary me-2"></i>Available Courses</span>
          <span class="badge text-bg-light border"><?= count($availableCourses ?? []) ?></span>
        </div>
        <div class="card-body" style="max-height:380px; overflow:auto;">
          <ul class="list-group list-group-flush" id="available-list">
            <?php if (empty($availableCourses)): ?>
              <li class="list-group-item text-center py-4">
                <div class="text-muted"><i class="fa-regular fa-circle-check me-1"></i>No available courses.</div>
              </li>
            <?php else: ?>
              <?php foreach ($availableCourses as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold"><?= esc($c['title']) ?></div>
                    <?php if (!empty($c['description'])): ?>
                      <small class="text-muted d-block"><?= esc($c['description']) ?></small>
                    <?php endif; ?>
                  </div>
                  <button class="btn btn-sm btn-primary enroll-btn" data-course-id="<?= (int) $c['id'] ?>">
                    <i class="fa-solid fa-plus me-1"></i>Enroll
                  </button>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- jQuery (student page specific) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
  // CSRF integration
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
        // Remove placeholder if this is the first enrolled item
        $('#enrolled-empty').remove();
        $('#enrolled-list').append(enrolledLi);
        li.remove();
        // Refresh to fetch latest materials for all courses
        window.location.reload();
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