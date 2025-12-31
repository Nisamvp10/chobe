<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ActivitycommentsModel;
use App\Models\TaskStaffActivityModel;

class ActivitycommentsController extends Controller {
    protected $commentModel;
    protected $taskStaffActivityModel;

    function __construct()
    {
        $this->commentModel = new ActivitycommentsModel();
        $this->taskStaffActivityModel = new TaskStaffActivityModel();
    }
    public function activityCommentsList() {
        //for admin list all comments
        if(hasPermission('','activity_comments')) {}
    }
      public function activityCommentsuserList() {
        //for User list our  comments only
    }
    
    public function saveCommets() {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }

        $rules = [
            'comment' => 'required|min_length[2]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $taskId = decryptor($this->request->getPost('taskId'));
        $activityId = decryptor($this->request->getPost('activityId'));
        $comment = trim($this->request->getPost('comment'));
        if($taskId) {
            $activity = $this->taskStaffActivityModel->find($activityId); //task_activity_id
            $activitytaskId = $activity['task_activity_id'];
        }
        //edit 
        $commentId  =  $this->request->getPost('commentId');
        $dataInc = [
            'task_id'	    => $taskId,
            'activity_id'   =>  $activitytaskId = $activity['task_activity_id'],
            'user_id'	    => session('user_data')['id'],
            'comment'       => $comment,
            'status'	    => 1,
            'created_by'	=> session('user_data')['id'],
        ];
        if($this->commentModel->insert($dataInc)) {
             return $this->response->setJSON([
                'success' => true,
                'message' => 'Done'
            ]);
        }else{
             return $this->response->setJSON([
                'success' => false,
                  'message' => 'Something went wrong please try later'
            ]);
        }
    }
}