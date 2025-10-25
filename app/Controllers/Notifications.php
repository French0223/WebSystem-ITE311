<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\HTTP\ResponseInterface;

class Notifications extends BaseController
{
    public function get()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = (int) session('user_id');
        $model = new NotificationModel();
        $count = $model->getUnreadCount($userId);
        $list  = $model->getNotificationsForUser($userId, 5);

        return $this->response->setJSON([
            'unread' => $count,
            'items'  => $list,
        ]);
    }

    public function mark_as_read($id = null)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $notifId = (int) $id;
        if ($notifId <= 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['success' => false, 'message' => 'Invalid notification id']);
        }

        $model = new NotificationModel();
        $ok = $model->markAsRead($notifId);

        return $this->response->setJSON(['success' => (bool) $ok]);
    }
}

