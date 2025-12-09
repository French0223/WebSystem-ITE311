<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Course extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();
        $mineOnly = $this->shouldFilterMyCourses();

        if ($mineOnly) {
            $courseModel->where('instructor_id', (int) session('user_id'));
        }

        $courses = $courseModel->orderBy('title', 'ASC')->findAll();
        $canManageCourses = $this->canManageCourses();
        $students = $canManageCourses ? $this->getStudentOptions() : [];
        $instructors = $canManageCourses ? $this->getInstructorOptions() : [];

        return view('courses/index', [
            'title'      => 'Courses',
            'courses'    => $courses,
            'searchTerm' => '',
            'canManageCourses' => $canManageCourses,
            'mineOnly'        => $mineOnly,
            'students'        => $students,
            'instructors'     => $instructors,
            'courseStatuses'   => ['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive'],
        ]);
    }

    public function assignStudent()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['status' => 'error', 'message' => 'Please login first.', 'csrf' => csrf_hash()]);
        }

        if (!$this->canManageCourses()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized.', 'csrf' => csrf_hash()]);
        }

        $courseId = (int) $this->request->getPost('course_id');
        $studentId = (int) $this->request->getPost('student_id');

        if ($courseId <= 0 || $studentId <= 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Invalid request.', 'csrf' => csrf_hash()]);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['status' => 'error', 'message' => 'Course not found.', 'csrf' => csrf_hash()]);
        }

        $isAdmin = session('role') === 'admin';
        if (!$isAdmin && (int) ($course['instructor_id'] ?? 0) !== (int) session('user_id')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'You can only manage your courses.', 'csrf' => csrf_hash()]);
        }

        $userModel = new UserModel();
        $student = $userModel->where('id', $studentId)->where('role', 'student')->first();
        if (!$student) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Student not found.', 'csrf' => csrf_hash()]);
        }

        $enrollments = new EnrollmentModel();
        if (!$enrollments->enrollStudent($courseId, $studentId)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON(['status' => 'error', 'message' => 'Unable to enroll student.', 'csrf' => csrf_hash()]);
        }

        return $this->response->setJSON([
            'status'  => 'ok',
            'message' => $student['name'] . ' assigned to ' . ($course['title'] ?? 'course') . '.',
            'csrf'    => csrf_hash(),
        ]);
    }

    public function assignInstructor()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['status' => 'error', 'message' => 'Please login first.', 'csrf' => csrf_hash()]);
        }

        if (!$this->canManageCourses()) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized.', 'csrf' => csrf_hash()]);
        }

        $courseId = (int) $this->request->getPost('course_id');
        $instructorId = (int) $this->request->getPost('instructor_id');

        if ($courseId <= 0 || $instructorId <= 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Invalid request.', 'csrf' => csrf_hash()]);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['status' => 'error', 'message' => 'Course not found.', 'csrf' => csrf_hash()]);
        }

        $isAdmin = session('role') === 'admin';
        if (!$isAdmin && (int) ($course['instructor_id'] ?? 0) !== (int) session('user_id')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'You can only manage your courses.', 'csrf' => csrf_hash()]);
        }

        $userModel = new UserModel();
        $instructor = $userModel->where('id', $instructorId)
            ->whereIn('role', ['instructor', 'teacher', 'admin'])
            ->first();
        
        if (!$instructor) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Instructor not found.', 'csrf' => csrf_hash()]);
        }

        // Update the course with the new instructor
        $updateData = [
            'instructor_id' => $instructorId,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!$courseModel->update($courseId, $updateData)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON(['status' => 'error', 'message' => 'Unable to assign instructor.', 'csrf' => csrf_hash()]);
        }

        return $this->response->setJSON([
            'status'  => 'ok',
            'message' => $instructor['name'] . ' assigned as instructor to ' . ($course['title'] ?? 'course') . '.',
            'instructor' => [
                'id' => $instructor['id'],
                'name' => $instructor['name'],
                'email' => $instructor['email']
            ],
            'csrf'    => csrf_hash(),
        ]);
    }

    public function search()
    {
        $searchTerm = trim((string) ($this->request->getVar('search_term') ?? ''));
        $courseModel = new CourseModel();
        $canManageCourses = $this->canManageCourses();
        $mineOnly = $this->shouldFilterMyCourses();

        if ($mineOnly) {
            $courseModel->where('instructor_id', (int) session('user_id'));
        }

        if ($searchTerm !== '') {
            $courseModel = $courseModel
                ->groupStart()
                ->like('title', $searchTerm)
                ->orLike('description', $searchTerm)
                ->groupEnd();
        }

        $courses = $courseModel->orderBy('title', 'ASC')->findAll();

        // Build instructorsById for the response
        $instructorsById = [];
        $userModel = new UserModel();
        $instructorIds = array_filter(array_map(function($course) {
            return (int) ($course['instructor_id'] ?? 0);
        }, $courses));
        
        if (!empty($instructorIds)) {
            $instructors = $userModel->select('id, name, email')
                ->whereIn('id', array_unique($instructorIds))
                ->findAll();
            foreach ($instructors as $instructor) {
                $instructorsById[$instructor['id']] = [
                    'id' => $instructor['id'],
                    'name' => $instructor['name'],
                    'email' => $instructor['email']
                ];
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'courses'    => $courses,
                'searchTerm' => $searchTerm,
                'count'      => count($courses),
                'instructors' => $instructorsById,
            ]);
        }

        return view('courses/index', [
            'title'      => 'Courses',
            'courses'    => $courses,
            'searchTerm' => $searchTerm,
            'canManageCourses' => $canManageCourses,
            'mineOnly'        => $mineOnly,
            'students'        => $canManageCourses ? $this->getStudentOptions() : [],
            'instructors'     => $canManageCourses ? $this->getInstructorOptions() : [],
            'courseStatuses'   => ['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive'],
        ]);
    }

    public function create()
    {
        if ($redirect = $this->guardCourseManager()) {
            return $redirect;
        }

        helper(['form']);

        $rules = [
            'title'       => 'required|min_length[3]|max_length[200]',
            'course_code' => 'required|min_length[3]|max_length[50]|is_unique[courses.course_code]',
            'term'        => 'required|min_length[2]|max_length[100]',
            'semester'    => 'required|min_length[2]|max_length[100]',
            'start_date'  => 'required|valid_date[Y-m-d]',
            'end_date'    => 'required|valid_date[Y-m-d]',
            'description' => 'permit_empty|string',
            'status'      => 'required|in_list[draft,active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('course_errors', $this->validator->getErrors())
                ->with('course_modal_open', '1');
        }

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        if (strtotime($startDate) > strtotime($endDate)) {
            return redirect()->back()
                ->withInput()
                ->with('course_errors', ['end_date' => 'End date must be after start date.'])
                ->with('course_modal_open', '1');
        }

        $courseModel = new CourseModel();
        $now = date('Y-m-d H:i:s');

        $data = [
            'title'         => trim((string) $this->request->getPost('title')),
            'course_code'   => strtoupper(trim((string) $this->request->getPost('course_code'))),
            'term'          => trim((string) $this->request->getPost('term')),
            'semester'      => trim((string) $this->request->getPost('semester')),
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'description'   => trim((string) $this->request->getPost('description')),
            'instructor_id' => (int) session('user_id'),
            'status'        => $this->request->getPost('status'),
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        if (!$courseModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('course_errors', $courseModel->errors() ?: ['general' => 'Unable to create course.'])
                ->with('course_modal_open', '1');
        }

        session()->setFlashdata('success', 'Course created successfully.');
        return redirect()->to(base_url('courses'));
    }

    public function enroll()
    {
        // Require login and student role
        if (!session()->get('isLoggedIn') || session('role') !== 'student') {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized.']);
        }

        // CSRF is automatically validated if enabled in Security config
        $courseId = (int) $this->request->getPost('course_id');
        if (!$courseId || $courseId <= 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Invalid course.']);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->findActive($courseId);
        if (!$course) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['status' => 'error', 'message' => 'Course not found or inactive.']);
        }

        $userId = (int) session('user_id');

        $enrollments = new EnrollmentModel();
        if ($enrollments->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response->setJSON(['status' => 'ok', 'message' => 'Already enrolled.']);
        }

        $data = [
            'user_id'         => $userId,
            'course_id'       => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s'),
        ];

        // Insert enrollment
        $insertId = $enrollments->insert($data, true);
        if (!$insertId) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON(['status' => 'error', 'message' => 'Failed to enroll. Please try again.']);
        }

        // Build a friendly course name (fallback if key differs)
        $courseName = $course['name'] ?? $course['title'] ?? ('Course #' . $courseId);
        $courseInstructorId = (int) ($course['instructor_id'] ?? 0);
        $studentName = session('name') ?: 'A student';

        // Create notifications for student + staff
        $notifModel = new NotificationModel();
        $userModel  = new UserModel();
        $now = date('Y-m-d H:i:s');
        $notifications = [];
        $notified = [];

        $addNotification = function (int $recipientId, string $message) use (&$notifications, &$notified, $now) {
            if ($recipientId <= 0 || isset($notified[$recipientId])) {
                return;
            }
            $notifications[] = [
                'user_id'    => $recipientId,
                'message'    => $message,
                'is_read'    => 0,
                'created_at' => $now,
            ];
            $notified[$recipientId] = true;
        };

        $studentMessage  = 'You have been enrolled in ' . $courseName;
        $staffMessage    = $studentName . ' enrolled in ' . $courseName;

        // Student notification
        $addNotification($userId, $studentMessage);

        // Notify assigned instructor/teacher, if available
        if ($courseInstructorId > 0 && $courseInstructorId !== $userId) {
            $addNotification($courseInstructorId, $staffMessage);
        }

        // Notify all admins about the new enrollment
        $admins = $userModel->select('id')->where('role', 'admin')->findAll();
        foreach ($admins as $admin) {
            $adminId = (int) ($admin['id'] ?? 0);
            if ($adminId > 0 && $adminId !== $userId) {
                $addNotification($adminId, $staffMessage);
            }
        }

        // Notify all teachers (excluding the student)
        $teachers = $userModel->select('id')->where('role', 'teacher')->findAll();
        foreach ($teachers as $teacher) {
            $teacherId = (int) ($teacher['id'] ?? 0);
            if ($teacherId > 0 && $teacherId !== $userId) {
                $addNotification($teacherId, $staffMessage);
            }
        }

        if (!empty($notifications)) {
            if (count($notifications) === 1) {
                $notifModel->insert($notifications[0]);
            } else {
                $notifModel->insertBatch($notifications);
            }
        }

        return $this->response->setJSON([
            'status'  => 'ok',
            'message' => 'Enrolled successfully.',
        ]);
    }

    public function people()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['status' => 'error', 'message' => 'Please login first.']);
        }

        $courseId = (int) $this->request->getVar('course_id');
        if ($courseId <= 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Invalid course ID.']);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['status' => 'error', 'message' => 'Course not found.']);
        }

        $userModel = new UserModel();
        $enrollmentModel = new EnrollmentModel();

        // Get instructor
        $instructor = null;
        $instructorId = (int) ($course['instructor_id'] ?? 0);
        if ($instructorId > 0) {
            $instructor = $userModel->select('id, name, email')
                ->where('id', $instructorId)
                ->first();
        }

        // Get enrolled students
        $enrollments = $enrollmentModel->where('course_id', $courseId)->findAll();
        $studentIds = array_map(function($e) {
            return (int) ($e['user_id'] ?? 0);
        }, $enrollments);

        $students = [];
        if (!empty($studentIds)) {
            $students = $userModel->select('id, name, email')
                ->whereIn('id', $studentIds)
                ->where('role', 'student')
                ->orderBy('name', 'ASC')
                ->findAll();

            // Add enrollment date to each student
            $enrollmentsByUserId = [];
            foreach ($enrollments as $enrollment) {
                $uid = (int) ($enrollment['user_id'] ?? 0);
                if ($uid > 0) {
                    $enrollmentsByUserId[$uid] = $enrollment;
                }
            }

            foreach ($students as &$student) {
                $sid = (int) ($student['id'] ?? 0);
                if (isset($enrollmentsByUserId[$sid])) {
                    $student['enrollment_date'] = $enrollmentsByUserId[$sid]['enrollment_date'] ?? null;
                }
            }
        }

        return $this->response->setJSON([
            'instructor' => $instructor ? [
                'id' => $instructor['id'],
                'name' => $instructor['name'],
                'email' => $instructor['email']
            ] : null,
            'students' => $students
        ]);
    }

    private function canManageCourses(): bool
    {
        if (!session()->get('isLoggedIn')) {
            return false;
        }

        return in_array(session()->get('role'), ['admin', 'instructor', 'teacher'], true);
    }

    private function guardCourseManager()
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to continue.');
            return redirect()->to(base_url('login'));
        }

        if (!$this->canManageCourses()) {
            session()->setFlashdata('error', 'You are not authorized to manage courses.');
            return redirect()->to(base_url('courses'));
        }

        return null;
    }

    private function shouldFilterMyCourses(): bool
    {
        if (!$this->canManageCourses()) {
            return false;
        }

        $flag = $this->request->getVar('mine');
        return (string) $flag === '1';
    }

    private function getStudentOptions(): array
    {
        $userModel = new UserModel();
        return $userModel->select('id, name, email')
            ->where('role', 'student')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function getInstructorOptions(): array
    {
        $userModel = new UserModel();
        return $userModel->select('id, name, email')
            ->whereIn('role', ['instructor', 'teacher', 'admin'])
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}