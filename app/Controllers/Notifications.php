<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    public function get()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $userId = (int) session()->get('user_id');
        $model  = new NotificationModel();

        $count = $model->getUnreadCount($userId);
        $list  = $model->getNotificationsForUser($userId, 5);

        return $this->response->setJSON([
            'unread' => $count,
            'items'  => $list,
        ]);
    }

    public function mark_as_read($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $model = new NotificationModel();
        $ok    = $model->markAsRead((int) $id);

        return $this->response->setJSON(['success' => $ok]);
    }
}