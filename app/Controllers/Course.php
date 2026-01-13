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
        $currentRole = session('role');
        $currentUserId = (int) session('user_id');

        // If student, only show enrolled courses
        if ($currentRole === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $enrollments = $enrollmentModel->where('user_id', $currentUserId)->findAll();
            $enrolledCourseIds = array_map(function($e) {
                return (int) ($e['course_id'] ?? 0);
            }, $enrollments);
            
            if (!empty($enrolledCourseIds)) {
                $courseModel->whereIn('id', $enrolledCourseIds);
            } else {
                // If no enrollments, return empty array
                $courses = [];
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
                    'courseStatuses'   => ['active' => 'Active', 'inactive' => 'Inactive'],
                ]);
            }
        } elseif ($mineOnly) {
            $courseModel->where('instructor_id', $currentUserId);
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
            'courseStatuses'   => ['active' => 'Active', 'inactive' => 'Inactive'],
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

        // Create notification for the enrolled student
        $notifModel = new NotificationModel();
        $courseTitle = $course['title'] ?? 'course';
        $courseName = $course['name'] ?? $courseTitle;
        $now = date('Y-m-d H:i:s');
        
        // Notify the student
        $message = "You have been enrolled in the course '{$courseName}'";
        $notifModel->insert([
            'user_id'    => $studentId,
            'message'    => $message,
            'is_read'    => 0,
            'created_at' => $now,
        ]);

        // Also notify staff (instructor and admins) about the enrollment
        $notifications = [];
        $notified = [];
        $courseInstructorId = (int) ($course['instructor_id'] ?? 0);
        $enrollerId = (int) session('user_id');
        $studentName = $student['name'] ?? 'A student';
        $staffMessage = $studentName . ' has been enrolled in ' . $courseName;

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

        // Notify assigned instructor/teacher, if available and different from enroller
        if ($courseInstructorId > 0 && $courseInstructorId !== $enrollerId && $courseInstructorId !== $studentId) {
            $addNotification($courseInstructorId, $staffMessage);
        }

        // Notify all admins about the enrollment (excluding enroller and student)
        $admins = $userModel->select('id')->where('role', 'admin')->findAll();
        foreach ($admins as $admin) {
            $adminId = (int) ($admin['id'] ?? 0);
            if ($adminId > 0 && $adminId !== $enrollerId && $adminId !== $studentId) {
                $addNotification($adminId, $staffMessage);
            }
        }

        // Insert staff notifications if any
        if (!empty($notifications)) {
            if (count($notifications) === 1) {
                $notifModel->insert($notifications[0]);
            } else {
                $notifModel->insertBatch($notifications);
            }
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

        // Create notification for the assigned instructor
        $notifModel = new NotificationModel();
        $courseTitle = $course['title'] ?? 'course';
        $assignerId = (int) session('user_id');
        $now = date('Y-m-d H:i:s');
        
        // Notify the instructor if they are different from the assigner
        if ($instructorId > 0 && $instructorId !== $assignerId) {
            $message = "You have been assigned as instructor to the course '{$courseTitle}'";
            $notifModel->insert([
                'user_id'    => $instructorId,
                'message'    => $message,
                'is_read'    => 0,
                'created_at' => $now,
            ]);
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
        $currentRole = session('role');
        $currentUserId = (int) session('user_id');

        // If student, only show enrolled courses
        if ($currentRole === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $enrollments = $enrollmentModel->where('user_id', $currentUserId)->findAll();
            $enrolledCourseIds = array_map(function($e) {
                return (int) ($e['course_id'] ?? 0);
            }, $enrollments);
            
            if (!empty($enrolledCourseIds)) {
                $courseModel->whereIn('id', $enrolledCourseIds);
            } else {
                // If no enrollments, return empty array
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'courses'    => [],
                        'searchTerm' => $searchTerm,
                        'count'      => 0,
                        'instructors' => [],
                    ]);
                }
                return view('courses/index', [
                    'title'      => 'Courses',
                    'courses'    => [],
                    'searchTerm' => $searchTerm,
                    'canManageCourses' => $canManageCourses,
                    'mineOnly'        => $mineOnly,
                    'students'        => $canManageCourses ? $this->getStudentOptions() : [],
                    'instructors'     => $canManageCourses ? $this->getInstructorOptions() : [],
                    'courseStatuses'   => ['active' => 'Active', 'inactive' => 'Inactive'],
                ]);
            }
        } elseif ($mineOnly) {
            $courseModel->where('instructor_id', $currentUserId);
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
            'courseStatuses'   => ['active' => 'Active', 'inactive' => 'Inactive'],
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
            'status'      => 'required|in_list[active,inactive]',
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
            'status'        => $this->request->getPost('status') ?? 'active',
            'created_at'    => $now,
            'updated_at'    => $now,
        ];

        if (!$courseModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('course_errors', $courseModel->errors() ?: ['general' => 'Unable to create course.'])
                ->with('course_modal_open', '1');
        }

        // Create notifications for course creation
        $notifModel = new NotificationModel();
        $userModel  = new UserModel();
        $courseTitle = $data['title'];
        $creatorId = (int) session('user_id');
        $instructorId = (int) $data['instructor_id'];
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

        // Notify instructor (if different from creator, they were assigned; if same, they created it)
        if ($instructorId > 0) {
            if ($instructorId !== $creatorId) {
                $addNotification($instructorId, "A new course '{$courseTitle}' has been assigned to you");
            } else {
                $addNotification($instructorId, "Course '{$courseTitle}' has been created successfully");
            }
        }

        // Notify all admins (excluding creator if admin)
        $admins = $userModel->select('id')->where('role', 'admin')->findAll();
        foreach ($admins as $admin) {
            $adminId = (int) ($admin['id'] ?? 0);
            if ($adminId > 0 && $adminId !== $creatorId) {
                $addNotification($adminId, "A new course '{$courseTitle}' has been created");
            }
        }

        if (!empty($notifications)) {
            if (count($notifications) === 1) {
                $notifModel->insert($notifications[0]);
            } else {
                $notifModel->insertBatch($notifications);
            }
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

        // Check access: Allow access if user is admin, assigned teacher, or enrolled student (regardless of course status)
        $role = session('role');
        $userId = (int) session('user_id');
        
        if ($role === 'student') {
            $enrollmentModel = new EnrollmentModel();
            if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                    ->setJSON(['status' => 'error', 'message' => 'Access denied. You are not enrolled in this course.']);
            }
        } elseif (!in_array($role, ['admin', 'teacher', 'instructor'], true)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'Access denied.']);
        } elseif ($role !== 'admin' && (int) ($course['instructor_id'] ?? 0) !== $userId) {
            // For teachers/instructors, check if they're assigned to this course
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'Access denied. You are not assigned to this course.']);
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

    public function materials()
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

        // Check access permissions: Allow access if user is admin, assigned teacher, or enrolled student (regardless of course status)
        $role = session('role');
        $userId = (int) session('user_id');
        
        if ($role === 'student') {
            $enrollmentModel = new EnrollmentModel();
            if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                    ->setJSON(['status' => 'error', 'message' => 'Access denied. You are not enrolled in this course.']);
            }
        } elseif (!in_array($role, ['admin', 'teacher', 'instructor'], true)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'Access denied.']);
        } elseif ($role !== 'admin' && (int) ($course['instructor_id'] ?? 0) !== $userId) {
            // For teachers/instructors, check if they're assigned to this course
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'Access denied. You are not assigned to this course.']);
        }

        $materialModel = new \App\Models\MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($courseId);
        
        // Build download URLs
        $materialsWithUrls = array_map(function($material) {
            return [
                'id' => $material['id'],
                'filename' => $material['file_name'],
                'name' => $material['file_name'],
                'uploaded_at' => $material['created_at'] ?? null,
                'url' => base_url('materials/download/' . $material['id']),
                'download_url' => base_url('materials/download/' . $material['id'])
            ];
        }, $materials);

        return $this->response->setJSON([
            'materials' => $materialsWithUrls
        ]);
    }

    public function update()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['status' => 'error', 'message' => 'Please login first.', 'csrf' => csrf_hash()]);
        }

        // Only admin can update courses
        if (session('role') !== 'admin') {
            return $this->response->setStatusCode(ResponseInterface::HTTP_FORBIDDEN)
                ->setJSON(['status' => 'error', 'message' => 'Only admins can update courses.', 'csrf' => csrf_hash()]);
        }

        $courseId = (int) $this->request->getPost('course_id');
        if ($courseId <= 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Invalid course ID.', 'csrf' => csrf_hash()]);
        }

        helper(['form']);

        $rules = [
            'title'       => 'required|min_length[3]|max_length[200]',
            'course_code' => 'required|min_length[3]|max_length[50]',
            'term'        => 'required|min_length[2]|max_length[100]',
            'semester'    => 'required|min_length[2]|max_length[100]',
            'start_date'  => 'required|valid_date[Y-m-d]',
            'end_date'    => 'required|valid_date[Y-m-d]',
            'description' => 'permit_empty|string',
            'status'      => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $this->validator->getErrors(),
                    'csrf' => csrf_hash()
                ]);
        }

        $startDate = $this->request->getPost('start_date');
        $endDate   = $this->request->getPost('end_date');
        if (strtotime($startDate) > strtotime($endDate)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'End date must be after start date.', 'csrf' => csrf_hash()]);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);
        if (!$course) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['status' => 'error', 'message' => 'Course not found.', 'csrf' => csrf_hash()]);
        }

        // Check if course_code is unique (excluding current course)
        $existingCourse = $courseModel->where('course_code', strtoupper(trim((string) $this->request->getPost('course_code'))))
            ->where('id !=', $courseId)
            ->first();
        if ($existingCourse) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['status' => 'error', 'message' => 'Course code already exists.', 'csrf' => csrf_hash()]);
        }

        $updateData = [
            'title'       => trim((string) $this->request->getPost('title')),
            'course_code' => strtoupper(trim((string) $this->request->getPost('course_code'))),
            'term'        => trim((string) $this->request->getPost('term')),
            'semester'    => trim((string) $this->request->getPost('semester')),
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'description' => trim((string) $this->request->getPost('description')),
            'status'      => $this->request->getPost('status'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if (!$courseModel->update($courseId, $updateData)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)
                ->setJSON(['status' => 'error', 'message' => 'Unable to update course.', 'csrf' => csrf_hash()]);
        }

        return $this->response->setJSON([
            'status'  => 'ok',
            'message' => 'Course updated successfully.',
            'csrf'    => csrf_hash(),
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