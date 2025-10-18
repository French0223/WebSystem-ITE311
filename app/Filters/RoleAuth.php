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
        $uri      = $request->getUri();
        $first    = strtolower((string) $uri->getSegment(1));

        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }

        $role = strtolower((string) $session->get('role'));

        // Authorization rules
        if ($role === 'admin') {
            if ($first === 'admin') {
                return; // allow admin area
            }
        } elseif ($role === 'teacher') {
            if ($first === 'teacher') {
                return; // allow teacher area
            }
        } elseif ($role === 'student') {
            if ($first === 'student' || $first === 'announcements') {
                return; // allow student area and announcements
            }
        }

        $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
        return redirect()->to('/announcements');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}
