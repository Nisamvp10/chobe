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
use App\Models\MastertaskModel;
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
    protected $mastertaskModel;
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
        $this->mastertaskModel = new MastertaskModel();
        
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
            //'taskmode'    => 'required',
        ];

        

        if (empty($taskId)) {
            $rules['taskmode'] = 'required';
            $rules['project'] = 'required';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors()
            ]);
        }

        // Prepare task data
        $masterTaskData = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'status'        => 'active',
            'tasktype'      => $this->request->getPost('taskmode'),
        ];
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
            'taskmode'      => $this->request->getPost('taskmode'),
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
        $masterActivities = $this->activityModel->where(['status'=> 'active','activity_type' => 1])->findAll();

        $staffs = $this->request->getPost('staff') ?? [];
        $roles  = $this->request->getPost('role') ?? [];


        if (!empty($taskId)) {
            // ---------------- UPDATE TASK ----------------
            //$this->mastertaskModel->update($taskId, $data);
            $getTask = $this->taskModel->where('id',$taskId)->get()->getRow();
            $data['taskmode'] = $getTask->taskmode;
            if (!$this->taskModel->update($taskId, $data)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Task update failed',
                        'errors'  => $this->taskModel->errors()
                    ]);
                }

                // 2️⃣ Task files
                if (!empty($taskFiles)) {
                    $taskFiles['task_id'] = $taskId;

                    if (!$this->taskImgModel->insert($taskFiles)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Task file upload failed',
                            'errors'  => $this->taskImgModel->errors()
                        ]);
                    }
                }

                // 3️⃣ Get task activities
                $allTaskActivities = $this->taskActivityModel
                    ->where('task_id', $taskId)
                    ->findAll();

                $allTaskActivityIds = array_column($allTaskActivities, 'id');

                // 4️⃣ Existing staff
                $existingAssignments = $this->taskassignModel
                    ->where('task_id', $taskId)
                    ->findAll();

                $existingStaffIds = array_column($existingAssignments, 'staff_id');

                // 5️⃣ Assign staff
                foreach ($staffs as $index => $staffId) {

                    $roleId = $roles[$index] ?? null;

                    if (in_array($staffId, $existingStaffIds)) {

                        // update role
                        $this->taskassignModel->where(['task_id'  => $taskId,'staff_id' => $staffId])->update(null, ['role' => $roleId]);

                    } else {

                        // new staff
                        if (!$this->taskassignModel->insert([
                            'task_id'  => $taskId,
                            'staff_id' => $staffId,
                            'role'     => $roleId,
                            'status'   => 'assigned'
                        ])) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => 'Staff assignment failed',
                                'errors'  => $this->taskassignModel->errors()
                            ]);
                        }

                        // assign activities
                        // foreach ($allTaskActivityIds as $taskActivityId) {
                        //     $this->taskStaffActivityModel->insert([
                        //         'task_activity_id' => $taskActivityId,
                        //         'staff_id'         => $staffId,
                        //         'status'           => 'pending'
                        //     ]);
                        // }

                        // notify
                        $this->notificationModel->insert([
                            'user_id'    => $staffId,
                            'task_id'    => $taskId,
                            'type'       => 'task_reassign',
                            'title'      => 'Task Assigned/Updated',
                            'created_by' => session('user_data')['id'] ?? null,
                            'message'    => 'Task updated and assigned to you by ' .
                                            (session('user_data')['username'] ?? 'system')
                        ]);
                    }
                }

                // 6️⃣ Completion check
                $assignedStaffs = $this->taskassignModel
                    ->where('task_id', $taskId)
                    ->findAll();

                $allDone = true;

                // foreach ($assignedStaffs as $staff) {

                //     $total = $this->taskStaffActivityModel
                //         ->where('staff_id', $staff['staff_id'])
                //         ->whereIn('task_activity_id', $allTaskActivityIds)
                //         ->countAllResults();

                //     $completed = $this->taskStaffActivityModel
                //         ->where('staff_id', $staff['staff_id'])
                //         ->whereIn('task_activity_id', $allTaskActivityIds)
                //         ->where('status', 'completed')
                //         ->countAllResults();

                //     if ($total == 0 || $completed != $total) {
                //         $allDone = false;
                //         break;
                //     }
                // }

                // OPTIONAL
                // if ($allDone) {
                //     $this->taskModel->update($taskId, ['status' => 'completed']);
                // }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Task updated successfully'
                ]);

        } else {
                $db->transStart();
            // 1️⃣ Get all active project units
                $projectUnits = $this->projectUnitModel->where('status', 1)->findAll();
                if (empty($projectUnits)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No active project units found'
                    ]);
                }

                // 2️⃣ Get master activities
                //$masterActivities = $this->taskActivityModel->where('status', 1)->findAll();
                 $masterActivities = $this->activityModel
                    ->where('status', 'active')
                    ->where('activity_type', 1)
                    ->findAll();


                if (empty($masterActivities)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No master activities found'
                    ]);
                }
                 $masterTskId = $this->mastertaskModel->insert( $masterTaskData,true);
                foreach ($projectUnits as $unit) {

                    /* ===============================
                    🔹 COLLECT PERMANENT STAFF
                    ================================ */
                    $staffs = [];

                    if (!empty($unit['allocated_to']) && $unit['allocated_type'] === 'permanently') {
                        $staffs[] = $unit['allocated_to'];
                    }

                    if (!empty($unit['assigned_to']) && $unit['assigned_type'] === 'permanently') {
                        $staffs[] = $unit['assigned_to'];
                    }

                    $staffs = array_unique($staffs);

                    if (empty($staffs)) {
                        continue; // skip project unit
                    }

                    /* ===============================
                    🔹 CREATE TASK (PER PROJECT UNIT)
                    ================================ */
                    $taskData = [
                        'title'           => 'Daily Verification Task',
                        'description'     => 'Auto generated daily verification task',
                        'project_unit_id' => $unit['id'],
                        'status'          => 'pending',
                        'created_by'      => session('user_data')['id'] ?? null,
                        'created_at'      => date('Y-m-d H:i:s')
                    ];
                    $data['project_unit'] = $unit['id'];

                    $newTaskId = $this->taskModel->insert($data, true);
                    $masterTaskData['created_by'] = session('user_data')['id'];
                  

                    if (!$newTaskId) {
                        continue;
                    }
                     $this->taskModel->update($newTaskId, ['created_from_template' => $masterTskId]);

                    /* ===============================
                    🔹 ASSIGN STAFF + ACTIVITIES
                    ================================ */
                    foreach ($staffs as $staffId) {

                        // Task assignment
                        $this->taskassignModel->insert([
                            'task_id'  => $newTaskId,
                            'staff_id' => $staffId,
                            'status'   => 'assigned'
                        ]);

                        // Task activities
                        foreach ($masterActivities as $act) {
                            $this->taskStaffActivityModel->insert([
                                'task_id'          => $newTaskId,
                                'task_activity_id' => $act['id'],
                                'staff_id'         => $staffId,
                                'status'           => 'pending',
                                'created_at'       => date('Y-m-d H:i:s')
                            ]);
                        }

                        // Notification
                        $this->notificationModel->insert([
                            'user_id'    => $staffId,
                            'task_id'    => $newTaskId,
                            'type'       => 'new_task',
                            'title'      => 'New Task Assigned',
                            'created_by' => session('user_data')['id'] ?? null,
                            'message'    => 'A daily verification task has been auto assigned'
                        ]);
                    }
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Transaction failed while creating tasks'
                    ]);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Daily tasks auto-created successfully for all project units'
                ]);
        }
    }

  public function autoAssignTasksWithStaff()
    {
        $today = date('Y-m-d');

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

        // 1️⃣ Fetch DAILY task templates
        $mode = $this->request->getPost('assignmentMode'); 
        $taskType = ($mode === 'permanent') ? 1 : 2;
        $templates = $this->taskModel->where(['recurrence' => 'daily','taskmode'   => $taskType])->where('next_run_date <=', $today)->groupBy('created_from_template')->findAll();
//echo $this->taskModel->getlastQuery();exit();
        if (empty($templates)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'No templates to process'
            ]);
        }

        // 2️⃣ Fetch active project units
        $projectUnits = $this->projectUnitModel->where('status', 1)->findAll();
        $taskCreated = false; 

        foreach ($templates as $template) {

            foreach ($projectUnits as $unit) {

                // 3️⃣ Check if THIS unit already has task today
                $exists = $this->taskModel
                    ->where('created_from_template', $template['id'])
                    ->where('project_unit', $unit['id'])
                    ->where('DATE(created_at)', $today)
                    ->where('taskmode', $taskType)
                    ->countAllResults();
                    //echo $this->taskModel->getlastQuery();

                if ($exists > 0) {
                    continue; // Skip only THIS unit
                }

                // 4️⃣ Create new task
                $newTaskId = $this->taskModel->insert([
                    'project_id'            => $template['project_id'],
                    'project_unit'          => $unit['id'],
                    'title'                 => $template['title'],
                    'description'           => $template['description'],
                    'branch'                => $template['branch'],
                    'overdue_date'          => $template['overdue_date'],
                    'priority'              => $template['priority'],
                    'status'                => 'Pending',
                    'progress'              => 0,
                    'taskmode'              => $taskType,
                    'recurrence'            => 'daily',
                    'next_run_date'         => date('Y-m-d', strtotime('+1 day')),
                    'created_from_template' => $template['created_from_template'],
                    'created_at'            => date('Y-m-d H:i:s'),
                ]);

                if (!$newTaskId) {
                    continue;
                }

                $taskCreated = true; 

                // 5️⃣ Create task activities
                $masterActivities = $this->activityModel
                    ->where('status', 'active')
                    ->where('activity_type', 1)
                    ->findAll();

                foreach ($masterActivities as $act) {
                    $this->taskActivityModel->insert([
                        'task_id'     => $newTaskId,
                        'activity_id' => $act['id'],
                        'created_at'  => date('Y-m-d H:i:s'),
                    ]);
                }

                // 6️⃣ Collect permanently allocated staff
                $staffIds = [];

                if (!empty($unit['allocated_to']) && $unit['allocated_type'] === 'permanently') {
                    $staffIds[] = $unit['allocated_to'];
                }

                if (!empty($unit['assigned_to']) && $unit['assigned_type'] === 'permanently') {
                    $staffIds[] = $unit['assigned_to'];
                }

                $staffIds = array_unique($staffIds);

                if (empty($staffIds)) {
                    continue;
                }

                // 7️⃣ Assign staff + activities
                foreach ($staffIds as $staffId) {

                    $this->taskassignModel->insert([
                        'task_id'   => $newTaskId,
                        'staff_id'  => $staffId,
                        'status'    => 'assigned',
                        'created_at'=> date('Y-m-d H:i:s')
                    ]);

                    foreach ($masterActivities as $act) {
                        $this->taskStaffActivityModel->insert([
                            'task_id'          => $newTaskId,
                            'task_activity_id' => $act['id'],
                            'staff_id'         => $staffId,
                            'status'           => 'pending',
                            'progress'         => 'pending',
                        ]);
                    }

                    $this->notificationModel->insert([
                        'user_id'    => $staffId,
                        'task_id'    => $newTaskId,
                        'type'       => 'new_task',
                        'title'      => 'New Task',
                        'created_by' => session('user_data')['id'],
                        'message'    => 'A daily task has been assigned to you',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        // ⭐ FINAL MESSAGE CONTROL
        if (!$taskCreated) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'All tasks are already assigned for today. Please try tomorrow.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Daily tasks created successfully'
        ]);
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

            
            $groupData = [];

            foreach ($alltask as &$task) {

                $taskId = $task['id'];
                 $allusers = $this->staffModal
                        ->select('users.id, users.name, users.profileimg')
                        ->join('user_position u', 'users.position_id = u.id', 'left')
                        ->where('users.status', 'approved')
                        ->where('users.store_id', $task['clientId'])
                        ->where('users.booking_status', 1)
                        ->where('users.role !=', 1)
                        ->where('u.type !=', 1)
                        ->findAll();

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
                        'progress'  => $task['progress'],
                        'created'   => date('d-m-Y',strtotime($task['created_at'])),
                        'allUsers'  => $allusers,
                        'overdue_date' => $task['next_run_date'],
                        'polarisCode'   => $task['polaris_code'],
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
        $alltasks = $this->taskModel->getMytask('','',$notifiytask,$filter); 
       
        
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
                    'duedate'   => $task['next_run_date'],
                    'polarisCode'   => $task['polaris_code'],
                    'ducument'  => $task['image_url'],
                    'created'   => date('d-m-Y',strtotime($task['created_at'])),
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

    public function allocatedstaffs($id=false) {
        if($id) {
            // $builderClint = $this->projectUnitModel->select('allocated_to,assigned_to,id,client_id')->where('id',$id)->get()->getRow();
            // $users = $this->staffModal->select('name,id')->where('store_id',$builderClint->client_id)->get()->getResult();
            $data = $this->projectUnitModel
                    ->select('
                        project_unit.store,project_unit.allocated_to,project_unit.assigned_to,project_unit.regional_manager_id,project_unit.manager_id,project_unit.allocated_type,
                        project_unit.assigned_type,
                        alloc.name as allocated_name,
                        assign.name as assigned_name,
                        u.name,u.id
                    ')
                    ->join('users as alloc', 'alloc.id = project_unit.allocated_to', 'left')
                    ->join('users as assign', 'assign.id = project_unit.assigned_to', 'left')
                     ->join('users as u', 'u.store_id = project_unit.client_id', 'left')
                    ->where('project_unit.id', $id)
                    ->get()
                    ->getresult();
                    $users =[] ;
                    if($data) {
                        foreach($data as &$row) {
                            if($row->regional_manager_id != $row->id && $row->manager_id != $row->id) {
                                $users[] = [
                                    'isAssigned' => ($row->allocated_to == $row->id && $row->allocated_type == 'permanently' ? true : ''),
                                    'isTemp'      => ($row->assigned_to == $row->id && $row->assigned_type == 'permanently' ? true : ''),
                                    'userId'    => $row->id,
                                    'name'      => $row->name
                                ];
                            }
                        }
                    }

              
            return $this->response->setJSON([
                'success' => true,
                'result' => $users
            ]);
                
        }
    }
}