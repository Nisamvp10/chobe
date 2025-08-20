<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ReplayModel;
use App\Models\NotificationModel;
use App\Models\TaskModel;
use App\Models\ActivityReplayModel;
use App\Models\ActivityModel;
class ReplayController extends Controller {

    protected $taskModel;
    protected $notificationModel;
    protected $activityModel;

    function __construct() {
        $this->taskModel = new TaskModel();
        $this->notificationModel = new NotificationModel();
        $this->activityModel = new ActivityModel();
    }


    function save() {
        $replayModel = new ReplayModel();
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => 'invalid Request']);
        }
        if(!haspermission('','create_replay')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
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
            'reply_text'      => $this->request->getPost('replay'),
            'task_id'  => $id,
            'user_id'  => session('user_data')['id'],
        ];
        if($inc = $replayModel->insert($data))
        {
            $getTask = $this->taskModel->find($id);
            $notify = [
                'user_id' =>  session('user_data')['id'],
                'type'    => 'task_replay',
                'task_id' => $id,
                'created_by' => session('user_data')['id'],
                'title'   => 'Task '.$getTask['title'].' Replayed ',
                'message' => 'Task '.$getTask['title'].' replayed  By '.session('user_data')['username']
            ];
            $this->notificationModel->insert($notify);
        }
         return $this->response->setJSON(['success' => true,'message' => 'Done']);
    }
    function replayHistory() {
         $replayModel = new ReplayModel();
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => 'invalid Request']);
        }
        $id = decryptor($this->request->getVar('taskId'));
        $result = $replayModel->getHistory($id);
        return $this->response->setJSON(['success' => true,'replay' => $result]);
    }

    function activityReplayHistory() {
         $replayModel = new ActivityReplayModel();
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => 'invalid Request']);
        }
        $id = decryptor($this->request->getVar('taskId'));
        $result = $replayModel->getHistory($id);
        return $this->response->setJSON(['success' => true,'replay' => $result]);
    }

    
    public function activityReplaySave() {
         $replayModel = new ActivityReplayModel(); 
        if(!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false , 'msg' => lang('Custom.invalidRequest')]);
        }

        if(!haspermission('','create_replay')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
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
        $getTask =  $this->activityModel->find($id);

        if ( $getTask['status'] == 'Completed') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'You cannot reply because this task is already completed.'
            ]);
        }
        
        $data = [
            'reply_text'      => $this->request->getPost('replay'),
            'task_id'  => $id,
            'user_id'  => session('user_data')['id'],
        ];
        if($inc = $replayModel->insert($data))
        {
          
            $notify = [
                'user_id' =>  session('user_data')['id'],
                'type'    => 'activity_task_replay',
                'task_id' => $getTask['task_id'],
                'activity_task_id' => $id,
                'created_by' => session('user_data')['id'],
                'title'   => 'Task '.$getTask['activity_title'].' Replayed ',
                'message' => 'Task '.$getTask['activity_title'].' replayed  By '.session('user_data')['username']
            ];
            $this->notificationModel->insert($notify);
        }
         return $this->response->setJSON(['success' => true,'message' => 'Done']);
    }
}