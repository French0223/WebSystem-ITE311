<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<?php
  /** @var bool  $canManageCourses */
  /** @var array $courseStatuses */
  $session         = session();
  $courseErrors    = (array) ($session->getFlashdata('course_errors') ?? []);
  $courseOldInput  = (array) ($session->getFlashdata('_ci_old_input') ?? []);
  $courseModalOpen = (bool) ($session->getFlashdata('course_modal_open') ?? false);
  $currentUserId   = (int) ($session->get('user_id') ?? 0);
  $currentRole     = (string) ($session->get('role') ?? '');
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
  <div>
    <h1 class="h3 text-primary mb-1">Browse Courses</h1>
    <p class="text-muted mb-0">Search the catalog or filter instantly to find the right class.</p>
  </div>
  <?php if (!empty($canManageCourses)): ?>
    <button class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createCourseModal">
      <i class="fa-solid fa-circle-plus"></i>
      <span>Add Course</span>
    </button>
  <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>


<div class="card border-0 shadow-sm mb-4">
  <div class="card-body">
    <form id="searchForm" class="row g-3 align-items-center">
      <div class="col-lg-8">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
          <input type="text" class="form-control" id="searchInput" name="search_term" placeholder="Search course title or description" value="<?= esc($searchTerm ?? '') ?>" autocomplete="off">
        </div>
      </div>
      <?php if (!empty($canManageCourses)): ?>
        <div class="col-lg-2 d-flex align-items-center">
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="mineToggle" name="mine" value="1" <?= !empty($mineOnly) ? 'checked' : '' ?>>
            <label class="form-check-label" for="mineToggle">My Courses</label>
          </div>
        </div>
      <?php endif; ?>
      <div class="col-lg-2 d-grid d-md-flex">
        <button class="btn btn-primary w-100" type="submit">
          <i class="fa-solid fa-search me-1"></i>search
        </button>
      </div>
    </form>
    <div id="searchFeedback" class="text-muted small mt-2"></div>
  </div>
</div>

<div id="coursesContainer" class="row g-3">
  <?php if (empty($courses)): ?>
    <div class="col-12">
      <div class="alert alert-info">No courses available. Seed the database to continue.</div>
    </div>
  <?php else: ?>
    <?php foreach ($courses as $course): ?>
      <?php
        $title       = $course['title'] ?? 'Untitled Course';
        $description = $course['description'] ?? 'No description provided yet.';
        $courseCode  = $course['course_code'] ?? 'N/A';
        $term        = $course['term'] ?? '';
        $semester    = $course['semester'] ?? '';
        $startDate   = !empty($course['start_date']) ? date('M d, Y', strtotime($course['start_date'])) : 'TBD';
        $endDate     = !empty($course['end_date']) ? date('M d, Y', strtotime($course['end_date'])) : 'TBD';
        $status      = ucfirst($course['status'] ?? 'draft');
        $courseId    = (int) ($course['id'] ?? 0);
        $instructorId = (int) ($course['instructor_id'] ?? 0);
        $ownsCourse  = $canManageCourses && ($currentRole === 'admin' || $instructorId === $currentUserId);
        $searchBlob  = strtolower($title . ' ' . $description . ' ' . $courseCode . ' ' . $term . ' ' . $semester);
      ?>
      <div class="col-md-4 course-column" data-search="<?= esc($searchBlob) ?>">
        <div class="card course-card h-100 border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="badge bg-light text-dark"><?= esc($courseCode) ?></span>
              <span class="badge <?= $status === 'Active' ? 'bg-success' : ($status === 'Draft' ? 'bg-secondary' : 'bg-warning text-dark') ?>">
                <?= esc($status) ?>
              </span>
            </div>
            <h5 class="card-title mb-1"><?= esc($title) ?></h5>
            <p class="text-muted mb-2 small"><?= esc($term) ?> <?= $semester ? '• ' . esc($semester) : '' ?></p>
            <p class="card-text text-muted flex-grow-1" style="min-height:72px;"><?= esc($description) ?></p>
            <div class="mt-3 text-muted small d-flex justify-content-between">
              <span><i class="fa-regular fa-calendar me-1"></i><?= esc($startDate) ?></span>
              <span><i class="fa-regular fa-flag me-1"></i><?= esc($endDate) ?></span>
            </div>
            <?php if ($canManageCourses): ?>
              <div class="mt-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted">
                  <?php if ($ownsCourse): ?>
                    Assigned to you
                  <?php else: ?>
                    Instructor ID: <?= esc($instructorId ?: 'N/A') ?>
                  <?php endif; ?>
                </span>
                <?php if ($ownsCourse): ?>
                  <a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/course/' . $courseId . '/upload') ?>">
                    <i class="fa-solid fa-paperclip me-1"></i>Materials
                  </a>
                <?php else: ?>
                  <button class="btn btn-sm btn-outline-secondary" type="button" disabled title="Only the assigned instructor can manage materials">
                    <i class="fa-solid fa-paperclip me-1"></i>Materials
                  </button>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?php if (!empty($canManageCourses)): ?>
  <!-- Create Course Modal -->
  <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-primary" id="createCourseModalLabel">Add Course</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= base_url('courses') ?>" method="post">
          <?= csrf_field() ?>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Course Name</label>
                <input type="text" name="title" class="form-control <?= isset($courseErrors['title']) ? 'is-invalid' : '' ?>" value="<?= esc($courseOldInput['title'] ?? '') ?>" required>
                <?php if (isset($courseErrors['title'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['title']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Course Code</label>
                <input type="text" name="course_code" class="form-control text-uppercase <?= isset($courseErrors['course_code']) ? 'is-invalid' : '' ?>" value="<?= esc($courseOldInput['course_code'] ?? '') ?>" required>
                <?php if (isset($courseErrors['course_code'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['course_code']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Term</label>
                <input type="text" name="term" class="form-control <?= isset($courseErrors['term']) ? 'is-invalid' : '' ?>" value="<?= esc($courseOldInput['term'] ?? '') ?>" placeholder="e.g., Academic Year 2025-2026" required>
                <?php if (isset($courseErrors['term'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['term']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Semester</label>
                <input type="text" name="semester" class="form-control <?= isset($courseErrors['semester']) ? 'is-invalid' : '' ?>" value="<?= esc($courseOldInput['semester'] ?? '') ?>" placeholder="e.g., 1st Semester" required>
                <?php if (isset($courseErrors['semester'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['semester']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Start Date</label>
                <input type="date" name="start_date" class="form-control <?= isset($courseErrors['start_date']) ? 'is-invalid' : '' ?>" value="<?= esc($courseOldInput['start_date'] ?? '') ?>" required>
                <?php if (isset($courseErrors['start_date'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['start_date']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">End Date</label>
                <input type="date" name="end_date" class="form-control <?= isset($courseErrors['end_date']) ? 'is-invalid' : '' ?>" value="<?= esc($courseOldInput['end_date'] ?? '') ?>" required>
                <?php if (isset($courseErrors['end_date'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['end_date']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select <?= isset($courseErrors['status']) ? 'is-invalid' : '' ?>" required>
                  <?php foreach (($courseStatuses ?? []) as $key => $label): ?>
                    <option value="<?= esc($key) ?>" <?= (($courseOldInput['status'] ?? 'draft') === $key) ? 'selected' : '' ?>>
                      <?= esc($label) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if (isset($courseErrors['status'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['status']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" rows="4" class="form-control <?= isset($courseErrors['description']) ? 'is-invalid' : '' ?>" placeholder="Describe this course..."><?= esc($courseOldInput['description'] ?? '') ?></textarea>
                <?php if (isset($courseErrors['description'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['description']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Course</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
(function ($) {
  const $form = $('#searchForm');
  const $input = $('#searchInput');
  const $mineToggle = $('#mineToggle');
  const $feedback = $('#searchFeedback');
  const $container = $('#coursesContainer');
  const canManageCourses = <?= $canManageCourses ? 'true' : 'false' ?>;
  const currentRole = '<?= esc($currentRole, 'js') ?>';
  const currentUserId = <?= (int) $currentUserId ?>;

  const escapeHtml = (value) => $('<div>').text(value ?? '').html();

  function renderCourses(courses) {
    if (!Array.isArray(courses) || courses.length === 0) {
      $container.html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
      return;
    }

    const cards = courses.map((course) => {
      const title = course.title || 'Untitled Course';
      const description = course.description || 'No description provided yet.';
      const courseCode = course.course_code || 'N/A';
      const term = course.term || '';
      const semester = course.semester || '';
      const startDate = course.start_date ? new Date(course.start_date).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD';
      const endDate = course.end_date ? new Date(course.end_date).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) : 'TBD';
      const status = (course.status || 'draft').charAt(0).toUpperCase() + (course.status || 'draft').slice(1);
      const searchBlob = (title + ' ' + description + ' ' + courseCode + ' ' + term + ' ' + semester).toLowerCase();
      const instructorId = Number(course.instructor_id || 0);
      const courseId = Number(course.id || 0);
      const ownsCourse = canManageCourses && (currentRole === 'admin' || instructorId === currentUserId);

      const materialsButton = canManageCourses
        ? ownsCourse
          ? `<a class="btn btn-sm btn-outline-primary" href="<?= base_url('admin/course') ?>/${courseId}/upload">
                <i class="fa-solid fa-paperclip me-1"></i>Materials
             </a>`
          : `<button class="btn btn-sm btn-outline-secondary" type="button" disabled title="Only the assigned instructor can manage materials">
                <i class="fa-solid fa-paperclip me-1"></i>Materials
             </button>`
        : '';

      const ownershipText = canManageCourses
        ? (ownsCourse ? 'Assigned to you' : `Instructor ID: ${escapeHtml(instructorId || 'N/A')}`)
        : '';

      return `
        <div class="col-md-4 course-column" data-search="${escapeHtml(searchBlob)}">
          <div class="card course-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-light text-dark">${escapeHtml(courseCode)}</span>
                <span class="badge ${status === 'Active' ? 'bg-success' : (status === 'Draft' ? 'bg-secondary' : 'bg-warning text-dark')}">${escapeHtml(status)}</span>
              </div>
              <h5 class="card-title mb-1">${escapeHtml(title)}</h5>
              <p class="text-muted mb-2 small">${escapeHtml(term)} ${semester ? '• ' + escapeHtml(semester) : ''}</p>
              <p class="card-text text-muted flex-grow-1" style="min-height:72px;">${escapeHtml(description)}</p>
              <div class="mt-3 text-muted small d-flex justify-content-between">
                <span><i class="fa-regular fa-calendar me-1"></i> ${escapeHtml(startDate)}</span>
                <span><i class="fa-regular fa-flag me-1"></i> ${escapeHtml(endDate)}</span>
              </div>
              ${canManageCourses ? `
                <div class="mt-3 d-flex justify-content-between align-items-center">
                  <span class="small text-muted">${ownershipText}</span>
                  ${materialsButton}
                </div>
              ` : ''}
            </div>
          </div>
        </div>`;
    }).join('');

    $container.html(cards);
  }

  function applyClientFilter(term) {
    const lowered = term.toLowerCase();
    let visible = 0;

    $('.course-column').each(function () {
      const haystack = ($(this).data('search') || '').toString();
      const match = haystack.indexOf(lowered) !== -1;
      $(this).toggle(match);
      visible += match ? 1 : 0;
    });

    if (!visible) {
      $feedback.text('No visible courses for the current filter. Try another keyword or run a server search.');
    } else if (lowered.length) {
      $feedback.text(`Showing ${visible} course${visible === 1 ? '' : 's'} that match your filter.`);
    } else {
      $feedback.text('Tip: type to filter instantly or run a server search for database results.');
    }
  }

  $input.on('keyup', function () {
    applyClientFilter($(this).val());
  });

  $mineToggle.on('change', function () {
    if ($mineToggle.length) {
      $form.trigger('submit');
    }
  });

  $form.on('submit', function (e) {
    e.preventDefault();
    const term = $input.val().trim();
    const mine = $mineToggle.length && $mineToggle.is(':checked') ? 1 : 0;

    $feedback.text('Searching the database...');

    $.getJSON('<?= site_url('courses/search') ?>', { search_term: term, mine })
      .done(function (response) {
        renderCourses(response.courses || []);
        applyClientFilter(term);
        const count = response.count ?? (response.courses ? response.courses.length : 0);
        $feedback.text(count ? `Found ${count} course${count === 1 ? '' : 's'} for "${term || 'all'}".` : 'No courses found matching your search.');
      })
      .fail(function () {
        $feedback.text('Unable to complete the search right now. Please try again later.');
      });
  });

  if ($input.val()) {
    applyClientFilter($input.val());
  }

  <?php if (!empty($canManageCourses)): ?>
    const modalShouldOpen = <?= $courseModalOpen ? 'true' : 'false' ?>;
    if (modalShouldOpen) {
      const modalEle = document.getElementById('createCourseModal');
      if (modalEle) {
        const modal = new bootstrap.Modal(modalEle);
        modal.show();
      }
    }
  <?php endif; ?>
})(jQuery);
</script>
<?= $this->endSection() ?>