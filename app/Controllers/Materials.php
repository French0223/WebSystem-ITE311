<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        $courseId = (int) $course_id;
        $course   = $this->getCourse($courseId);
        if (!$course || !$this->canManageCourse($course)) {
            session()->setFlashdata('error', 'You can only manage materials for courses assigned to you.');
            return redirect()->to(base_url('courses?mine=1'));
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'material' => [
                    'label' => 'File',
                    'rules' => 'uploaded[material]|max_size[material,10240]|ext_in[material,pdf,ppt,pptx,doc,docx,zip]'
                ]
            ];

            if (!$this->validate($rules)) {
                session()->setFlashdata('error', 'Upload failed.');
                return redirect()->back()->withInput();
            }

            $file = $this->request->getFile('material');
            if (!$file || !$file->isValid()) {
                session()->setFlashdata('error', 'Invalid file.');
                return redirect()->back()->withInput();
            }

            $originalName = $file->getClientName();
            $newName      = $file->getRandomName();
            $relativeDir  = 'materials/' . (int) $course_id;
            $targetDir    = WRITEPATH . 'uploads/' . $relativeDir;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            if (!$file->move($targetDir, $newName)) {
                session()->setFlashdata('error', 'Could not move uploaded file.');
                return redirect()->back()->withInput();
            }

            $model = new MaterialModel();
            $saved = $model->insertMaterial([
                'course_id'  => $courseId,
                'file_name'  => $originalName,
                'file_path'  => $relativeDir . '/' . $newName,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($saved) {
                session()->setFlashdata('success', 'Material uploaded.');
            } else {
                session()->setFlashdata('error', 'Failed to save record.');
            }

            return redirect()->to(base_url('courses?mine=1'));
        }

        return view('materials/upload', [
            'courseId' => $courseId,
            'course'   => $course,
        ]);
    }

    public function download($material_id)
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        $model    = new MaterialModel();
        $material = $model->find((int) $material_id);

        if (!$material) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $courseId = (int) $material['course_id'];
        $course   = $this->getCourse($courseId);
        if (!$course) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $role = (string) session()->get('role');
        if ($role === 'student') {
            $userId   = (int) session()->get('user_id');
            $enrollments = new EnrollmentModel();
            if (!$enrollments->isAlreadyEnrolled($userId, $courseId)) {
                session()->setFlashdata('error', 'Access denied.');
                return redirect()->to(base_url('dashboard'));
            }
        } elseif (in_array($role, ['teacher', 'instructor'], true) && !$this->canManageCourse($course)) {
            session()->setFlashdata('error', 'You can only access materials for your assigned courses.');
            return redirect()->to(base_url('courses?mine=1'));
        }

        $absPath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (!is_file($absPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($absPath, null);
    }

    private function getCourse(int $courseId): ?array
    {
        $courseModel = new CourseModel();
        return $courseModel->find($courseId);
    }

    private function canManageCourse(array $course): bool
    {
        if (!session()->get('isLoggedIn')) {
            return false;
        }

        $role = (string) session()->get('role');
        if ($role === 'admin') {
            return true;
        }

        if (in_array($role, ['teacher', 'instructor'], true)) {
            return (int) ($course['instructor_id'] ?? 0) === (int) session()->get('user_id');
        }

        return false;
    }
}
