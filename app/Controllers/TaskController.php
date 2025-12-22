<?php
namespace App\Controllers;

use App\Models\AssigntaskModel;
use CodeIgniter\Controller;
use App\Models\BranchesModel;
use App\Models\TaskModel;
use App\Models\NotificationModel;
use App\Models\TaskimagesModel;
use App\Controllers\UploadImages;
use App\Models\ProjectsModel;
use App\Models\ActivityModel;
use App\Models\ActivityStaffModel;
use App\Models\UserModel;
use App\Models\ProjectunitModel;
use App\Models\TaskactivityModel;
use App\Models\TaskStaffActivityModel;
class TaskController extends Controller {

    protected $branchModel;
    protected $taskActivityModel;
    protected $taskModel;
    protected $taskassignModel;
    protected $notificationModel;
    protected $taskImgModel;
    protected $uploadImg;
    protected $projects;
    protected $activityModel;
    protected $staffModal;
    protected $projectUnitModel;
    protected $taskStaffActivityModel;
    function __construct() {
        $this->branchModel = new BranchesModel();
        $this->taskModel = new TaskModel();
        $this->taskassignModel = new AssigntaskModel();
        $this->notificationModel = new NotificationModel();
        $this->taskImgModel = new TaskimagesModel();
        $this->uploadImg = new UploadImages();
        $this->projects = new ProjectsModel();
        $this->activityModel = new ActivityModel();
        $this->staffModal = new UserModel();
        $this->projectUnitModel = new ProjectunitModel();
        $this->taskActivityModel = new TaskactivityModel();
        $this->taskStaffActivityModel = new TaskStaffActivityModel();
        
    }

    function index($taskStatus= false) {

        $page = "Tasks";
        $branches = $this->branchModel->where('status','active')->findAll();
        $projects = $this->projects->where('is_active',1)->findAll();
        $projectUnits = $this->projectUnitModel->where('status',1)->findAll();
        $taskStatus = $taskStatus;
        return view('admin/task/index',compact('page','branches','taskStatus','projects','projectUnits'));
    }

    function create() {

        $page = "Create New Task";
        $branches = $this->branchModel->where('status','active')->findAll();
        $projects = $this->projects->where('is_active',1)->findAll();
        $staffs =  $this->staffModal->where('role !=',1)->findAll();
        $projectUnits = $this->projectUnitModel->where('status',1)->findAll();
        return view('admin/task/create',compact('page','branches','projects','staffs','projectUnits'));
    }
    // task save
    public function save()
    {
        if (!haspermission('', 'create_task')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.accessDenied')
            ]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }

        $db = \Config\Database::connect();
        $taskId = decryptor($this->request->getPost('taskId'));

        // Validation rules
        $rules = [
            'title'       => 'required',
            'description' => 'required|min_length[3]',
            'priority'    => 'required',
        ];

        if (empty($taskId)) {
            $rules['project'] = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors()
            ]);
        }

        // Prepare task data
        $data = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'overdue_date'  => $this->request->getPost('duedate') ?: null,
            'priority'      => $this->request->getPost('priority'),
            'branch'        => 'all',
            'project_unit'  => $this->request->getPost('projectUnit') ?: null,
            'project_id'    => $this->request->getPost('project') ?: null,
            'status'        => $this->request->getPost('status') ?? 'Pending',
            'recurrence'    => 'daily',
            'next_run_date' => date('Y-m-d', strtotime('+1 day')),
        ];

        if (!empty($taskId)) {
            $progress = $this->request->getPost('progress');
            if ($progress !== null) {
                $data['progress'] = $progress;
            }
        }

        // File upload
        $taskFiles = null;
        $file = $this->request->getFile('file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadResult = $this->uploadImg->uploadimg($file, 'taskfiles');
            $image = json_decode($uploadResult, true);
            if (!empty($image['file'])) {
                $taskFiles = [
                    'image_url' => base_url($image['file']),
                    'file_ext'  => $image['file_ext'] ?? null
                ];
            }
        }

        // Fetch active master activities
        $masterActivities = $this->activityModel->where('status', 'active')->findAll();

        $staffs = $this->request->getPost('staff') ?? [];
        $roles  = $this->request->getPost('role') ?? [];

        $db->transStart();

        if (!empty($taskId)) {
            // ---------------- UPDATE TASK ----------------
            $this->taskModel->update($taskId, $data);

            if ($taskFiles) {
                $taskFiles['task_id'] = $taskId;
                $this->taskImgModel->insert($taskFiles);
            }

            // --- Task Activities ---
            $existingTaskActivities = $this->taskActivityModel
                                        ->select('activity_id')
                                        ->where('task_id', $taskId)
                                        ->findAll();
            $existingActivityIds = array_column($existingTaskActivities, 'activity_id');

            $newTaskActivityIds = [];
            foreach ($masterActivities as $act) {
                if (!in_array($act['id'], $existingActivityIds)) {
                    $taskActivityId = $this->taskActivityModel->insert([
                        'task_id'     => $taskId,
                        'activity_id' => $act['id'],
                        'status'      => 'pending',
                        'progress'    => 'pending',
                        'created_at'  => date('Y-m-d H:i:s'),
                    ], true);

                    if ($taskActivityId) {
                        $newTaskActivityIds[] = $taskActivityId;
                    }
                }
            }

            $allTaskActivities = $this->taskActivityModel
                                    ->where('task_id', $taskId)
                                    ->findAll();
            $allTaskActivityIds = array_column($allTaskActivities, 'id');

            // --- Staff Assignment ---
            $existingAssignments = $this->taskassignModel
                                    ->where('task_id', $taskId)
                                    ->findAll();
            $existingStaffIds = array_column($existingAssignments, 'staff_id');

            foreach ($staffs as $index => $staffId) {
                $roleId = $roles[$index] ?? null;

                if (in_array($staffId, $existingStaffIds)) {
                    // Update role
                    $this->taskassignModel
                        ->where(['task_id' => $taskId, 'staff_id' => $staffId])
                        ->set(['role' => $roleId])
                        ->update();

                    // Assign new activities only
                    foreach ($newTaskActivityIds as $taskActivityId) {
                        $this->taskStaffActivityModel->insert([
                            'task_activity_id' => $taskActivityId,
                            'staff_id'         => $staffId,
                            'status'           => 'pending'
                        ]);
                    }

                } else {
                    // New staff assignment
                    $this->taskassignModel->insert([
                        'task_id'  => $taskId,
                        'staff_id' => $staffId,
                        'role'     => $roleId,
                        'status'   => 'assigned'
                    ]);

                    // Assign all activities
                    foreach ($allTaskActivityIds as $taskActivityId) {
                        $this->taskStaffActivityModel->insert([
                            'task_activity_id' => $taskActivityId,
                            'staff_id'         => $staffId,
                            'status'           => 'pending'
                        ]);
                    }

                    // Notify staff
                    $this->notificationModel->insert([
                        'user_id'    => $staffId,
                        'task_id'    => $taskId,
                        'type'       => 'task_reassign',
                        'title'      => 'Task Assigned/Updated',
                        'created_by' => session('user_data')['id'] ?? null,
                        'message'    => 'Task updated and assigned to you by ' . (session('user_data')['username'] ?? 'system')
                    ]);
                }
            }

            // --- Check staff completion ---
            $assignedStaffs = $this->taskassignModel
                                ->where('task_id', $taskId)
                                ->findAll();

            $allDone = true;
            foreach ($assignedStaffs as $staff) {
                $totalActivities = $this->taskStaffActivityModel
                                        ->where('staff_id', $staff['staff_id'])
                                        ->whereIn('task_activity_id', $allTaskActivityIds)
                                        ->countAllResults();

                $completedActivities = $this->taskStaffActivityModel
                                            ->where('staff_id', $staff['staff_id'])
                                            ->whereIn('task_activity_id', $allTaskActivityIds)
                                            ->where('status', 'completed')
                                            ->countAllResults();

                if ($completedActivities != $totalActivities) {
                    $allDone = false;
                }
            }

            if ($allDone) {
                $this->taskModel->update($taskId, ['status' => 'completed']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update task (transaction failed)'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);

        } else {
            // ---------------- CREATE TASK ----------------
            if (empty($staffs)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please select at least one participant for the task'
                ]);
            }

            if (empty($masterActivities)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No active activities found'
                ]);
            }

            $newTaskId = $this->taskModel->insert($data);
            

            if (!$newTaskId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create task'
                ]);
            }

            if ($taskFiles) {
                $taskFiles['task_id'] = $newTaskId;
                $this->taskImgModel->insert($taskFiles);
            }

            // $newTaskActivityIds = [];
            // foreach ($masterActivities as $act) {
            //     $taskActivityId = $this->taskActivityModel->insert([
            //         'task_id'     => $newTaskId,
            //         'activity_id' => $act['id'],
            //         'status'      => 'pending',
            //         'progress'    => 'pending',
            //         'created_at'  => date('Y-m-d H:i:s'),
            //     ], true);

            //     if ($taskActivityId) {
            //         $newTaskActivityIds[] = $taskActivityId;
            //     }
            // }

            foreach ($staffs as $index => $staffId) {
                $roleId = $roles[$index] ?? null;
                $this->taskassignModel->insert([
                    'task_id'  => $newTaskId,
                    'staff_id' => $staffId,
                    'role'     => $roleId,
                    'status'   => 'assigned'
                ]);

                foreach ($masterActivities as $act) {
                    $activityStaffAssign = [
                          'task_id'          => $newTaskId,
                        'task_activity_id' =>  $act['id'],
                        'staff_id'         => $staffId,
                        'status'           => 'pending'
                    ];
                    $this->taskStaffActivityModel->insert($activityStaffAssign);
                    //echo $this->taskStaffActivityModel->getLastQuery();
                }

                $this->notificationModel->insert([
                    'user_id'    => $staffId,
                    'task_id'    => $newTaskId,
                    'type'       => 'new_task',
                    'title'      => 'New Task',
                    'created_by' => session('user_data')['id'] ?? null,
                    'message'    => 'A new Task has been created by ' . (session('user_data')['username'] ?? 'system')
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create task (transaction failed)'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'New Task Added Successfully'
            ]);
        }
    }

        function list() {

            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid Request'
                ]);
            }
            if (!haspermission('','task_view')) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Permission Denied'
                ]);
            }
            $filter = $this->request->getGet('filter');
            $searchInput = $this->request->getGet('search');
            $startDate = $this->request->getGet('startDate');
            $endDate = $this->request->getGet('endDate');
            $taskProject = $this->request->getGet('taskProject');
            $alltask = $this->taskModel->getTasks('','',$filter,$searchInput,$startDate,$endDate,$taskProject); // or ->findAll()
        // echo  $this->taskModel->getLastQuery();exit();

            $allusers = $this->staffModal->select('id,name,profileimg')->where(['status'=>'approved','booking_status'=>1 ,'role !=' =>1])->findAll();
            $groupData = [];

            foreach ($alltask as &$task) {

                $taskId = $task['id'];

                if (!isset($groupData[$taskId])) {
                    
                    $groupData[$taskId] = [

                        'id'        => encryptor($task['id']),
                        'title'     => $task['title'],
                        'storeId'   => $task['store'],
                        'description' => $task['description'],
                        'branch_name' => $task['branch_name'],
                        'projectUnit' => $task['project_unit'],
                        'total_activities' => $this->taskStaffActivityModel->where('task_id',$task['id'])->groupBy('task_activity_id')->countAllResults(),
                        'completed_activities' => $this->taskStaffActivityModel->where(['task_id'=>$task['id'],'status' => 'completed'])->groupBy('task_activity_id')->countAllResults(), //$this->taskActivityModel->where(['task_id'=>$task['id'],'status' => 'completed'])->countAllResults(),//$task['completed_activities'],
                        'project'   => $task['project_id'],
                        'priority'  => $task['priority'],
                        'status'    => $task['status'],
                        'action'    => 0,//$task['action'],
                        'overdue_date' => $task['overdue_date'] ?? null,
                        'progress'  => $task['progress'],
                        'allUsers'  => $allusers,
                        'ducument'  => $task['image_url'],
                        'users'     => [],
                    ];

                    if (!empty($task['name'])) {
                        $groupData[$taskId]['users'][] = [

                            'img'       => $task['profileimg'],
                            'staffName' => $task['name'],
                            'userId'    => $task['userId'],
                            'role'      => $task['role'],
                            'userPriority' => $task['userPriority'], 
                        ];
                    }

                    $groupData[$taskId]['duration'] = $task['status'] == 'Completed'
                        ? human_duration($task['created_at'], $task['completed_at'])
                        : human_duration($task['created_at']);
                } else {
                    $existingProfiles = array_column($groupData[$taskId]['users'], 'userId');
                    if (!empty($task['name'])  && !in_array($task['userId'], $existingProfiles)) {
                        $groupData[$taskId]['users'][] = [

                            'img'       => $task['profileimg'],
                            'staffName' => $task['name'],
                            'userId'    => $task['userId'],
                            'role'      => $task['role'],
                            'userPriority' => $task['userPriority'],
                        ];
                    }
                }
            }

            $tasks = array_values($groupData);
            return $this->response->setJSON([ 'success'=>true,'task' => $tasks]);
        }

    // Mt Task
    function myTaskList() {

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Request'
            ]);
        }
        if (!haspermission('','task_view')) {
             return $this->response->setJSON([
                'success' => false,
                'message' => 'Permission Denied'
            ]);
        }
        $activityTasksAssignModel = new ActivityStaffModel();
        $filter = $this->request->getGet('filter');
        $notifiytask = $this->request->getGet('notifiytask');
        $alltasks = $this->taskModel->getMytask('','',$notifiytask,$filter); // or ->findAll()
       
        
        $groupData = [];
        foreach ($alltasks as &$task) {
            $taskId = $task['id'];

            if (!isset($groupData[$taskId])) {
                  $activityTasksAssignModel->where(['activity_id'=> $task['id'],'staff_id'=>session('user_data')['id']])->countAllResults();
                  $this->taskActivityModel->getMytaskCount($task['id']);
      //  echo $this->taskActivityModel->getLastQuery();
                $groupData[$taskId] = [

                    'id'        => encryptor($task['id']),
                    'title'     => $task['title'],
                    'storeId'   => $task['store'],
                    'description' => $task['description'],
                    'branch_name' => $task['branch_name'],
                    'project'   => $task['project_id'],
                    'total_activities' => $this->taskStaffActivityModel->where(['task_id' => $task['id'],'staff_id'=>session('user_data')['id']])->groupBy('task_activity_id')->countAllResults(),
                    'completed_activities' => $this->taskStaffActivityModel->where(['task_id' => $task['id'],'staff_id'=>session('user_data')['id'],'status' => 'Completed'])->groupBy('task_activity_id')->countAllResults(),
                    'priority'  => $task['priority'],
                    'status'    => $task['status'],
                    'overdue_date' => $task['overdue_date'],
                    'progress'  => $task['progress'],
                    'action'    => 0,//$task['action'],
                    'ducument'  => $task['image_url'],
                    'users'     => [],
                ];

                if (!empty($task['profileimg']) || !empty($task['name'])) {
                    $groupData[$taskId]['users'][] = [

                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                        'role'      => $task['role'],
                        'userPriority' => $task['userPriority'],
                    ];
                }

                $groupData[$taskId]['duration'] = $task['status'] == 'Completed'
                    ? human_duration($task['created_at'], $task['completed_at'])
                    : human_duration($task['created_at']);
            } else {
                $existingProfiles = array_column($groupData[$taskId]['users'], 'userId');
                if (!empty($task['name'])  && !in_array($task['userId'], $existingProfiles)) {
                    $groupData[$taskId]['users'][] = [

                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                        'role'      => $task['role'],
                        'userPriority' => $task['userPriority'],
                    ];
                }
            }
        }

        $tasks = array_values($groupData);
        return $this->response->setJSON([ 'success'=>true,'task' => $tasks]);
    }

    function view($id= false) {
        
        $id = decryptor($id) ?? NULL;

        if($id) {
            $participants = $this->taskassignModel->getParticipants($id);
           // print_r($participants);
        }
    }

    public function update_status()
    {
        $task_id = $this->request->getPost('task_id');
        $new_status = $this->request->getPost('new_status');

        if (!$task_id || !$new_status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }
        $task_id = decryptor( $task_id);
        $getTask = $this->taskModel->find($task_id);
        $tasks =  $this->taskassignModel->where('task_id',$task_id)->findAll();
        
        foreach($tasks as $tsk) {
            $notify = [
                'user_id' =>  $tsk['staff_id'],
                'type'    => 'new_task',
                'task_id'   => $task_id,
                'created_by' => session('user_data')['id'],
                'title'   => 'Task '.$getTask['title'].' Status Change ',
                'message' => 'Task '.$getTask['title'].' Status Change to '.ucwords(str_replace('_', ' ', $new_status)).' By '.session('user_data')['username']
            ];
             $this->notificationModel->insert($notify);
        }
      
        $updated = $this->taskModel->update($task_id, ['status' => $new_status]);
        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Task updated' : 'Failed to update task',
        ]);
    }

    function myTask() {
        $page = "My Tasks";
        $branches = $this->branchModel->where('status','active')->findAll();
        return view('admin/task/mytask',compact('page','branches'));
    }

    function notificationTask($taskId=false){
        $page = "Notifications";
        $notificationId = decryptor($taskId);

        $taskId = $this->notificationModel->where('id', $notificationId)->first()['task_id'] ?? null; 
        return view('admin/task/mytask',compact('page','taskId'));
    }

    public function delete($id)
    {
        $taskModel =  $this->taskModel;

        if(!hasPermission('','task_delete')) {
            return $this->response->setJSON(['status' => false,'msg'=>lang('Custom.accessDenied')]);
        }

        if ($taskModel->find(decryptor($id))) {
            //$taskModel->delete($id);
            return $this->response->setJSON(['status' => true,'msg' => 'Task deleted successfully!']);
        }

        return $this->response->setJSON(['status' => false, 'msg' => 'Task not found']);
    }
    
    public function start()
    {
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

        $taskId   = decryptor($this->request->getPost('id'));
        $loggedIn = session('user_data')['id'];
        $role     = session('user_data')['role'];

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
            ->where('task_id', $taskId)
            ->where('staff_id', $loggedIn)
            ->where('is_open', 1)
            ->first();
            

        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This task activity is already opened. You cannot open it again.'
            ]);
        }

        // Record only once
        $this->taskStaffActivityModel
            ->where('task_id', $taskId)
            ->where('staff_id', $loggedIn)
            ->set([
                'started_at' => date('Y-m-d H:i:s'),
                'started_by' => $loggedIn,
                'is_open'    => 1
            ])->update();

            $total_activities = $this->taskStaffActivityModel->where('task_id',$taskId)->groupBy('task_activity_id')->countAllResults();
            $completed_activities = $this->taskStaffActivityModel->where(['task_id'=> $taskId,'status' => 'completed'])->groupBy('task_activity_id')->countAllResults(); 
            $totalProgress = ($total_activities / $completed_activities ) * 100;
            if($totalProgress > 1 ) {
                $taskUpdate = [
                    'progress' => $totalProgress,
                    'status' => 'In_Progress'
                ];
                 $this->taskModel->where('id', $taskId)->set($taskUpdate)->update();
            }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Task activities started successfully'
        ]);
    }


}