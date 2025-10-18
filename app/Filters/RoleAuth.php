<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uriPath = trim($request->getUri()->getPath(), '/'); // e.g., 'admin/dashboard'

        // Must be logged in to proceed to protected groups
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }

        $role = (string) $session->get('role');

        // Normalize top-level segment (admin, teacher, student, announcements, ...)
        $firstSegment = strtok($uriPath, '/');

        // Authorization rules
        if ($role === 'admin') {
            // Admin is only meant to access /admin/* when this filter is applied to admin group
            if ($firstSegment === 'admin') {
                return; // allow
            }
        } elseif ($role === 'teacher') {
            if ($firstSegment === 'teacher') {
                return; // allow
            }
        } elseif ($role === 'student') {
            if ($firstSegment === 'student' || $firstSegment === 'announcements') {
                return; // allow
            }
        }

        // Default: deny and redirect
        $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
        return redirect()->to('/announcements');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing required
    }
}
