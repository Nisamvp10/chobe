<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\TaskModel;
use App\Models\UserModel;
use App\Models\ActivityModel;
use App\Models\ActivityStaffModel;
use App\Models\AssigntaskModel;
use App\Models\TaskactivityModel;
use App\Models\TaskStaffActivityModel;
use DateTime;

class ActivitiesController extends Controller {
protected $taskModel;
protected $activityTaskModel;
protected $staffModal;
protected $activityModel;
protected $userModel;
protected $taskStaffActivityModel;
protected $taskassignModel;

    function __construct(){
        $this->taskModel = new TaskModel();
        $this->staffModal = new UserModel();
        $this->activityModel = new ActivityModel();
        $this->userModel = new UserModel();
        $this->taskassignModel = new AssigntaskModel();
        $this->activityTaskModel = new TaskactivityModel();
        $this->taskStaffActivityModel = new TaskStaffActivityModel();
    }

    function activities($id=false) {
        $id = decryptor($id);
        $task = $this->taskModel->where('id',$id)->first();
        if(!empty($task)) {
            $staff =  $this->taskassignModel->getMasterTaskStaff($id);
            //echo $this->taskassignModel->getLastQuery();
            $page = "Task : " .$task['title'];
        }else{
            $page = '';
            $staff = '';
        }
        $data = '';
        
        $activityId =$id;
        return view('admin/activities/index',compact('page','data','id','staff','activityId'));
    } 

    function mYactivities($id=false) {
        
        $id = decryptor($id);
        $task = $this->taskModel->where('id',$id)->first();
        if(!empty($task)) {
            $staff =  $this->staffModal->where('role !=',1)->findAll();
            $page = "Task : " .$task['title'];
        }else{
            $page = '';
            $staff = '';
        }
        $data = '';
        
        $activityId =$id;
        return view('admin/activities/myactivities',compact('page','data','id','staff','activityId'));
    
    }

    function save () {

        if(!haspermission('','create_task')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.invalidRequest')]);
        }
        $taskId = decryptor($this->request->getPost('taskId'));

        $rules = [
            'title' => 'required',
            'description' => 'required|min_length[3]',
            //'status' => 'required',
            
        ];

        if(!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false , 'errors' => $this->validator->getErrors()]);
        }
        $validStatus = false;
        $validMsg = '';

        $data = [
            'activity_title'        => $this->request->getPost('title'),
            'activity_description'  => $this->request->getPost('description'),
            'status'                => 'Pending',//$this->request->getPost('status'),
            'activity_type'         => 2,
            'task_id'               => $taskId,
        ];
       
            $activityModel = new ActivityModel();
            $activitiesStaff = new ActivityStaffModel();
               
                $staffs = $this->taskStaffActivityModel->where('task_id',$taskId)->groupBy('staff_id')->get()->getResult(); //$this->request->getPost('staff');
                if(!empty($staffs)) {
                        if ($activitytaskId = $activityModel->insert($data)) {
                            $getlastTask =   $this->activityModel->find($activitytaskId);
                            $this->taskModel->update($getlastTask['task_id'],['status' => 'In_Progress']);
                
                            foreach ($staffs as $index => $staff) {
                                $assign = [
                                    'task_id'       => $taskId,
                                    'task_activity_id'  => $activitytaskId,
                                    'staff_id' => $staff->staff_id,
                                    'status'    => 'pending',
                                    'started_at'   => date('Y-m-d H:i:s'),
                                    'progress'  => 'pending',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'is_open'   => $staff->is_open,
                                    'started_by'  => $staff->progress
                                    
                                ];
                               $this->taskStaffActivityModel->insert($assign);
                               // $this->notificationModel->insert($notify);
                            }
                            $validStatus = true;
                            $validMsg = 'New Task Added Successfully';
                    
                    
                }else {
                    $validMsg = lang('Custom.formError');
                }
            } else{
                $validMsg = 'Please select at least one participant for the task ';
            }
        
        return $this->response->setJSON(['success' => $validStatus,'message' => $validMsg]);
    }

    function update() {
        
        if(!haspermission('','create_task')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.invalidRequest')]);
        }
        $taskId = decryptor($this->request->getPost('taskId'));

        $rules = [
            'title' => 'required',
            'description' => 'required|min_length[3]',
            'status' => 'required',
            
        ];
        if(!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false , 'errors' => $this->validator->getErrors()]);
        }
        $validStatus = false;
        $validMsg = '';
        $staffIds  = $this->request->getPost('staff');

        $data = [
            'activity_title'        => $this->request->getPost('title'),
            'activity_description'  => $this->request->getPost('description'),
            'status'                => $this->request->getPost('status'),
            'progress'              => $this->request->getPost('progress'),
            'duedate'               => $this->request->getPost('duedate'),
        ];
      
        $activityModel = new ActivityModel();
        $activitiesStaff = new ActivityStaffModel();
               
        $staffs = $this->request->getPost('staff');
        $existingStaff  = $activitiesStaff->select('staff_id')->where('activity_id', $taskId)->get()->getResultArray();
        $existingStaffIds = array_column($existingStaff,'staff_id');
        $toAdd = array_diff($staffs, $existingStaffIds);
       // print_r($existingStaffIds);

        $toRemove = array_diff($existingStaffIds, $staffIds);
        if(!empty($staffs)) {
            if (!empty($toRemove)) {
               
               $activitiesStaff->where('activity_id', $taskId)->whereIn('staff_id', $toRemove)->delete();
            }
            if (!empty($toAdd)) {
                $insertData = [];
                foreach ($toAdd as $sid) {
                    $insertData[] = [
                        'activity_id'  => $taskId,
                        'staff_id' => $sid
                    ];
                }
                $activitiesStaff->insertBatch($insertData);
            }
            
            if($activityModel->update($taskId, $data)){
                $validStatus = true;
                $validMsg = ' Task Updated Successfully';
            }
            $masterTask = $activityModel->where('id',$taskId)->first();
            $taskStatus = $activityModel->gettotalActivity($masterTask['task_id']);
            //$activityModel->getLastQuery();
            if(!empty($taskStatus)) {
                $totalActivity = $taskStatus['total_activities'] ?? 0;
                $completedActivity = $taskStatus['completed_activities'] ?? 0 ;
                $percentage = $totalActivity > 0 ? round(($completedActivity / $totalActivity) * 100) :0;

                $status = $percentage >= 100 ? 'Completed' : 'In_Progress';
                $taskUpdate = [
                    'status' => $status,
                    'progress' => $percentage
                ];
                $this->taskModel->update($masterTask['task_id'], $taskUpdate);

            }
        }else{
            $validMsg = 'Please select at least one participant for the task ';
        }
                        
        return $this->response->setJSON(['success' => $validStatus,'message' => $validMsg]);
    }

    function allActivityList() {
        $page = (!haspermission('','view_activity_task') ? lang('Custom.accessDenied') : 'Activity Tasks' );
        $tasks = $this->taskModel->findAll();
        $staff =  $this->staffModal->where('role !=',1)->findAll();
        return view('admin/activities/list',compact('page','tasks','staff'));
        
    }

    function getStaffBytask() {
        if(!haspermission('','create_task')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.invalidRequest')]);
        }
        $taskId = decryptor($this->request->getPost('ataskId'));
        $staff =  $this->taskassignModel->getMasterTaskStaff($taskId);
        if(!empty($staff)) {
            return $this->response->setJSON(['success' => true, 'staffs' => $staff]);
        }else{
            return $this->response->setJSON(['success' => false, 'message' => 'No staff found for this task']);
        }
    }

    function getAllActivityList() {
         if(!haspermission('','view_activity_task')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.invalidRequest')]);
        }

        $taskId    = $this->request->getPost('taskFilerStatus');
        $search    = $this->request->getPost('searchInput');
        $filter    = $this->request->getPost('filter');
        $startDate = $this->request->getPost('startDate');
        $endDate   = $this->request->getPost('endDate');

        $getAlltaskwithActivity = $this->activityModel->getActivity($filter,$taskId,$search,$startDate,$endDate);

        return $this->response->setJSON(['success' => true, 'task' => $getAlltaskwithActivity]);
    }

    function activitiList() {
       
        if(!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false , 'msg' => lang('Custom.invalidRequest')]);
        }

        if(!haspermission('','task_view')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
        }

        $taskId    = $this->request->getGet('task');
        $search    = $this->request->getGet('search');
        $filter    = $this->request->getGet('filter');
        $startDate = $this->request->getGet('startDate');
        $endDate   = $this->request->getGet('endDate');
        $staffId   = (session('user_data')['role'] != 1  && session('user_data')['role'] != 2 ? session('user_data')['id'] : NULL);

        //$activityTasks = $this->activityModel->getActivities($taskId,$search,$filter,$startDate,$endDate);
        $activityTasks = $this->activityModel->getActivities($taskId,$search,$filter,$startDate,$endDate,$staffId);
        //echo $this->activityModel->getLastQuery(); exit();
        $groupData = [];
        //$allusers = $this->userModel->select('id,name,profileimg')->where(['status'=>'approved','booking_status'=>1])->findAll(); 
           $allusers =   $staff =  $this->taskassignModel->getMasterTaskStaff($taskId);

        foreach($activityTasks as &$task) {
            $taskId = $task['activityId'];
            
             $duration = null;

            if (
                $task['status'] === 'completed' &&
                !empty($task['started_at']) &&
                !empty($task['completed_at'])
            ) {
                $start = strtotime($task['started_at']);
                $end   = strtotime($task['completed_at']);

                if ($start && $end && $end >= $start) {
                    $seconds = $end - $start;

                    $days    = floor($seconds / 86400);
                    $hours   = floor(($seconds % 86400) / 3600);
                    $minutes = floor(($seconds % 3600) / 60);

                    $duration = "{$days}D {$hours}H {$minutes}M";
                }
            }
            
                
            if(!isset($groupData[$taskId])) {
                $groupData[$taskId] = [
                    'id'            => encryptor($task['id']),
                    'activityId'    => encryptor($taskId),
                    'title'         => $task['activity_title'],
                    'description'   => $task['activity_description'],
                    'priority'      =>  $task['priority'],
                    'status'        => $task['status'],
                    'branch_name'   => $task['branch_name'],
                    'progress'      => $task['progress'],
                    'overdue_date' => date('Y-m-d', strtotime($task['created_at'] . ' +1 day')),
                    'createdAt'     => $task['created_at'],
                    'staffStatus' => 'pending',//$task['staffStatus'],
                    'allUsers'      => $allusers,
                    'duration'      => $duration,
                    'users'         => [],
                    'completedBy'   => []
                ];

                if(!empty($task['profileimg']) || !empty($task['name'])) {
                    $groupData[$taskId]['users'][] = [
                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                    ];
                }
                if(!empty($task['cmImg']) || !empty($task['cmName'])) {
                    $groupData[$taskId]['completedBy'][] = [
                        'cmImg'       => $task['cmImg'],
                        'cmName' => $task['cmName'],
                        'cmId'    => $task['cmId'],
                    ];
                }
            }else{
                if ($duration !== null) {
                    $groupData[$taskId]['duration'] = $duration;
                }
                $existingProfiles = array_column($groupData[$taskId]['users'],'userId');
                if(!empty($task['userId']  )) {
                    $groupData[$taskId]['users'] [] = [
                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                    ];
                }
                $cmexistingProfiles = array_column($groupData[$taskId]['completedBy'],'cmId');
                if(!empty($task['cmImg']) || !empty($task['cmName'])) {
                    $groupData[$taskId]['completedBy'][] = [
                        'cmImg'       => $task['cmImg'],
                        'cmName' => $task['cmName'],
                        'cmId'    => $task['cmId'],
                    ];
                }
            }
        }

        $tasks = array_values($groupData);
        return $this->response->setJSON([ 'success'=>true,'task' => $tasks]);
    }

    public function lock() {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Request'
            ]);
        }

        if (!haspermission('', 'task_view')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Permission Denied'
            ]);
        }

        $activityId   = decryptor($this->request->getPost('id'));
        $loggedIn = session('user_data')['id'];
        $role     = session('user_data')['role'];

        $tasks = $this->taskStaffActivityModel->select('task_id,task_activity_id')->where(['id'=>$activityId])->first();
        $taskId =  $tasks['task_id'];
        $taskActivityId =  $tasks['task_activity_id'];
        // Get assigned staff
        $assignedStaff = $this->taskStaffActivityModel
            ->select('staff_id')
            ->where('task_id', $taskId)
            ->groupBy('staff_id')
            ->findAll();
        $assignedStaffIds = array_column($assignedStaff, 'staff_id');

        // Admin/Super Admin cannot start
        if ($role == 1 || $role == 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admins cannot start the activity. Only assigned staff can start.'
            ]);
        }

        // Logged in staff must be assigned
        if (!in_array($loggedIn, $assignedStaffIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are not assigned to this task, so you cannot start it.'
            ]);
        }

        // Check if already opened
        $existing = $this->taskStaffActivityModel
            ->where('id', $activityId)
            ->where('status', 'completed')
            ->first();

        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This task activity is already Completed. You cannot chanage it again.'
            ]);
        }

        // Record only once
        $this->taskStaffActivityModel
            ->where('task_activity_id', $taskActivityId)
            ->set([
                'completed_at' => date('Y-m-d H:i:s'),
                'complated_by' => $loggedIn,
                'status'    => 'completed',
                'progress'  => 'completed',
            ])->update();

            $total_activities = $this->taskStaffActivityModel->where('task_id',$taskId)->groupBy('task_activity_id')->countAllResults();
            $completed_activities = $this->taskStaffActivityModel->where(['task_id'=> $taskId,'status' => 'completed'])->groupBy('task_activity_id')->countAllResults(); 
            $totalProgress = ($total_activities > 0) ? round(($completed_activities / $total_activities) * 100, 2) : 0;

            if($totalProgress >1 &&  $totalProgress <= 99 ) {
                $taskUpdate = [
                    'progress' => $totalProgress,
                    'status' => 'In_Progress'
                ];
                 
            }else{
                $taskUpdate = [
                    'progress' => $totalProgress,
                    'status' => 'Completed'
                ];
            }
            $this->taskModel->where('id', $taskId)->set($taskUpdate)->update();
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Task activities started successfully'
        ]);
    }
}
