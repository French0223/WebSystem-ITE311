<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        $role = (string) session()->get('role');
        if (!in_array($role, ['admin', 'teacher'], true)) {
            session()->setFlashdata('error', 'Unauthorized.');
            return redirect()->to(base_url('dashboard'));
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
                'course_id'  => (int) $course_id,
                'file_name'  => $originalName,
                'file_path'  => $relativeDir . '/' . $newName,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($saved) {
                session()->setFlashdata('success', 'Material uploaded.');
            } else {
                session()->setFlashdata('error', 'Failed to save record.');
            }

            return redirect()->to(base_url('dashboard'));
        }

        return view('materials/upload', ['courseId' => (int) $course_id]);
    }

    public function delete($material_id)
    {
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login first.');
            return redirect()->to(base_url('login'));
        }

        $role = (string) session()->get('role');
        if (!in_array($role, ['admin', 'teacher'], true)) {
            session()->setFlashdata('error', 'Unauthorized.');
            return redirect()->to(base_url('dashboard'));
        }

        $model    = new MaterialModel();
        $material = $model->find((int) $material_id);

        if (!$material) {
            session()->setFlashdata('error', 'Material not found.');
            return redirect()->back();
        }

        $absPath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (is_file($absPath)) {
            @unlink($absPath);
        }

        $model->delete((int) $material_id);
        session()->setFlashdata('success', 'Material deleted.');
        return redirect()->back();
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

        $role = (string) session()->get('role');
        if ($role === 'student') {
            $userId   = (int) session()->get('user_id');
            $courseId = (int) $material['course_id'];
            $enrollments = new EnrollmentModel();
            if (!$enrollments->isAlreadyEnrolled($userId, $courseId)) {
                session()->setFlashdata('error', 'Access denied.');
                return redirect()->to(base_url('dashboard'));
            }
        }

        $absPath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (!is_file($absPath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($absPath, null);
    }
}
