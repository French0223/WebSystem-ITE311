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
        $title        = $course['title'] ?? 'Untitled Course';
        $description  = $course['description'] ?? 'No description provided yet.';
        $courseCode   = $course['course_code'] ?? 'N/A';
        $term         = $course['term'] ?? '';
        $semester     = $course['semester'] ?? '';
        $startDate    = !empty($course['start_date']) ? date('M d, Y', strtotime($course['start_date'])) : 'TBD';
        $endDate      = !empty($course['end_date']) ? date('M d, Y', strtotime($course['end_date'])) : 'TBD';
        $statusRaw    = $course['status'] ?? 'draft';
        $status       = ucfirst($statusRaw);
        $courseId     = (int) ($course['id'] ?? 0);
        $instructorId = (int) ($course['instructor_id'] ?? 0);
        $instructor   = $instructorsById[$instructorId] ?? ['name' => 'Unassigned', 'email' => ''];
        $ownsCourse   = $canManageCourses && ($instructorId === $currentUserId);
        $searchBlob   = strtolower($title . ' ' . $description . ' ' . $courseCode . ' ' . $term . ' ' . $semester);
      ?>
      <div class="col-md-4 course-column" data-search="<?= esc($searchBlob) ?>">
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
              <?php if ($canManageCourses): ?>
                <?php if ($currentRole === 'admin' || $ownsCourse): ?>
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
                    data-instructor-name="<?= esc($instructor['name'] ?? '', 'attr') ?>"
                    data-instructor-email="<?= esc($instructor['email'] ?? '', 'attr') ?>"
                    data-materials-url="<?= esc(base_url('admin/course/' . $courseId . '/upload'), 'attr') ?>"
                    data-course-owns="<?= $ownsCourse ? '1' : '0' ?>"
                    style="cursor: pointer; position: relative; z-index: 1000;"
                  >
                    <i class="fa-solid fa-folder-open me-1"></i>Open
                  </button>
                <?php else: ?>
                  <button
                    type="button"
                    class="btn btn-sm btn-secondary"
                    disabled
                    title="This course is not assigned to you"
                    style="cursor: not-allowed;"
                  >
                    <i class="fa-solid fa-lock me-1"></i>Locked
                  </button>
                <?php endif; ?>
              <?php endif; ?>
            </div>
            <p class="text-muted mb-2 small"><?= esc($term) ?> <?= $semester ? '• ' . esc($semester) : '' ?></p>
            <p class="card-text text-muted flex-grow-1" style="min-height:72px;"><?= esc($description) ?></p>
            <div class="mt-3 text-muted small d-flex justify-content-between">
              <span><i class="fa-regular fa-calendar me-1"></i><?= esc($startDate) ?></span>
              <span><i class="fa-regular fa-flag me-1"></i><?= esc($endDate) ?></span>
            </div>
            <?php if ($canManageCourses): ?>
              <div class="mt-3">
                <span class="small text-muted">
                  <?php if ($ownsCourse): ?>
                    Assigned to you
                  <?php elseif ($instructorId > 0 && isset($instructor['name'])): ?>
                    Instructor: <?= esc($instructor['name']) ?>
                  <?php else: ?>
                    Unassigned
                  <?php endif; ?>
                </span>
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
                <select name="term" class="form-select <?= isset($courseErrors['term']) ? 'is-invalid' : '' ?>" required>
                  <option value="">Select Term</option>
                  <?php
                    $terms = ['1st Term', '2nd Term', '3rd Term'];
                    $selectedTerm = $courseOldInput['term'] ?? '';
                    foreach ($terms as $term):
                      $isSelected = ($selectedTerm === $term) ? 'selected' : '';
                  ?>
                    <option value="<?= esc($term) ?>" <?= $isSelected ?>><?= esc($term) ?></option>
                  <?php endforeach; ?>
                </select>
                <?php if (isset($courseErrors['term'])): ?>
                  <div class="invalid-feedback"><?= esc($courseErrors['term']) ?></div>
                <?php endif; ?>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Semester</label>
                <select name="semester" class="form-select <?= isset($courseErrors['semester']) ? 'is-invalid' : '' ?>" required>
                  <option value="">Select Semester</option>
                  <?php
                    $semesters = ['1st Semester', '2nd Semester', '3rd Semester', 'Summer'];
                    $selectedSemester = $courseOldInput['semester'] ?? '';
                    foreach ($semesters as $semester):
                      $isSelected = ($selectedSemester === $semester) ? 'selected' : '';
                  ?>
                    <option value="<?= esc($semester) ?>" <?= $isSelected ?>><?= esc($semester) ?></option>
                  <?php endforeach; ?>
                </select>
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
                  <input type="file" name="material" id="materialsFileInput" class="form-control" required>
                  <div class="form-text">Allowed: PDF, PPT/PPTX, DOC/DOCX, ZIP • Max size: 10MB</div>
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
<?php endif; ?>
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
  const $form = $('#searchForm');
  const $input = $('#searchInput');
  const $mineToggle = $('#mineToggle');
  const $feedback = $('#searchFeedback');
  const $container = $('#coursesContainer');
  const canManageCourses = <?= $canManageCourses ? 'true' : 'false' ?>;
  const currentRole = '<?= esc($currentRole, 'js') ?>';
  const currentUserId = <?= (int) $currentUserId ?>;
  const students = <?= json_encode($students ?? []) ?>;
  let instructorsById = <?= json_encode($instructorsById ?? []) ?>;
  let instructors = <?= json_encode($instructors ?? []) ?>;

  const manageModalEl = document.getElementById('courseManageModal');
  const manageModal = manageModalEl ? new bootstrap.Modal(manageModalEl) : null;
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
  let selectedStudents = []; // Array to store multiple selected students
  const $overviewCourseCode = $('#overviewCourseCode');
  const $overviewCourseTitle = $('#overviewCourseTitle');
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
      const ownsCourse = canManageCourses && (instructorId === currentUserId);

      const instructor = instructorsById[instructorId] || {};
      const instructorName = instructor.name || 'Unassigned';
      const instructorEmail = instructor.email || '';

      const ownershipText = canManageCourses
        ? (ownsCourse ? 'Assigned to you' : (instructorId > 0 && instructor.name ? `Instructor: ${escapeHtml(instructor.name)}` : 'Unassigned'))
        : '';

      return `
        <div class="col-md-4 course-column" data-search="${escapeHtml(searchBlob)}">
          <div class="card course-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-light text-dark">${escapeHtml(courseCode)}</span>
                <span class="badge ${status === 'Active' ? 'bg-success' : (status === 'Draft' ? 'bg-secondary' : 'bg-warning text-dark')}">${escapeHtml(status)}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-1 mb-0">${escapeHtml(title)}</h5>
                ${canManageCourses ? (
                  currentRole === 'admin' || ownsCourse
                    ? `
                  <button
                    type="button"
                    class="btn btn-sm btn-primary course-manage-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#courseManageModal"
                    data-course-id="${courseId}"
                    data-course-title="${escapeHtml(title)}"
                    data-course-code="${escapeHtml(courseCode)}"
                    data-course-term="${escapeHtml(term)}"
                    data-course-semester="${escapeHtml(semester)}"
                    data-course-status="${escapeHtml(course.status || 'draft')}"
                    data-course-description="${escapeHtml(description)}"
                    data-course-start="${escapeHtml(course.start_date || '')}"
                    data-course-end="${escapeHtml(course.end_date || '')}"
                    data-instructor-name="${escapeHtml(instructorName)}"
                    data-instructor-email="${escapeHtml(instructorEmail)}"
                    data-materials-url="<?= base_url('admin/course') ?>/${courseId}/upload"
                    data-course-owns="${ownsCourse ? '1' : '0'}"
                    style="cursor: pointer; position: relative; z-index: 1000;"
                  >
                    <i class="fa-solid fa-folder-open me-1"></i>Open
                  </button>`
                    : `
                  <button
                    type="button"
                    class="btn btn-sm btn-secondary"
                    disabled
                    title="This course is not assigned to you"
                    style="cursor: not-allowed;"
                  >
                    <i class="fa-solid fa-lock me-1"></i>Locked
                  </button>`
                ) : ''}
              </div>
              <p class="text-muted mb-2 small">${escapeHtml(term)} ${semester ? '• ' + escapeHtml(semester) : ''}</p>
              <p class="card-text text-muted flex-grow-1" style="min-height:72px;">${escapeHtml(description)}</p>
              <div class="mt-3 text-muted small d-flex justify-content-between">
                <span><i class="fa-regular fa-calendar me-1"></i> ${escapeHtml(startDate)}</span>
                <span><i class="fa-regular fa-flag me-1"></i> ${escapeHtml(endDate)}</span>
              </div>
              ${canManageCourses ? `
                <div class="mt-3">
                  <span class="small text-muted">${ownershipText}</span>
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
        instructorsById = response.instructors || instructorsById;
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
    // Update People tab
    $peopleInstructorName.text(instructorName);
    $peopleInstructorEmail.text(instructorEmail);
    // Update Home tab
    $overviewInstructorName.text(instructorName);
    $overviewInstructorEmail.text(instructorEmail);

    const students = (data && Array.isArray(data.students)) ? data.students : [];
    const hasStudents = students.length > 0;
    
    // Track enrolled student IDs to exclude from search
    enrolledStudentIds = students.map(s => Number(s.id || 0)).filter(Boolean);
    
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
    if (hasStudents) {
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

  // Listen for when the modal is shown and populate data
  if (manageModalEl) {
    $(manageModalEl).on('show.bs.modal', function (event) {
      console.log('Modal show event triggered');
      // Get the button that triggered the modal
      const button = event.relatedTarget;
      console.log('Related target:', button);
      
      if (!button) {
        console.warn('No related target found');
        return;
      }
      
      if (!button.classList.contains('course-manage-btn')) {
        console.warn('Button is not a course-manage-btn');
        return;
      }
      
      const $btn = $(button);
      console.log('Calling handleCourseManageClick');
      handleCourseManageClick($btn, event);
    });
  }
  
  // Also handle click directly as backup
  $(document).on('click', '.course-manage-btn', function (e) {
    console.log('Course manage button clicked directly');
    const $btn = $(this);
    // Store the button data for the modal event
    $(this).data('clicked', true);
  });
  
  function handleCourseManageClick($btn, e) {
    console.log('handleCourseManageClick called', $btn);
    
    try {
      // Ensure modal element exists
      if (!manageModalEl) {
        console.error('Course manage modal element not found');
        return;
      }
      
      // Initialize modal if not already initialized
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
      
      console.log('Course data loaded:', { courseId, title, code });

      // Ensure Home tab is active
      if (overviewTabBtn) {
        bootstrap.Tab.getOrCreateInstance(overviewTabBtn).show();
      }

      $manageTitle.text(title);
      $manageCode.text(code ? `Course Code: ${code}` : '');
      
      // Update Home tab content
      $('#overviewCourseTitleHeader').text(title);
      const $overviewCourseCodeValue = $('#overviewCourseCodeValue');
      $overviewCourseCodeValue.text(code || '—');

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

      // People tab defaults
      $peopleInstructorCourseId.val(courseId);
      $peopleStudentCourseId.val(courseId);
      $manageCourseId.val(courseId);
      // Set instructor info immediately from data attributes
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

      // Check if enroll form elements exist before using them
      const $enrollForm = $('#enrollForm');
      const $enrollNote = $('#enrollNote');
      const $enrollFeedback = $('#enrollFeedback');
      
      if ($enrollForm.length) {
        if (owns) {
          $enrollForm.removeClass('d-none');
          if ($enrollNote.length) {
            $enrollNote.text('Invite students to this course.');
          }
        } else {
          $enrollForm.addClass('d-none');
          if ($enrollNote.length) {
            $enrollNote.text('Only the assigned instructor can enroll students.');
          }
        }
        if ($enrollForm[0]) {
          $enrollForm[0].reset();
        }
        if ($enrollFeedback.length) {
          $enrollFeedback.text('');
        }
        $manageCourseId.val(courseId);
      }

      console.log('Modal content populated successfully');
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

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
      if (!$(e.target).closest('#instructorSearchInput, #instructorSearchDropdown').length) {
        $instructorSearchDropdown.hide();
      }
    });

    // Clear selection when modal opens
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
        // Exclude already enrolled students
        if (enrolledStudentIds.includes(studentId)) {
          return false;
        }
        // Exclude already selected students
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
      // Check if student is already selected
      if (selectedStudents.some(s => s.id === id)) {
        return; // Already selected, don't add again
      }
      // Check if student is already enrolled
      if (enrolledStudentIds.includes(id)) {
        return; // Already enrolled, don't allow selection
      }
      // Add to selected students
      selectedStudents.push({
        id: id,
        name: studentName,
        email: studentEmail
      });
      renderSelectedStudents();
      updateEnrollButton();
      
      // Refresh search results to exclude newly selected student
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
      
      // Refresh search results if dropdown is visible
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
      const wasSelected = selectedStudents.some(s => s.id === Number(studentId));
      selectStudent(studentId, studentName, studentEmail);
      
      if (!wasSelected) {
        // Mark as selected in dropdown and refresh results
        $item.addClass('bg-light');
        const searchTerm = $studentSearchInput.val();
        if (searchTerm.trim() !== '') {
          const results = filterStudents(searchTerm);
          if (results.length === 0) {
            $studentSearchDropdown.hide();
          } else {
            renderStudentResults(results);
          }
        }
      }
    });

    $(document).on('click', '.remove-student-btn', function() {
      const studentId = $(this).data('student-id');
      removeSelectedStudent(studentId);
    });

    $clearAllSelectedStudents.on('click', function() {
      clearAllSelectedStudents();
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
      if (!$(e.target).closest('#studentSearchInput, #studentSearchDropdown').length) {
        $studentSearchDropdown.hide();
      }
    });

    // Clear selection when modal opens
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
        // Update People tab
        $peopleInstructorName.text(instructorName);
        $peopleInstructorEmail.text(instructorEmail);
        // Update Home tab
        $overviewInstructorName.text(instructorName);
        $overviewInstructorEmail.text(instructorEmail);
        // Update instructorsById for future reference
        if (resp.instructor) {
          instructorsById[resp.instructor.id] = {
            id: resp.instructor.id,
            name: resp.instructor.name,
            email: resp.instructor.email
          };
        }
        updateCsrf(resp.csrf);
        fetchPeople(courseId);
        // Clear selected instructor
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
      
      // Display results
      if (successCount > 0 && failCount === 0) {
        $enrollStudentFeedback.html(`<div class="text-success">Successfully enrolled ${successCount} student${successCount === 1 ? '' : 's'}.</div>`);
      } else if (successCount > 0 && failCount > 0) {
        $enrollStudentFeedback.html(`<div class="text-warning">Enrolled ${successCount} student${successCount === 1 ? '' : 's'}, ${failCount} failed.</div>`);
      } else {
        $enrollStudentFeedback.html(`<div class="text-danger">Failed to enroll students.</div>`);
      }
      
      // Show detailed results if there are failures
      if (failCount > 0) {
        const details = results.map(r => `<div class="${r.success ? 'text-success' : 'text-danger'}">${escapeHtml(r.message)}</div>`).join('');
        $enrollStudentFeedback.append(details);
      }
      
      // Refresh people list and clear selections
      fetchPeople(courseId);
      clearAllSelectedStudents();
      $enrollStudentBtn.prop('disabled', false);
    });
  }

  function updateCsrf(newHash) {
    if (!newHash) return;
    $('input[name="<?= esc(csrf_token()) ?>"]').val(newHash);
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