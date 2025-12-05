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

        return view('courses/index', [
            'title'      => 'Courses',
            'courses'    => $courses,
            'searchTerm' => '',
            'canManageCourses' => $canManageCourses,
            'mineOnly'        => $mineOnly,
            'courseStatuses'   => ['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive'],
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

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'courses'    => $courses,
                'searchTerm' => $searchTerm,
                'count'      => count($courses),
            ]);
        }

        return view('courses/index', [
            'title'      => 'Courses',
            'courses'    => $courses,
            'searchTerm' => $searchTerm,
            'canManageCourses' => $canManageCourses,
            'mineOnly'        => $mineOnly,
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
}