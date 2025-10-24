<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Course extends BaseController
{
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

        // Create a notification for the enrolled student
        $notifModel = new NotificationModel();
        $notifModel->insert([
            'user_id'    => $userId,
            'message'    => 'You have been enrolled in ' . $courseName,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status'  => 'ok',
            'message' => 'Enrolled successfully.',
        ]);
    }
}