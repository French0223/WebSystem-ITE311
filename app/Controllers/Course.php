<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
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
}