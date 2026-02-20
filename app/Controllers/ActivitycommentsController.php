<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ActivitycommentsModel;
use App\Models\TaskStaffActivityModel;
use App\Models\TaskModel;

class ActivitycommentsController extends Controller {
    protected $commentModel;
    protected $taskStaffActivityModel;
    protected $taskModel;

    function __construct()
    {
        $this->commentModel = new ActivitycommentsModel();
        $this->taskStaffActivityModel = new TaskStaffActivityModel();
        $this->taskModel = new TaskModel();
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
            'comment' => 'required|min_length[1]',
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
            //echo $this->taskStaffActivityModel->getLastQuery(); exit();
            $activitytaskId = $activity['task_activity_id'];
             $this->taskStaffActivityModel->where(['task_activity_id'=> $activitytaskId,'task_id' => $taskId])
            ->set([
                'completed_at' => date('Y-m-d H:i:s'),
                'complated_by' => session('user_data')['id'],
                'status'    => 'completed',
                'progress'  => 'completed',
            ])->update();

        }
        //edit 
        $commentId  =  $this->request->getPost('commentId');
        $dataInc = [
            'task_id'	    => $taskId,
            'activity_id'   =>  $activitytaskId,
            'user_id'	    => session('user_data')['id'],
            'comment'       => $comment,
            'status'	    => 1,
            'created_by'	=> session('user_data')['id'],
        ];

           /* 🔹 UPDATE TASK PROGRESS */
        $totalActivities = $this->taskStaffActivityModel
            ->where('task_id', $taskId)
            ->groupBy('task_activity_id')
            ->countAllResults();

        $completedActivities = $this->taskStaffActivityModel
            ->where([
                'task_id' => $taskId,
                'status'  => 'completed'
            ])
            ->groupBy('task_activity_id')
            ->countAllResults();

        $progress = ($totalActivities > 0)
            ? round(($completedActivities / $totalActivities) * 100, 2)
            : 0;

        $taskUpdate = [
            'progress' => $progress,
            'status'   => ($progress >= 100) ? 'Completed' : 'Pending'
        ];

        $this->taskModel->update($taskId, $taskUpdate);

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