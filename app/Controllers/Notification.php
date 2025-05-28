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
        }else
        {
            $notify = $this->notifications->where('is_read',0)->findAll();
        }     

        return $this->response->setJSON([
            'notifications' => $notify,
        ]);

    }
}