<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\NotificationModel;

class Notification extends Controller{
    protected $notifications;
    function __construct(){
        $this->notifications = new NotificationModel();
    }
    function notifications(){
        $role = session('user_data')['role'];
        
        if ($role != 1) 
        {
            $notify = $this->notifications->where(['user_id' =>session('user_data')['id'],'is_read' =>0 ])->findAll();
        }else
        {
            $notify = $this->notifications->where('is_read',0)->findAll();
        }     
        return $this->response->setJSON([
            'unread_count' => count($notify) ?? 0
        ]);
    }
    
    function load() {
         $role = session('user_data')['role'];
        if ($role != 1) 
        {
            $notify = $this->notifications->where(['user_id' =>session('user_data')['id'],'is_read' =>0 ])->findAll();
             foreach($notify as &$noti) {
                $noti['id'] = encryptor($noti['id']);
            }
        }else
        {
            //$notify = $this->notifications->where('is_read',0)->findAll();
            $notify = $this->notifications->where(['user_id' =>session('user_data')['id'],'is_read' =>0 ])->findAll();
            foreach($notify as &$noti) {
                $noti['id'] = encryptor($noti['id']);
            }

        }     
        return $this->response->setJSON([
            'notifications' => $notify,
        ]);

    }

    function myNotifications () {
       
        $page = "Notifications";
        return view('admin/notifications/index',compact('page',));
    }

    function allnotification() {

        if(!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true ,'message' => lang('Custom.invalidRequest')]);
        }

        if(!hasPermission('','view_notification')) {
             return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.accessDenied')
            ]);
        }

        $search = $this->request->getPost('search');
        $filter = $this->request->getPost('filter');
        
        $notify = $this->notifications->getStaffNotifications();
        foreach($notify as &$noti) {
            $noti['id'] = encryptor($noti['id']);
            $user = session('user_data');
            $role = $user['role'] ?? null;
            $by = ($role == 1 && !empty($noti['name'])) ? '[ Send to ' . $noti['name'] . ' ]' : '';
            $noti['title'] = $noti['title'] .' '.$by;
        }
        return $this->response->setJSON([
                'success' => true,
                'notification' => $notify
        ]);
    }

    function view() {
         if(!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true ,'message' => lang('Custom.invalidRequest')]);
        }

        $user = session('user_data');
        $role = $user['role'] ?? null;

        if(!hasPermission('','view_notification')) {
             return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.accessDenied')
            ]);
        }

        $id = decryptor($this->request->getPost('id'));
        $updated = ($role !=1 ? $this->notifications->update($id,['is_read' => 1]) : false);
        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Notification Viewd' : 'You are not assigned to this task',
            'id' => encryptor($id)

        ]);
    }
}