<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h1 class="text-primary mb-2" style="font-size: 2rem; font-weight: 600;">Teacher Dashboard</h1>
      <p class="text-muted mb-0">Welcome, <?= esc($name) ?>! These are the courses assigned to you.</p>
    </div>
    <a href="<?= base_url('courses') ?>" class="btn btn-outline-primary d-flex align-items-center gap-2">
      <i class="fa-solid fa-file-lines"></i>
      <span>Manage Courses</span>
      <i class="fa-solid fa-arrow-right"></i>
    </a>
  </div>

  <!-- Assigned Courses Section -->
  <div class="mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="text-primary mb-0 d-flex align-items-center gap-2">
        <i class="fa-solid fa-book"></i>
        Assigned Courses
      </h5>
      <span class="text-muted small">Total: <?= count($assignedCourses ?? []) ?></span>
    </div>
  </div>

  <?php if (empty($assignedCourses)): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="fa-solid fa-book-open fa-3x text-muted mb-3"></i>
        <p class="text-muted mb-0">No courses assigned to you yet.</p>
      </div>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($assignedCourses as $course): ?>
        <?php
          $courseId = (int) ($course['id'] ?? 0);
          $title = $course['title'] ?? 'Untitled Course';
          $courseCode = $course['course_code'] ?? 'N/A';
          $term = $course['term'] ?? '';
          $semester = $course['semester'] ?? '';
          $statusRaw = $course['status'] ?? 'draft';
          $status = ucfirst($statusRaw);
          $description = $course['description'] ?? 'No description provided.';
          $startDate = !empty($course['start_date']) ? date('M d, Y', strtotime($course['start_date'])) : 'TBD';
          $endDate = !empty($course['end_date']) ? date('M d, Y', strtotime($course['end_date'])) : 'TBD';
        ?>
        <div class="col-md-4">
          <div class="card course-card h-100 border-0 shadow-sm" style="position: relative;">
            <div class="card-body d-flex flex-column" style="position: relative; z-index: 1;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-light text-dark"><?= esc($courseCode) ?></span>
                <span class="badge <?= $status === 'Active' ? 'bg-success' : ($status === 'Draft' ? 'bg-secondary' : 'bg-warning text-dark') ?>">
                  <?= esc($status) ?>
                </span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-1 mb-0"><?= esc($title) ?></h5>
                <?php if ($statusRaw === 'active'): ?>
                  <button
                    type="button"
                    class="btn btn-sm btn-primary course-manage-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#courseManageModal"
                    data-course-id="<?= $courseId ?>"
                    data-course-title="<?= esc($title, 'attr') ?>"
                    data-course-code="<?= esc($courseCode, 'attr') ?>"
                    data-course-term="<?= esc($term, 'attr') ?>"
                    data-course-semester="<?= esc($semester, 'attr') ?>"
                    data-course-status="<?= esc($statusRaw, 'attr') ?>"
                    data-course-description="<?= esc($description, 'attr') ?>"
                    data-course-start="<?= esc($course['start_date'] ?? '', 'attr') ?>"
                    data-course-end="<?= esc($course['end_date'] ?? '', 'attr') ?>"
                    data-instructor-name="<?= esc($course['instructor']['name'] ?? session('name') ?? '', 'attr') ?>"
                    data-instructor-email="<?= esc($course['instructor']['email'] ?? session('email') ?? '', 'attr') ?>"
                    data-materials-url="<?= esc(base_url('admin/course/' . $courseId . '/upload'), 'attr') ?>"
                    data-course-owns="1"
                    style="cursor: pointer; position: relative; z-index: 1000;"
                  >
                    <i class="fa-solid fa-folder-open me-1"></i>Open
                  </button>
                <?php else: ?>
                  <button
                    type="button"
                    class="btn btn-sm btn-secondary"
                    disabled
                    title="This course is inactive"
                    style="cursor: not-allowed;"
                  >
                    <i class="fa-solid fa-lock me-1"></i>Inactive
                  </button>
                <?php endif; ?>
              </div>
              <p class="text-muted mb-2 small"><?= esc($term) ?> <?= $semester ? '• ' . esc($semester) : '' ?></p>
              <p class="card-text text-muted flex-grow-1" style="min-height:72px;"><?= esc($description) ?></p>
              <div class="mt-3 text-muted small d-flex justify-content-between">
                <span><i class="fa-regular fa-calendar me-1"></i><?= esc($startDate) ?></span>
                <span><i class="fa-regular fa-flag me-1"></i><?= esc($endDate) ?></span>
              </div>
              <div class="mt-3">
                <span class="small text-muted">Assigned to you</span>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Course Manage Modal -->
<div class="modal fade" id="courseManageModal" tabindex="-1" aria-labelledby="courseManageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title text-primary mb-0" id="courseManageTitle">Course Title</h5>
          <small class="text-muted d-block" id="courseManageCode">Course Code: —</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="courseManageTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overviewTab" type="button" role="tab">Home</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#materialsTab" type="button" role="tab">Materials</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#peopleTab" type="button" role="tab">People</button>
          </li>
        </ul>
        <div class="tab-content pt-3">
          <div class="tab-pane fade show active" id="overviewTab" role="tabpanel">
            <div>
              <!-- Course Overview Section -->
              <div class="mb-4">
                <h5 class="text-primary mb-3" id="overviewCourseTitleHeader">Course Title</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="text-muted small mb-1"><strong>COURSE CODE:</strong></div>
                    <div class="fw-semibold" id="overviewCourseCodeValue">—</div>
                  </div>
                  <div class="col-md-6">
                    <div class="text-muted small mb-1"><strong>STATUS:</strong></div>
                    <div>
                      <span class="badge rounded-pill bg-success text-white" id="overviewStatus">Active</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="text-muted small mb-1"><strong>TERM:</strong></div>
                    <div class="fw-semibold" id="overviewTerm">—</div>
                  </div>
                  <div class="col-md-6">
                    <div class="text-muted small mb-1"><strong>SEMESTER:</strong></div>
                    <div class="fw-semibold" id="overviewSemester">—</div>
                  </div>
                  <div class="col-md-6">
                    <div class="text-muted small mb-1"><strong>START DATE:</strong></div>
                    <div class="fw-semibold">
                      <i class="fa-regular fa-calendar me-1"></i>
                      <span id="overviewStart">—</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="text-muted small mb-1"><strong>END DATE:</strong></div>
                    <div class="fw-semibold">
                      <i class="fa-regular fa-flag me-1"></i>
                      <span id="overviewEnd">—</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Description Section -->
              <hr class="my-4">
              <div class="mb-4">
                <h6 class="fw-semibold mb-2">Description</h6>
                <div id="overviewDescription">—</div>
              </div>

              <!-- Instructor Section -->
              <hr class="my-4">
              <div>
                <div class="d-flex align-items-center gap-2 mb-3">
                  <i class="fa-solid fa-chalkboard-user text-primary"></i>
                  <h6 class="text-primary mb-0">Instructor</h6>
                </div>
                <div class="card border-0 shadow-sm">
                  <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                      <div class="flex-shrink-0">
                        <i class="fa-solid fa-chalkboard-user fa-2x text-primary"></i>
                      </div>
                      <div class="flex-grow-1">
                        <div class="fw-semibold mb-1" id="overviewInstructorName">Unassigned</div>
                        <div class="text-muted small" id="overviewInstructorEmail">—</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="materialsTab" role="tabpanel">
            <p class="text-muted" id="materialsInfo">Manage the files shared with students.</p>
            <div class="alert alert-warning d-none" id="materialsWarning">Only the assigned instructor can upload materials.</div>
            <form id="materialsUploadForm" class="d-none" method="post" enctype="multipart/form-data">
              <?= csrf_field() ?>
              <div class="mb-3">
                <label class="form-label fw-semibold">Upload new material</label>
                <input type="file" name="material" id="materialsFileInput" class="form-control" accept=".pdf,.ppt,.pptx" required>
                <div class="form-text">Allowed: PDF, PPT/PPTX • Max size: 10MB</div>
              </div>
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                  <i class="fa-solid fa-upload me-1"></i>Upload
                </button>
              </div>
            </form>
          </div>
          <div class="tab-pane fade" id="peopleTab" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <div class="btn-group" role="group" aria-label="People filters">
                <button type="button" class="btn btn-outline-primary active" data-people-filter="instructor">Instructor</button>
                <button type="button" class="btn btn-outline-primary" data-people-filter="student">Student</button>
                <button type="button" class="btn btn-outline-primary" data-people-filter="all">View All</button>
              </div>
              <input type="hidden" name="course_id" id="manageCourseId">
            </div>

            <div id="peopleInstructorSection" class="people-section">
              <h6 class="fw-semibold mb-2">Instructor</h6>
              <div class="card border-0 shadow-sm mb-3" id="peopleInstructorCard">
                <div class="card-body">
                  <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0">
                      <i class="fa-solid fa-chalkboard-user fa-2x text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="fw-semibold" id="peopleInstructorName">Unassigned</div>
                      <div class="text-muted small" id="peopleInstructorEmail">—</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Search and select instructor</label>
                <div class="position-relative">
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
                    <input type="text" class="form-control" id="instructorSearchInput" placeholder="Type to search instructors..." autocomplete="off">
                  </div>
                  <div id="instructorSearchDropdown" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;">
                    <!-- Search results will be populated here -->
                  </div>
                </div>
                <div class="form-text mt-1">
                  <i class="fa-solid fa-circle-info text-primary"></i>
                  Type to search instructors. Select from the dropdown list.
                </div>
              </div>
              <form id="assignInstructorForm" class="row g-3 align-items-end">
                <?= csrf_field() ?>
                <input type="hidden" name="course_id" id="peopleInstructorCourseId">
                <input type="hidden" name="instructor_id" id="selectedInstructorId" required>
                <div class="col-12">
                  <div id="selectedInstructorDisplay" class="d-none mb-2 p-2 bg-light rounded">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-semibold" id="selectedInstructorName"></div>
                        <div class="text-muted small" id="selectedInstructorEmail"></div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSelectedInstructor">
                        <i class="fa-solid fa-times"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                  <button type="submit" class="btn btn-primary" id="assignInstructorBtn" disabled>
                    <i class="fa-solid fa-user-plus me-1"></i>Assign Instructor
                  </button>
                </div>
                <div class="col-12 small text-muted" id="assignInstructorFeedback"></div>
              </form>
            </div>

            <div id="peopleStudentSection" class="people-section d-none">
              <h6 class="fw-semibold mb-2">Students</h6>
              <div class="mb-3">
                <label class="form-label fw-semibold">Search and select students to enroll</label>
                <div class="position-relative">
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-primary"></i></span>
                    <input type="text" class="form-control" id="studentSearchInput" placeholder="Type to search students..." autocomplete="off">
                  </div>
                  <div id="studentSearchDropdown" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;">
                    <!-- Search results will be populated here -->
                  </div>
                </div>
                <div class="form-text mt-1">
                  <i class="fa-solid fa-circle-info text-primary"></i>
                  Type to search students. Select multiple students from the dropdown list.
                </div>
              </div>
              <form id="enrollStudentForm" class="row g-3 align-items-end">
                <?= csrf_field() ?>
                <input type="hidden" name="course_id" id="peopleStudentCourseId">
                <div class="col-12">
                  <div id="selectedStudentsDisplay" class="mb-2">
                    <!-- Selected students will be displayed here -->
                  </div>
                </div>
                <div class="col-12 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="text-muted small" id="selectedStudentsCount">No students selected</span>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="clearAllSelectedStudents" style="display: none;">
                      <i class="fa-solid fa-times me-1"></i>Clear All
                    </button>
                    <button type="submit" class="btn btn-primary" id="enrollStudentBtn" disabled>
                      <i class="fa-solid fa-user-plus me-1"></i>Enroll Selected
                    </button>
                  </div>
                </div>
                <div class="col-12 small text-muted" id="enrollStudentFeedback"></div>
              </form>
            </div>

            <div id="peopleAllSection" class="people-section d-none">
              <h6 class="fw-semibold mb-2">All People in Course</h6>
              <div id="peopleAllContainer" class="d-flex flex-column gap-2"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
(function ($) {
  'use strict';
  
  // Wait for DOM and jQuery to be ready
  $(document).ready(function() {
    const students = <?= json_encode($students ?? []) ?>;
    let instructorsById = <?= json_encode($instructorsById ?? []) ?>;
    let instructors = <?= json_encode($instructors ?? []) ?>;

    const manageModalEl = document.getElementById('courseManageModal');
    let manageModal = null;
    
    if (manageModalEl) {
      manageModal = new bootstrap.Modal(manageModalEl);
    }
  const $manageTitle = $('#courseManageTitle');
  const $manageCode = $('#courseManageCode');
  const $materialsForm = $('#materialsUploadForm');
  const $materialsFileInput = $('#materialsFileInput');
  const $materialsInfo = $('#materialsInfo');
  const $materialsWarning = $('#materialsWarning');
  const $manageCourseId = $('#manageCourseId');
  const $peopleFilterButtons = $('[data-people-filter]');
  const $peopleInstructorSection = $('#peopleInstructorSection');
  const $peopleStudentSection = $('#peopleStudentSection');
  const $peopleAllSection = $('#peopleAllSection');
  const $assignInstructorForm = $('#assignInstructorForm');
  const $assignInstructorFeedback = $('#assignInstructorFeedback');
  const $peopleInstructorName = $('#peopleInstructorName');
  const $peopleInstructorEmail = $('#peopleInstructorEmail');
  const $peopleInstructorCourseId = $('#peopleInstructorCourseId');
  const $instructorSearchInput = $('#instructorSearchInput');
  const $instructorSearchDropdown = $('#instructorSearchDropdown');
  const $selectedInstructorId = $('#selectedInstructorId');
  const $selectedInstructorDisplay = $('#selectedInstructorDisplay');
  const $selectedInstructorName = $('#selectedInstructorName');
  const $selectedInstructorEmail = $('#selectedInstructorEmail');
  const $clearSelectedInstructor = $('#clearSelectedInstructor');
  const $assignInstructorBtn = $('#assignInstructorBtn');
  const $peopleAllContainer = $('#peopleAllContainer');
  const $studentSearchInput = $('#studentSearchInput');
  const $studentSearchDropdown = $('#studentSearchDropdown');
  const $selectedStudentsDisplay = $('#selectedStudentsDisplay');
  const $selectedStudentsCount = $('#selectedStudentsCount');
  const $clearAllSelectedStudents = $('#clearAllSelectedStudents');
  const $enrollStudentBtn = $('#enrollStudentBtn');
  const $enrollStudentForm = $('#enrollStudentForm');
  const $peopleStudentCourseId = $('#peopleStudentCourseId');
  const $enrollStudentFeedback = $('#enrollStudentFeedback');
  let enrolledStudentIds = [];
  let selectedStudents = [];
  const $overviewStatus = $('#overviewStatus');
  const $overviewTerm = $('#overviewTerm');
  const $overviewSemester = $('#overviewSemester');
  const $overviewStart = $('#overviewStart');
  const $overviewEnd = $('#overviewEnd');
  const $overviewDescription = $('#overviewDescription');
  const $overviewInstructorName = $('#overviewInstructorName');
  const $overviewInstructorEmail = $('#overviewInstructorEmail');
  const overviewTabBtn = document.querySelector('button[data-bs-target="#overviewTab"]');

  const escapeHtml = (value) => $('<div>').text(value ?? '').html();

  function setPeopleFilter(mode) {
    $peopleFilterButtons.removeClass('active');
    $peopleFilterButtons.filter(`[data-people-filter="${mode}"]`).addClass('active');
    $peopleInstructorSection.toggleClass('d-none', mode !== 'instructor');
    $peopleStudentSection.toggleClass('d-none', mode !== 'student');
    $peopleAllSection.toggleClass('d-none', mode !== 'all');
  }

  async function fetchPeople(courseId) {
    if (!courseId) return;
    try {
      const response = await $.getJSON('<?= site_url('courses/people') ?>', { course_id: courseId });
      renderPeople(response);
    } catch (e) {
      $assignInstructorFeedback.text('Unable to load people for this course.');
    }
  }

  function renderPeople(data) {
    const instructor = data && data.instructor ? data.instructor : null;
    const instructorName = instructor ? instructor.name : 'Unassigned';
    const instructorEmail = instructor ? instructor.email : '—';
    $peopleInstructorName.text(instructorName);
    $peopleInstructorEmail.text(instructorEmail);
    $overviewInstructorName.text(instructorName);
    $overviewInstructorEmail.text(instructorEmail);

    const students = (data && Array.isArray(data.students)) ? data.students : [];
    enrolledStudentIds = students.map(s => Number(s.id || 0)).filter(Boolean);
    
    const parts = [];
    if (instructor) {
      parts.push(`
        <div class="card border-0 shadow-sm">
          <div class="card-body d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">${escapeHtml(instructor.name)}</div>
              <div class="text-muted small">${escapeHtml(instructor.email || '')}</div>
            </div>
            <span class="badge bg-primary">Instructor</span>
          </div>
        </div>
      `);
    }
    if (students.length > 0) {
      students.forEach((s) => {
        const enrolled = s.enrollment_date ? new Date(s.enrollment_date).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' }) : '';
        parts.push(`
          <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-semibold">${escapeHtml(s.name || '')}</div>
                <div class="text-muted small">${escapeHtml(s.email || '')}</div>
                ${enrolled ? `<div class="text-muted small"><i class="fa-regular fa-calendar me-1"></i>Enrolled: ${escapeHtml(enrolled)}</div>` : ''}
              </div>
              <span class="badge bg-success text-dark">Student</span>
            </div>
          </div>
        `);
      });
    }
    $peopleAllContainer.html(parts.length ? parts.join('') : '<div class="text-muted">No people yet for this course.</div>');
  }

  if (manageModalEl) {
    $(manageModalEl).on('show.bs.modal', function (event) {
      console.log('Modal show event triggered');
      const button = event.relatedTarget;
      console.log('Button:', button);
      if (!button || !button.classList.contains('course-manage-btn')) {
        console.warn('Button is not a course-manage-btn or not found');
        return;
      }
      const $btn = $(button);
      console.log('Calling handleCourseManageClick');
      handleCourseManageClick($btn, event);
    });
  } else {
    console.error('Modal element not found');
  }
  
  function handleCourseManageClick($btn, e) {
    console.log('handleCourseManageClick called', $btn);
    try {
      if (!manageModalEl) {
        console.error('Course manage modal element not found');
        return;
      }
      if (!manageModal) {
        manageModal = new bootstrap.Modal(manageModalEl);
      }
      
      const courseId = Number($btn.data('course-id'));
      const title = $btn.data('course-title') || 'Course';
      const code = $btn.data('course-code') || '';
      const term = $btn.data('course-term') || '';
      const semester = $btn.data('course-semester') || '';
      const status = ($btn.data('course-status') || 'draft').toString().toLowerCase();
      const description = $btn.data('course-description') || 'No description provided yet.';
      const startDate = $btn.data('course-start') || '';
      const endDate = $btn.data('course-end') || '';
      const instructorName = $btn.data('instructor-name') || 'Unassigned';
      const instructorEmail = $btn.data('instructor-email') || '';
      const materialsUrl = $btn.data('materials-url') || '';
      const owns = String($btn.data('course-owns')) === '1';
      
      console.log('Course data:', { courseId, title, code, instructorName });

      if (overviewTabBtn) {
        bootstrap.Tab.getOrCreateInstance(overviewTabBtn).show();
      }

      console.log('Populating modal content');
      console.log('$manageTitle length:', $manageTitle.length);
      console.log('$manageCode length:', $manageCode.length);
      
      if ($manageTitle.length) {
        $manageTitle.text(title);
      } else {
        console.error('$manageTitle not found');
      }
      
      if ($manageCode.length) {
        $manageCode.text(code ? `Course Code: ${code}` : '');
      } else {
        console.error('$manageCode not found');
      }

      const $overviewTitleHeader = $('#overviewCourseTitleHeader');
      const $overviewCodeValue = $('#overviewCourseCodeValue');
      
      if ($overviewTitleHeader.length) {
        $overviewTitleHeader.text(title);
      } else {
        console.error('$overviewTitleHeader not found');
      }
      
      if ($overviewCodeValue.length) {
        $overviewCodeValue.text(code || '—');
      } else {
        console.error('$overviewCodeValue not found');
      }
      
      console.log('Modal title and code set');

      const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
      $overviewStatus.text(statusLabel);
      $overviewStatus
        .removeClass('bg-success bg-secondary bg-warning text-dark')
        .addClass(status === 'active' ? 'bg-success' : (status === 'draft' ? 'bg-secondary' : 'bg-warning text-dark'));

      $overviewTerm.text(term || '—');
      $overviewSemester.text(semester || '—');
      const startLabel = startDate ? new Date(startDate).toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' }) : '—';
      const endLabel = endDate ? new Date(endDate).toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' }) : '—';
      $overviewStart.text(startLabel);
      $overviewEnd.text(endLabel);
      $overviewDescription.text(description || '—');
      $overviewInstructorName.text(instructorName || 'Unassigned');
      $overviewInstructorEmail.text(instructorEmail || '—');

      $peopleInstructorCourseId.val(courseId);
      $peopleStudentCourseId.val(courseId);
      $manageCourseId.val(courseId);
      $peopleInstructorName.text(instructorName || 'Unassigned');
      $peopleInstructorEmail.text(instructorEmail || '—');
      setPeopleFilter('instructor');
      fetchPeople(courseId);

      $materialsWarning.toggleClass('d-none', owns);
      $materialsInfo.text(owns ? 'Manage the files shared with students.' : 'Only the assigned instructor can upload materials.');
      if ($materialsForm.length) {
        if (owns && materialsUrl) {
          $materialsForm.removeClass('d-none').attr('action', materialsUrl);
          if ($materialsFileInput.length) {
            $materialsFileInput.val('');
          }
        } else {
          $materialsForm.addClass('d-none').attr('action', '');
        }
      }

      const $enrollForm = $('#enrollStudentForm');
      if ($enrollForm.length) {
        if (owns) {
          $enrollForm.removeClass('d-none');
        } else {
          $enrollForm.addClass('d-none');
        }
        if ($enrollForm[0]) {
          $enrollForm[0].reset();
        }
        if ($enrollStudentFeedback.length) {
          $enrollStudentFeedback.text('');
        }
        $manageCourseId.val(courseId);
      }
    } catch (error) {
      console.error('Error in handleCourseManageClick:', error);
    }
  }

  $peopleFilterButtons.on('click', function () {
    const mode = $(this).data('people-filter') || 'instructor';
    setPeopleFilter(mode);
  });

  // Instructor search functionality
  if ($instructorSearchInput.length) {
    function filterInstructors(searchTerm) {
      if (!searchTerm || searchTerm.trim() === '') {
        return [];
      }
      const term = searchTerm.toLowerCase().trim();
      return instructors.filter(instructor => {
        const name = (instructor.name || '').toLowerCase();
        const email = (instructor.email || '').toLowerCase();
        return name.includes(term) || email.includes(term);
      });
    }

    function renderInstructorResults(results) {
      if (results.length === 0) {
        $instructorSearchDropdown.html('<div class="dropdown-item-text text-muted">No instructors found</div>').show();
        return;
      }
      const html = results.map(instructor => {
        return `
          <a class="dropdown-item" href="#" data-instructor-id="${instructor.id}" data-instructor-name="${escapeHtml(instructor.name || '')}" data-instructor-email="${escapeHtml(instructor.email || '')}">
            <div class="fw-semibold">${escapeHtml(instructor.name || '')}</div>
            <div class="text-muted small">${escapeHtml(instructor.email || '')}</div>
          </a>
        `;
      }).join('');
      $instructorSearchDropdown.html(html).show();
    }

    function selectInstructor(instructorId, instructorName, instructorEmail) {
      $selectedInstructorId.val(instructorId);
      $selectedInstructorName.text(instructorName);
      $selectedInstructorEmail.text(instructorEmail);
      $selectedInstructorDisplay.removeClass('d-none');
      $instructorSearchInput.val('');
      $instructorSearchDropdown.hide();
      $assignInstructorBtn.prop('disabled', false);
    }

    function clearSelectedInstructor() {
      $selectedInstructorId.val('');
      $selectedInstructorDisplay.addClass('d-none');
      $instructorSearchInput.val('');
      $instructorSearchDropdown.hide();
      $assignInstructorBtn.prop('disabled', true);
    }

    $instructorSearchInput.on('input', function() {
      const searchTerm = $(this).val();
      if (searchTerm.trim() === '') {
        $instructorSearchDropdown.hide();
        return;
      }
      const results = filterInstructors(searchTerm);
      renderInstructorResults(results);
    });

    $(document).on('click', '#instructorSearchDropdown .dropdown-item', function(e) {
      e.preventDefault();
      const $item = $(this);
      const instructorId = $item.data('instructor-id');
      const instructorName = $item.data('instructor-name');
      const instructorEmail = $item.data('instructor-email');
      selectInstructor(instructorId, instructorName, instructorEmail);
    });

    $clearSelectedInstructor.on('click', function() {
      clearSelectedInstructor();
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('#instructorSearchInput, #instructorSearchDropdown').length) {
        $instructorSearchDropdown.hide();
      }
    });

    if (manageModalEl) {
      manageModalEl.addEventListener('show.bs.modal', function() {
        clearSelectedInstructor();
      });
    }
  }

  // Student search functionality
  if ($studentSearchInput.length) {
    function filterStudents(searchTerm) {
      if (!searchTerm || searchTerm.trim() === '') {
        return [];
      }
      const term = searchTerm.toLowerCase().trim();
      const selectedIds = selectedStudents.map(s => s.id);
      return students.filter(student => {
        const studentId = Number(student.id || 0);
        if (enrolledStudentIds.includes(studentId)) {
          return false;
        }
        if (selectedIds.includes(studentId)) {
          return false;
        }
        const name = (student.name || '').toLowerCase();
        const email = (student.email || '').toLowerCase();
        return name.includes(term) || email.includes(term);
      });
    }

    function renderStudentResults(results) {
      if (results.length === 0) {
        $studentSearchDropdown.html('<div class="dropdown-item-text text-muted">No students found</div>').show();
        return;
      }
      const html = results.map(student => {
        return `
          <a class="dropdown-item" href="#" data-student-id="${student.id}" data-student-name="${escapeHtml(student.name || '')}" data-student-email="${escapeHtml(student.email || '')}">
            <div class="fw-semibold">${escapeHtml(student.name || '')}</div>
            <div class="text-muted small">${escapeHtml(student.email || '')}</div>
          </a>
        `;
      }).join('');
      $studentSearchDropdown.html(html).show();
    }

    function selectStudent(studentId, studentName, studentEmail) {
      const id = Number(studentId);
      if (selectedStudents.some(s => s.id === id)) {
        return;
      }
      if (enrolledStudentIds.includes(id)) {
        return;
      }
      selectedStudents.push({
        id: id,
        name: studentName,
        email: studentEmail
      });
      renderSelectedStudents();
      updateEnrollButton();
      
      const searchTerm = $studentSearchInput.val();
      if (searchTerm.trim() !== '') {
        const results = filterStudents(searchTerm);
        renderStudentResults(results);
      } else {
        $studentSearchDropdown.hide();
      }
    }

    function removeSelectedStudent(studentId) {
      const id = Number(studentId);
      selectedStudents = selectedStudents.filter(s => s.id !== id);
      renderSelectedStudents();
      updateEnrollButton();
      
      const searchTerm = $studentSearchInput.val();
      if (searchTerm.trim() !== '' && $studentSearchDropdown.is(':visible')) {
        const results = filterStudents(searchTerm);
        renderStudentResults(results);
      }
    }

    function clearAllSelectedStudents() {
      selectedStudents = [];
      renderSelectedStudents();
      updateEnrollButton();
    }

    function renderSelectedStudents() {
      if (selectedStudents.length === 0) {
        $selectedStudentsDisplay.html('');
        $selectedStudentsCount.text('No students selected');
        $clearAllSelectedStudents.hide();
        return;
      }
      
      const html = selectedStudents.map(student => {
        return `
          <div class="mb-2 p-2 bg-light rounded d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold">${escapeHtml(student.name || '')}</div>
              <div class="text-muted small">${escapeHtml(student.email || '')}</div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary remove-student-btn" data-student-id="${student.id}">
              <i class="fa-solid fa-times"></i>
            </button>
          </div>
        `;
      }).join('');
      
      $selectedStudentsDisplay.html(html);
      $selectedStudentsCount.text(`${selectedStudents.length} student${selectedStudents.length === 1 ? '' : 's'} selected`);
      $clearAllSelectedStudents.show();
    }

    function updateEnrollButton() {
      $enrollStudentBtn.prop('disabled', selectedStudents.length === 0);
    }

    $studentSearchInput.on('input', function() {
      const searchTerm = $(this).val();
      if (searchTerm.trim() === '') {
        $studentSearchDropdown.hide();
        return;
      }
      const results = filterStudents(searchTerm);
      renderStudentResults(results);
    });

    $(document).on('click', '#studentSearchDropdown .dropdown-item', function(e) {
      e.preventDefault();
      const $item = $(this);
      const studentId = $item.data('student-id');
      const studentName = $item.data('student-name');
      const studentEmail = $item.data('student-email');
      selectStudent(studentId, studentName, studentEmail);
    });

    $(document).on('click', '.remove-student-btn', function() {
      const studentId = $(this).data('student-id');
      removeSelectedStudent(studentId);
    });

    $clearAllSelectedStudents.on('click', function() {
      clearAllSelectedStudents();
    });

    $(document).on('click', function(e) {
      if (!$(e.target).closest('#studentSearchInput, #studentSearchDropdown').length) {
        $studentSearchDropdown.hide();
      }
    });

    if (manageModalEl) {
      manageModalEl.addEventListener('show.bs.modal', function() {
        clearAllSelectedStudents();
      });
    }
  }

  if ($assignInstructorForm.length) {
    $assignInstructorForm.on('submit', function (e) {
      e.preventDefault();
      const courseId = Number($peopleInstructorCourseId.val());
      const instructorId = Number($selectedInstructorId.val());
      if (!courseId || !instructorId) {
        $assignInstructorFeedback.text('Select an instructor.');
        return;
      }
      $assignInstructorFeedback.text('Assigning instructor...');
      $.post('<?= site_url('courses/assign-instructor') ?>', {
        course_id: courseId,
        instructor_id: instructorId,
        '<?= esc(csrf_token()) ?>': $('input[name="<?= esc(csrf_token()) ?>"]').val()
      }).done(function (resp) {
        $assignInstructorFeedback.text(resp.message || 'Instructor assigned.');
        const instructorName = resp.instructor?.name || 'Unassigned';
        const instructorEmail = resp.instructor?.email || '—';
        $peopleInstructorName.text(instructorName);
        $peopleInstructorEmail.text(instructorEmail);
        $overviewInstructorName.text(instructorName);
        $overviewInstructorEmail.text(instructorEmail);
        if (resp.instructor) {
          instructorsById[resp.instructor.id] = {
            id: resp.instructor.id,
            name: resp.instructor.name,
            email: resp.instructor.email
          };
        }
        updateCsrf(resp.csrf);
        fetchPeople(courseId);
        if ($selectedInstructorDisplay.length) {
          $selectedInstructorId.val('');
          $selectedInstructorDisplay.addClass('d-none');
          $assignInstructorBtn.prop('disabled', true);
        }
      }).fail(function (xhr) {
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to assign instructor.';
        $assignInstructorFeedback.text(msg);
        if (xhr.responseJSON && xhr.responseJSON.csrf) {
          updateCsrf(xhr.responseJSON.csrf);
        }
      });
    });
  }

  if ($enrollStudentForm.length) {
    $enrollStudentForm.on('submit', async function (e) {
      e.preventDefault();
      const courseId = Number($peopleStudentCourseId.val());
      if (!courseId || selectedStudents.length === 0) {
        $enrollStudentFeedback.text('Select at least one student.');
        return;
      }
      $enrollStudentFeedback.text(`Enrolling ${selectedStudents.length} student${selectedStudents.length === 1 ? '' : 's'}...`);
      $enrollStudentBtn.prop('disabled', true);
      
      const results = [];
      let successCount = 0;
      let failCount = 0;
      
      for (const student of selectedStudents) {
        try {
          const resp = await $.post('<?= site_url('courses/assign') ?>', {
            course_id: courseId,
            student_id: student.id,
            '<?= esc(csrf_token()) ?>': $('input[name="<?= esc(csrf_token()) ?>"]').val()
          });
          updateCsrf(resp.csrf);
          results.push({ success: true, message: resp.message || `${student.name} enrolled successfully.` });
          successCount++;
        } catch (err) {
          const msg = (err.responseJSON && err.responseJSON.message) ? err.responseJSON.message : `Failed to enroll ${student.name}.`;
          results.push({ success: false, message: msg });
          failCount++;
          if (err.responseJSON && err.responseJSON.csrf) {
            updateCsrf(err.responseJSON.csrf);
          }
        }
      }
      
      if (successCount > 0 && failCount === 0) {
        $enrollStudentFeedback.html(`<div class="text-success">Successfully enrolled ${successCount} student${successCount === 1 ? '' : 's'}.</div>`);
      } else if (successCount > 0 && failCount > 0) {
        $enrollStudentFeedback.html(`<div class="text-warning">Enrolled ${successCount} student${successCount === 1 ? '' : 's'}, ${failCount} failed.</div>`);
      } else {
        $enrollStudentFeedback.html(`<div class="text-danger">Failed to enroll students.</div>`);
      }
      
      if (failCount > 0) {
        const details = results.map(r => `<div class="${r.success ? 'text-success' : 'text-danger'}">${escapeHtml(r.message)}</div>`).join('');
        $enrollStudentFeedback.append(details);
      }
      
      fetchPeople(courseId);
      clearAllSelectedStudents();
      $enrollStudentBtn.prop('disabled', false);
    });
  }

  function updateCsrf(newHash) {
    if (!newHash) return;
    $('input[name="<?= esc(csrf_token()) ?>"]').val(newHash);
  }

  }); // End of $(document).ready
})(jQuery);
</script>
<?= $this->endSection() ?>
