<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ReplayModel;
use App\Models\NotificationModel;
use App\Models\TaskModel;
class ReplayController extends Controller {

    protected $taskModel;
    protected $notificationModel;

    function __construct() {
        $this->taskModel = new TaskModel();
        $this->notificationModel = new NotificationModel();
    }


    function save() {
        $replayModel = new ReplayModel();
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => 'invalid Request']);
        }
        $validSuccess = false;
        $rules = [
            'replay' => 'required|min_length[3]|max_length[150]',
        ];
        if(!$this->validate($rules))
        {
           return $this->response->setJSON(['success' => $validSuccess,'errors' => $this->validator->getErrors()]);
        }
        $id = decryptor($this->request->getVar('taskId'));
        $data = [
            'name'      => $this->request->getPost('replay'),
            'task_id'  => $id,
            'user_id'  => session('user_data')['id'],
        ];
        if($inc = $replayModel->insert($data)){
            $getTask = $this->taskModel->find($id);
            $notify = [
                'user_id' =>  session('user_data')['id'],
                'type'    => 'task_replay',
                'created_by' => session('user_data')['id'],
                'title'   => 'Task '.$getTask['title'].' Replayed ',
                'message' => 'Task '.$getTask['title'].' replayed  By '.session('user_data')['username']
            ];
            $this->notificationModel->insert($notify);
        }
         return $this->response->setJSON(['success' => true,'message' => 'Done']);
    }
}