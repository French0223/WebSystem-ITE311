<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<?php
  // Expecting $studentData passed from Auth::dashboard()
  $enrolledCourses  = $studentData['enrolledCourses']  ?? [];
  
  // Get full course details for enrolled courses
  $courseModel = new \App\Models\CourseModel();
  $userModel = new \App\Models\UserModel();
  $fullEnrolledCourses = [];
  
  foreach ($enrolledCourses as $enrolled) {
    $courseId = (int) ($enrolled['id'] ?? 0);
    if ($courseId > 0) {
      $course = $courseModel->find($courseId);
      if ($course) {
        $course['enrollment_date'] = $enrolled['enrollment_date'] ?? null;
        // Get instructor info
        $instructorId = (int) ($course['instructor_id'] ?? 0);
        if ($instructorId > 0) {
          $instructor = $userModel->find($instructorId);
          if ($instructor) {
            $course['instructor'] = [
              'name' => $instructor['name'] ?? 'Unassigned',
              'email' => $instructor['email'] ?? ''
            ];
          }
        }
        $fullEnrolledCourses[] = $course;
      }
    }
  }
  
  $currentRole = session('role');
?>
<div class="container py-4">
  <!-- Header Section -->
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <h1 class="text-primary mb-2" style="font-size: 2rem; font-weight: 600;">Student Dashboard</h1>
      <p class="text-muted mb-0">Welcome, <?= esc($name) ?>! These are your enrolled courses.</p>
    </div>
    <a href="<?= base_url('courses') ?>" class="btn btn-outline-primary d-flex align-items-center gap-2">
      <i class="fa-solid fa-file-lines"></i>
      <span>My Courses</span>
      <i class="fa-solid fa-arrow-right"></i>
    </a>
  </div>

  <!-- Enrolled Courses Section -->
  <div class="mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="text-primary mb-0 d-flex align-items-center gap-2">
        <i class="fa-solid fa-graduation-cap"></i>
        Enrolled Courses
      </h5>
      <span class="text-muted small">Total: <?= count($fullEnrolledCourses) ?></span>
    </div>
  </div>

  <?php if (empty($fullEnrolledCourses)): ?>
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="fa-solid fa-book-open fa-3x text-muted mb-3"></i>
        <p class="text-muted mb-0">You are not enrolled in any courses yet.</p>
      </div>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($fullEnrolledCourses as $course): ?>
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
          $enrollmentDate = !empty($course['enrollment_date']) ? date('M d, Y', strtotime($course['enrollment_date'])) : '';
          
          // Get instructor info
          $instructorName = 'Unassigned';
          $instructorEmail = '';
          if (isset($course['instructor'])) {
            $instructorName = $course['instructor']['name'] ?? 'Unassigned';
            $instructorEmail = $course['instructor']['email'] ?? '';
          } elseif (isset($course['instructor_id']) && $course['instructor_id'] > 0) {
            $instructor = $userModel->find($course['instructor_id']);
            if ($instructor) {
              $instructorName = $instructor['name'] ?? 'Unassigned';
              $instructorEmail = $instructor['email'] ?? '';
            }
          }
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
                    data-instructor-name="<?= esc($instructorName, 'attr') ?>"
                    data-instructor-email="<?= esc($instructorEmail, 'attr') ?>"
                    data-materials-url=""
                    data-course-owns="0"
                    data-is-student="1"
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
              <?php if ($enrollmentDate): ?>
                <div class="mt-3">
                  <span class="small text-muted">Enrolled: <?= esc($enrollmentDate) ?></span>
                </div>
              <?php endif; ?>
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
            <p class="text-muted" id="materialsInfo">View course materials shared by your instructor.</p>
            <div id="materialsList" class="mt-3">
              <!-- Materials list will be populated here -->
            </div>
          </div>
          <div class="tab-pane fade" id="peopleTab" role="tabpanel">
            <!-- Student view: Only show list of people -->
            <h6 class="fw-semibold mb-3">People in this Course</h6>
            <div id="peopleAllContainer" class="d-flex flex-column gap-2"></div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<style>
  #courseManageModal .modal-body {
    min-height: 500px;
    max-height: 70vh;
  }
  #courseManageModal .tab-content {
    min-height: 400px;
  }
  #courseManageModal .tab-pane {
    min-height: 400px;
  }
  .course-manage-btn {
    position: relative;
    z-index: 10;
    pointer-events: auto !important;
    cursor: pointer !important;
  }
  .course-card {
    position: relative;
  }
</style>
<script>
(function ($) {
  const currentRole = 'student';
  const currentUserId = <?= (int) session('user_id') ?>;
  
  const manageModalEl = document.getElementById('courseManageModal');
  const manageModal = manageModalEl ? new bootstrap.Modal(manageModalEl) : null;
  const $manageTitle = $('#courseManageTitle');
  const $manageCode = $('#courseManageCode');
  const $materialsInfo = $('#materialsInfo');
  const $peopleAllContainer = $('#peopleAllContainer');
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

  async function fetchPeople(courseId) {
    if (!courseId) return;
    try {
      const response = await $.getJSON('<?= site_url('courses/people') ?>', { course_id: courseId });
      renderPeople(response);
    } catch (e) {
      console.error('Error fetching people:', e);
    }
  }

  function renderPeople(data) {
    const instructor = data && data.instructor ? data.instructor : null;
    const instructorName = instructor ? instructor.name : 'Unassigned';
    const instructorEmail = instructor ? instructor.email : '—';
    
    // Update Home tab
    $overviewInstructorName.text(instructorName);
    $overviewInstructorEmail.text(instructorEmail);

    const students = (data && Array.isArray(data.students)) ? data.students : [];
    const parts = [];
    
    // Render for View All section
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
    if ($peopleAllContainer.length) {
      $peopleAllContainer.html(parts.length ? parts.join('') : '<div class="text-muted">No people yet for this course.</div>');
    }
  }

  function loadMaterialsForStudent(courseId) {
    if (!courseId) return;
    const $materialsList = $('#materialsList');
    if (!$materialsList.length) return;
    
    $materialsList.html('<div class="text-muted">Loading materials...</div>');
    
    $.getJSON('<?= site_url('courses/materials') ?>', { course_id: courseId })
      .done(function(response) {
        if (response && response.materials) {
          if (response.materials.length > 0) {
            const materialsHtml = response.materials.map(material => {
              const fileName = material.filename || material.name || 'Unknown';
              const fileUrl = material.url || material.download_url || '#';
              const uploadDate = material.uploaded_at ? new Date(material.uploaded_at).toLocaleDateString() : '';
              return `
                <div class="card border-0 shadow-sm mb-2">
                  <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                      <div class="fw-semibold">${escapeHtml(fileName)}</div>
                      ${uploadDate ? `<div class="text-muted small">Uploaded: ${escapeHtml(uploadDate)}</div>` : ''}
                    </div>
                    <a href="${escapeHtml(fileUrl)}" class="btn btn-sm btn-primary" target="_blank">
                      <i class="fa-solid fa-download me-1"></i>Download
                    </a>
                  </div>
                </div>
              `;
            }).join('');
            $materialsList.html(materialsHtml);
          } else {
            $materialsList.html('<div class="text-muted">No materials available for this course.</div>');
          }
        } else {
          $materialsList.html('<div class="text-muted">No materials available for this course.</div>');
        }
      })
      .fail(function(xhr, status, error) {
        console.error('Error loading materials:', { xhr, status, error });
        let errorMsg = 'Unable to load materials.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.status === 404) {
          errorMsg = 'Materials endpoint not found.';
        } else if (xhr.status === 403) {
          errorMsg = 'Access denied. You may not be enrolled in this course.';
        } else if (xhr.status === 401) {
          errorMsg = 'Please login to view materials.';
        }
        $materialsList.html(`<div class="text-danger">${escapeHtml(errorMsg)}</div>`);
      });
  }

  // Listen for when the modal is shown and populate data
  if (manageModalEl) {
    $(manageModalEl).on('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button || !button.classList.contains('course-manage-btn')) {
        return;
      }
      const $btn = $(button);
      handleCourseManageClick($btn, event);
    });
  }
  
  function handleCourseManageClick($btn, e) {
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
      const isStudent = String($btn.data('is-student')) === '1';

      // Ensure Home tab is active
      if (overviewTabBtn) {
        bootstrap.Tab.getOrCreateInstance(overviewTabBtn).show();
      }

      $manageTitle.text(title);
      $manageCode.text(code ? `Course Code: ${code}` : '');
      
      // Update Home tab content
      $('#overviewCourseTitleHeader').text(title);
      $('#overviewCourseCodeValue').text(code || '—');

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

      // People tab - for students, automatically show list
      if (isStudent) {
        fetchPeople(courseId);
      }

      // Materials tab - for students, load materials
      if (isStudent) {
        $materialsInfo.text('View course materials shared by your instructor.');
        // Store courseId for materials tab click event
        $('#materialsTab').data('course-id', courseId);
        // Load materials for viewing
        loadMaterialsForStudent(courseId);
      }
    } catch (error) {
      console.error('Error in handleCourseManageClick:', error);
    }
  }

  // Listen for Materials tab click to load/refresh materials
  $(document).on('shown.bs.tab', 'button[data-bs-target="#materialsTab"]', function() {
    const courseId = $('#materialsTab').data('course-id');
    if (courseId && currentRole === 'student') {
      loadMaterialsForStudent(courseId);
    }
  });
})(jQuery);
</script>
<?= $this->endSection() ?>
