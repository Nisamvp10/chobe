<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ReplayModel;
use App\Models\NotificationModel;
use App\Models\TaskModel;
use App\Models\ActivityReplayModel;
use App\Models\ActivityModel;
use App\Models\ActivityStaffModel;
use App\Models\TaskStaffActivityModel;
class ReplayController extends Controller {

    protected $taskModel;
    protected $notificationModel;
    protected $activityModel;
    protected $activityStaffModel;

    protected $taskstaffactivity;

    function __construct() {
        $this->taskModel = new TaskModel();
        $this->notificationModel = new NotificationModel();
        $this->activityModel = new ActivityModel();
        $this->activityStaffModel = new  ActivityStaffModel();
        $this->taskstaffactivity = new TaskStaffActivityModel();
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
            'replay' => 'required|min_length[1]|max_length[150]',
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
        foreach($result as &$row) {
            if($row['user_id'] == session('user_data')['id']) {
                $row['is_admin'] = 1;
            }
        }
        return $this->response->setJSON(['success' => true,'replay' => $result]);
    }

    function activityReplayHistory() {
         $replayModel = new ActivityReplayModel();
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => 'invalid Request']);
        }
        $id = decryptor($this->request->getVar('taskId'));
        $getTask =  $this->taskstaffactivity->find($id);
        if(!empty($getTask)) {
            $history = $replayModel->getReplay($getTask['task_id'],$getTask['task_activity_id']);
            foreach($history as &$row) {
            if($row['user_id'] == session('user_data')['id']) {
                $row['is_admin'] = 1;
            }
        }
        }else{
            $history = '';
        }
        
        return $this->response->setJSON(['success' => true,'replay' => $history]);
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
            'replay' => 'required|min_length[1]|max_length[150]',
        ];
        if(!$this->validate($rules))
        {
           return $this->response->setJSON(['success' => $validSuccess,'errors' => $this->validator->getErrors()]);
        }

        $id = decryptor($this->request->getVar('taskId'));
        $getTask =  $this->taskstaffactivity->find($id);
        $taskDetail =  $this->activityModel->find($getTask['task_activity_id']);
     
      
        if ( $getTask['status'] == 'completed') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'You cannot reply because this task is already completed.'
            ]);
        }else{
              
            $postStatus = $this->request->getPost('status');
            
            // $this->activityStaffModel
            //     ->where('staff_id', session('user_data')['id'])
            //     ->where('activity_id', $id)
            //     ->set(['status' => $getTask['status']])
            //     ->update();
            // Get all staff statuses for this activity
            // $result = $this->activityStaffModel
            //     ->select('status')
            //     ->where('activity_id', $id)
            //     ->findAll();

            // $total = count($result);
            // if ($total == 0) return; // No assignees

            // $completed  = 0;
            // $inProgress = 0;

            // foreach ($result as $row) {
            //     if ($row['status'] === 'Completed') {
            //         $completed++;
            //     } elseif ($row['status'] === 'In_Progress') {
            //         $inProgress++;
            //     }
            // }

            // $progress = (($completed + ($inProgress * 0.5)) / $total) * 100;

            // if ($completed == $total && $total > 0) {
            //     $status   = 'Completed';
            //     $progress = 100;
            // } elseif ($completed == 0 && $inProgress == 0) {
            //     $status   = 'Pending';
            //     $progress = 0;
            // } else {
            //     $status = 'In_Progress';
            // }

            // $updateData = [
            //     'status'   => $status,
            //     'progress' => round($progress, 2),
            // ];

            // $this->activityModel->update($id, $updateData);
            // $this->masterTaskStatusUpdate( $getTask['task_id']);

        }
       
        $data = [
            'reply_text'  => $this->request->getPost('replay'),
            'task_id'  => $id,
            'master_task_id' => $getTask['task_id'],
            'master_activity_id' => $getTask['task_activity_id'],
            'user_id'  => session('user_data')['id'],
        ];
        if($inc = $replayModel->insert($data))
        {
          
            $notify = [
                'user_id' =>  session('user_data')['id'],
                'type'    => 'activity_task_replay',
                'task_id' => $getTask['task_id'],
                'activity_task_id' => $getTask['task_activity_id'],
                'created_by' => session('user_data')['id'],
                'title'   => 'Task '.$taskDetail['activity_title'].' Replayed ',
                'message' => 'Task '.$taskDetail['activity_title'].' replayed  By '.session('user_data')['username']
            ];
            $this->notificationModel->insert($notify);
        }
         return $this->response->setJSON(['success' => true,'message' => 'Done']);
    }

    private function masterTaskStatusUpdate($taskId) {
        $activitiesTask = $this->activityModel->where('task_id', $taskId)->findAll();

        $totalTasks      = count($activitiesTask);
        $completedTasks  = 0;
        $inProgressTasks = 0;

        foreach ($activitiesTask as $task) {
            if ($task['status'] === 'Completed') {
                    $completedTasks++;
            } elseif ($task['status'] === 'In_Progress') {
                $inProgressTasks++;
            }
        }
      
        $masterProgress = (($completedTasks + ($inProgressTasks * 0.5)) / $totalTasks) * 100;

        // Master Task status
        if ($completedTasks == $totalTasks && $totalTasks > 0) {
                $status   = 'Completed';
                $progress = 100;
            } elseif ($completedTasks == 0 && $inProgressTasks == 0) {
                $status   = 'Pending';
                $progress = 0;
            } else {
                $status = 'In_Progress';
            }


        $updateData = [
            'status'   => $status,
            'progress' => round($masterProgress, 2),
        ];
        $this->taskModel->update($taskId, $updateData);
    }
}