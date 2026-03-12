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
use App\Models\ActivitycommentsModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    protected $activitycommentsModel;
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
        $this->activitycommentsModel = new ActivitycommentsModel();
    }

    function index($taskStatus= false) {

        $page = "Tasks";
        $branches = $this->branchModel->where('status','active')->findAll();
        $projects = $this->projects->where('is_active',1)->findAll();
        $projectUnits = $this->projectUnitModel->where('status',1)->findAll();
        $taskStatus = $taskStatus;
        $masterTasks = $this->mastertaskModel->where('status','active')->findAll();
        return view('admin/task/index',compact('page','branches','taskStatus','projects','projectUnits','masterTasks'));
    }

    function create() {

        $page = "Activate Task";
        $branches = $this->branchModel->where('status','active')->findAll();
        $projects = $this->projects->where('is_active',1)->findAll();
        $staffs =  $this->staffModal->where('role !=',1)->findAll();
        $projectUnits = $this->projectUnitModel->where('status',1)->findAll();
        //return view('admin/task/create',compact('page','branches','projects','staffs','projectUnits'));
        $masterTasks = $this->mastertaskModel->where('status','active')->findAll();
        return view('admin/task/assign',compact('page','branches','projects','staffs','projectUnits','masterTasks'));
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
            // 'title'       => 'required',
            // 'description' => 'required|min_length[3]',
            // 'priority'    => 'required',
            //'taskmode'    => 'required',
            'masetrTask'    => 'required',
        ];

        //taskCreate
        // if (empty($taskId)) {
        //     $rules['taskmode'] = 'required';
        //     $rules['project'] = 'required';
        // }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors()
            ]);
        }
        $mastertaskId = $this->request->getPost('masetrTask');
        $masterTask = $this->mastertaskModel->where('id',$mastertaskId)->get()->getRow();
        // Prepare task data
        // $masterTaskData = [
        //     'title'         => $this->request->getPost('title'),
        //     'description'   => $this->request->getPost('description'),
        //     'status'        => 'active',
        //     'tasktype'      => $this->request->getPost('taskmode'),
        // ];
        
        if (!empty($masterTask)) {
                $masterTaskId = $masterTask->id;
                $data = [
                'title'         => $masterTask->title,
                'description'   => $masterTask->description,
                'overdue_date'  => $this->request->getPost('duedate') ?: null,
                'priority'      => 1,//high
                'branch'        => 'all',
                'project_unit'  => $this->request->getPost('projectUnit') ?: null,
                'project_id'    => $masterTask->project_unit_id ?: null,
                'status'        => $this->request->getPost('status') ?? 'Pending',
                'recurrence'    => 'daily',
                'taskmode'      => $masterTask->tasktype,
                //'next_run_date' => date('Y-m-d', strtotime('+1 day')),
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
        $masterActivities = $this->activityModel->where(['status'=> 'active','task_id'=>$masterTaskId,'activity_type' => 1])->findAll();

        $staffs = $this->request->getPost('staff') ?? [];
        $roles  = $this->request->getPost('role') ?? [];


        if (!empty($taskId)) {

            $data = [
                'description'   => $this->request->getPost('description'),
                'priority'      => $this->request->getPost('priority'),
                //'status'        => $this->request->getPost('status') ?? 'Pending',
            ];

            // ---------------- UPDATE TASK ----------------
            //$this->mastertaskModel->update($taskId, $data);
            $getTask = $this->taskModel->where('id',$taskId)->get()->getRow();
            $data['taskmode'] = $getTask->taskmode;
            $data['task_gen_date'] = (
                $masterTask->tasktype == 1
                    ? date('Y-m-d', strtotime($getTask->task_gen_date))
                    : date('Y-m-d', strtotime('+1 day', strtotime($getTask->task_gen_date)))
            );
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
                //$allTaskActivities = $this->taskActivityModel->where('task_id', $taskId)->findAll();
                $teplateId = $getTask->created_from_template;
                $allTaskActivities = $this->activityModel->where('status', 'active')->where('activity_type', 1)->where('task_id', $teplateId)->findAll();
               
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
                        foreach ($allTaskActivityIds as $taskActivityId) {
                            //check the activity is completed or not 
                            $getTaskActivity = $this->taskStaffActivityModel->where('task_activity_id', $taskActivityId)->where('task_id', $taskId)->first();
                            $activityStatus = 1;
                            $isprogress =1;
                            if ($getTaskActivity) {
                                if($getTaskActivity['status'] == 'completed'){
                                    $activityStatus = 2;
                                }
                                if($getTaskActivity['progress'] == 'completed'){
                                    $isprogress = 2;
                                }
                            }
                            $this->taskStaffActivityModel->insert([
                                'task_id' => $taskId,
                                'task_activity_id' => $taskActivityId,
                                'staff_id'         => $staffId,
                                'status'           => $activityStatus,
                                'progress'         => $isprogress
                            ]);
                        }

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

                    // Get project
                    $projectId = $this->projects
                        ->where('id', $masterTask->project_unit_id)
                        ->first();


                    if (!$projectId) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Project not found'
                        ]);
                    }

                    // Get active project units
                    $projectUnits = $this->projectUnitModel
                        ->where('client_id', $projectId['client_id'])
                        ->where('status', 1)
                        ->findAll(); //120
                        

                    if (empty($projectUnits)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'No active project units found'
                        ]);
                    }

                    // Get master activities
                    $masterActivities = $this->activityModel->where('status', 'active')->where('activity_type', 1)->where('task_id', $masterTaskId)->findAll(); //2

                    if (empty($masterActivities)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'No master activities found'
                        ]);
                    }

                    $masterTskId = $masterTaskId;

                    // Generate date once
                    $taskGenDate = ($masterTask->tasktype == 1)
                        ? date('Y-m-d', strtotime('-1 day'))
                        : date('Y-m-d');
                    //count total project units
                    $totalProjectUnits = count($projectUnits); // project units is 119 
                    foreach ($projectUnits as $unit) {

                        

                        /* 🔹 Collect Permanent Staff */
                        $staffs = [];

                        if (!empty($unit['allocated_to'])) {
                            $staffs[] = $unit['allocated_to'];
                        }

                        if (!empty($unit['assigned_to'])) {
                            $staffs[] = $unit['assigned_to'];
                        }

                        $staffs = array_unique($staffs);

                        if (empty($staffs)) {
                            continue;
                        }
                        /* 🔴 DUPLICATE CHECK (IMPORTANT) */
                        $existingTask = $this->taskModel
                            ->where('created_from_template', $masterTskId)
                            ->where('project_unit', $unit['id'])
                            ->where('task_gen_date', $taskGenDate)
                            ->first();

                 
                        //check projectunit duplication like create_template_id and project_unit same show message 
                        $existingtaskProjectUnit = $this->taskModel
                            ->where('created_from_template', $masterTskId)
                            ->where('project_unit', $unit['id'])
                            ->first();
                       
                        if(!empty($existingtaskProjectUnit) && $existingtaskProjectUnit['created_from_template'] == $masterTskId && $existingtaskProjectUnit['project_unit'] == $unit['id']){
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => 'Task already exists click to auto assign'
                            ]);
                            continue;
                        }

                        if ($existingTask) {
                           return $this->response->setJSON([
                                'success' => false,
                                'message' => 'Task already exists'
                            ]);
                        }

                        /* 🔹 Create Task */
                        $data['project_unit'] = $unit['id'];
                        $data['task_gen_date']   = ($masterTask->tasktype == 1 ? date('Y-m-d', strtotime('-1 day')) : date('Y-m-d'));
                        $data['next_run_date'] = ($masterTask->tasktype == 1 ? date('Y-m-d') : date('Y-m-d'));
                        
                        $newTaskId = $this->taskModel->insert($data, true);

                        if (!$newTaskId) {
                            continue;
                        }
                        $this->taskModel->update($newTaskId, ['created_from_template' => $masterTskId]);

                        /* 🔹 Assign Staff + Activities */
                        foreach ($staffs as $staffId) {

                            $this->taskassignModel->insert([
                                'task_id'  => $newTaskId,
                                'staff_id' => $staffId,
                                'status'   => 'assigned'
                            ]);

                            // foreach ($masterActivities as $act) {
                            //     $this->taskStaffActivityModel->insert([
                            //         'task_id'          => $newTaskId,
                            //         'task_activity_id' => $act['id'],
                            //         'staff_id'         => $staffId,
                            //         'status'           => 'pending',
                            //         'created_at'       => date('Y-m-d H:i:s')
                            //     ]);
                            // }
                            foreach ($masterActivities as $act) {
                                //one staff $act 23 activities 

                                $insertData = [
                                    'task_id'          => $newTaskId,
                                    'task_activity_id' => $act['id'],
                                    'staff_id'         => $staffId,
                                    'status'           => 'pending',
                                    'created_at'       => date('Y-m-d H:i:s')
                                ];

                                $result = $this->taskStaffActivityModel->insert($insertData);

                                if (!$result) {
                                    echo "<pre>";
                                    print_r($this->taskStaffActivityModel->errors());
                                    exit;
                                }
                            }
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
        }else{
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No Master Task found'
            ]);
        }
    }

    public function autoAssignTasksWithStaff()
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

        $mode = $this->request->getPost('assignmentMode');
        $taskType = ($mode === 'permanent') ? 1 : 2;

        $taskGenDate = date('Y-m-d', strtotime('-1 day'));
        $today       = date('Y-m-d');
        $nextRunDate = $today;

        $messages = [];
        $createdCount = 0;
        $skipCount = 0;

        // Get templates
        $templates = $this->taskModel
            ->where('recurrence', 'daily')
            ->where('taskmode', $taskType)
            ->where('next_run_date <=', $today)
            ->findAll();

        if (!$templates) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No templates found'
            ]);
        }

        foreach ($templates as $template) {

            if (empty($template['created_from_template'])) {
                continue;
            }

            $project = $this->projects->find($template['project_id']);
            if (!$project) {
                continue;
            }

            // Get units
            $units = $this->projectUnitModel
                ->where('client_id', $project['client_id'])
                ->where('status', 1)
                ->findAll();

            // Get template activities once
            $masterActivities = $this->activityModel
                ->where('status', 'active')
                ->where('task_id', $template['created_from_template'])
                ->where('activity_type', 1)
                ->findAll();

            foreach ($units as $unit) {

                // Duplicate check
                $exists = $this->taskModel
                    ->where([
                        'created_from_template' => $template['created_from_template'],
                        'project_id' => $template['project_id'],
                        'project_unit' => $unit['id'],
                        'task_gen_date' => $taskGenDate,
                        'taskmode' => $taskType
                    ])->groupBy('project_unit')
                    ->first();

                if ($exists) {
                    $skipCount++;
                    $messages[] = "Skipped unit {$unit['id']} (already exists)";
                    continue;
                }

                // Create task
                $newTaskId = $this->taskModel->insert([
                    'project_id' => $template['project_id'],
                    'project_unit' => $unit['id'],
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'branch' => $template['branch'],
                    'overdue_date' => $template['overdue_date'],
                    'priority' => $template['priority'],
                    'status' => 'Pending',
                    'task_gen_date' => $taskGenDate,
                    'progress' => 0,
                    'taskmode' => $taskType,
                    'recurrence' => 'daily',
                    'next_run_date' => $nextRunDate,
                    'created_from_template' => $template['created_from_template'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if (!$newTaskId) {
                    continue;
                }

                $createdCount++;

                // Insert activities
                foreach ($masterActivities as $act) {

                    $this->taskActivityModel->insert([
                        'task_id' => $newTaskId,
                        'activity_id' => $act['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                // Collect staff
                $staffIds = [];

                if (!empty($unit['allocated_to']) && $unit['allocated_type'] === 'permanently') {
                    $staffIds[] = $unit['allocated_to'];
                }

                if (!empty($unit['assigned_to'])) {
                    $staffIds[] = $unit['assigned_to'];
                }

                $staffIds = array_unique($staffIds);

                foreach ($staffIds as $staffId) {

                    $this->taskassignModel->insert([
                        'task_id' => $newTaskId,
                        'staff_id' => $staffId,
                        'status' => 'assigned',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);

                    foreach ($masterActivities as $act) {

                        $this->taskStaffActivityModel->insert([
                            'task_id' => $newTaskId,
                            'task_activity_id' => $act['id'],
                            'staff_id' => $staffId,
                            'status' => 'pending',
                            'progress' => 'pending',
                        ]);
                    }

                    // Notification
                    // $this->notificationModel->insert([
                    //     'user_id' => $staffId,
                    //     'task_id' => $newTaskId,
                    //     'type' => 'new_task',
                    //     'title' => 'New Task',
                    //     'created_by' => session('user_data')['id'],
                    //     'message' => 'A daily task has been assigned to you',
                    //     'created_at' => date('Y-m-d H:i:s')
                    // ]);
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Task process completed',
            'created_tasks' => $createdCount,
            'skipped_tasks' => $skipCount,
            'logs' => $messages
        ]);
    }
 private function newtaskAssign($taskType) {
         $masterTasks = $this->mastertaskModel->where(['status'=>'active','tasktype'=>$taskType])->findAll();
         $projectUnits = $this->projectUnitModel->where('status', 1)->findAll();
         foreach ($masterTasks as $masterTask) {
            foreach ($projectUnits as $unit) {
              
                $taskId=$this->taskModel->insert([
                    'project_id'            => $masterTask['id'],
                    'project_unit'          => $unit['id'],
                    'title'                 => $masterTask['title'],
                    'description'           => $masterTask['description'],
                    'branch'                => 'all',
                    'overdue_date'          => date('Y-m-d', strtotime('+1 day')),
                    'priority'              =>1,
                    'status'                => 'Pending',
                    'progress'              => 0,
                    'taskmode'              => $taskType,
                    'recurrence'            => 'daily',
                    'next_run_date'         => date('Y-m-d', strtotime('+1 day')),
                    'created_from_template' => $masterTask['id'],
                    'created_at'            => date('Y-m-d H:i:s'),
                ],true);

                 $masterActivities = $this->activityModel
                    ->where(['status'=>'active','activity_type'=>1,'task_id'=>$masterTask['id']])
                    ->findAll();
                    foreach ($masterActivities as $act) {
                        $this->taskActivityModel->insert([
                            'task_id'   => $taskId,
                            'activity_id' => $act['id'],
                            'created_at'=> date('Y-m-d H:i:s')
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
                        'task_id'   => $taskId,
                        'staff_id'  => $staffId,
                        'status'    => 'assigned',
                        'created_at'=> date('Y-m-d H:i:s')
                    ]);

                    foreach ($masterActivities as $act) {
                        $this->taskStaffActivityModel->insert([
                            'task_id'          => $taskId,
                            'task_activity_id' => $act['id'],
                            'staff_id'         => $staffId,
                            'status'           => 'pending',
                            'progress'         => 'pending',
                        ]);
                    }

                    $this->notificationModel->insert([
                        'user_id'    => $staffId,
                        'task_id'    => $taskId,
                        'type'       => 'new_task',
                        'title'      => 'New Task',
                        'created_by' => session('user_data')['id'],
                        'message'    => 'A daily task has been assigned to you',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }


            }
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
            $limit =$this->request->getGet('taskProject') ?? 50;
            $offset = $this->request->getGet('offset') ?? 0;
            $alltask = $this->taskModel->getTasks('','',$filter,$searchInput,$startDate,$endDate,$taskProject,'',$limit,$offset); // or ->findAll()
            //echo $this->taskModel->getLastQuery();
            $groupData = [];
            $pendingTasks = 0;
            $completedTasks = 0;

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
                       //count pending and completed task
                       if($task['status'] == 'Pending'){
                           $pendingTasks++;
                       }
                       if($task['status'] == 'Completed'){
                           $completedTasks++;
                       }

                    $groupData[$taskId] = [

                        'id'        => encryptor($task['id']),
                        'title'     => $task['title'],
                        'template_id'   => $task['created_from_template'],
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
                        'created'   => date('d-m-Y',strtotime($task['task_gen_date'])),
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

            return $this->response->setJSON([ 'success'=>true,'task' => $tasks,'pendingTasks' => $pendingTasks,'completedTasks' => $completedTasks]);
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
        $pendingTasks = 0;
        $completedTasks = 0;
        
        $groupData = [];
        foreach ($alltasks as &$task) {
            $taskId = $task['id'];
            if($task['status'] == 'Pending'){
                $pendingTasks++;
            }
            if($task['status'] == 'Completed'){
                $completedTasks++;
            }

            if (!isset($groupData[$taskId])) {
                  $activityTasksAssignModel->where(['activity_id'=> $task['id'],'staff_id'=>session('user_data')['id']])->countAllResults();
                  $this->taskActivityModel->getMytaskCount($task['id']);
                
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
                    'created'   => date('d-m-Y',strtotime($task['task_gen_date'])),
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
        return $this->response->setJSON([ 'success'=>true,'task' => $tasks,'completedTasks'=>$completedTasks,'pendingTasks' =>$pendingTasks]);
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
            $taskModel->update(decryptor($id), ['tasktype' => 2]);
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

    function getActivities() {
        $id = $this->request->getPost('id');
    // $activities = $this->activityModel->where('task_id', $id)->findAll();
    $activities = $this->activityModel->getactivityBymastarTask($id);
        return $this->response->setJSON([
            'success' => true,
            'activities' => $activities
        ]);
    }

    function groupActivityTaskComplete() {
        $page = (!haspermission('','create_task')) ? lang('Custom.accessDenied') : 'Group Activity Task ';
        if(!haspermission('','create_task')) {
            $route = view('admin/pages-error-404',compact('page'));
        } else {
           $masterTasks = $this->mastertaskModel->where('status','active')->findAll();
            $route =  view('admin/task/group_activity_task_complete',compact('page','masterTasks'));
        }
        return $route;
    }

    public function saveCommentGroupActivities(){
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }

        if(!haspermission('','create_task')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.permissionDenied')
            ]);
        }

        $rules = [
            'masetrTask' => 'required',
            //'activities' => 'required',
          //  'comment' => 'required',
            'date' => 'required',
        ];

        if(!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        $file = $this->request->getFile('file');
        if (!$file->isValid() || $file->getExtension() === '') {
            return $this->response->setJSON(['success' =>false,'errors' => ['file' => 'Please upload a valid Excel file' ]]);
        }

        $ext = $file->getClientExtension();
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return $this->response->setJSON(['success' =>false,'errors' => [ 'file' => 'Only .xls or .xlsx files allowed'] ]);
        }
        

        $masterTask = $this->request->getPost('masetrTask');
        //$activityId = $this->request->getPost('activities');
       // $comments = $this->request->getPost('comment');
        $taskGenDate = $this->request->getPost('date');

        $filePath = $file->getTempName();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        //$rows = $sheet->toArray();
        $rows = $sheet->toArray(null, true, true, true);

        $taskInfo = $this->taskModel->where(['created_from_template'=>$masterTask,'task_gen_date'=>$taskGenDate])->get()->getResult();
       // the excel file mentioned project unit id and remrt (comment )
        $invalidCode = [];
        $emptyErrors = [];
        $rowNumber   = 1;
       if(!empty($rows)) {
        foreach($rows as $row) {
           $oracleCode = trim($row['A'] ?? '');
           $activityId = trim($row['B'] ?? '');
           $remart     = trim($row['C'] ?? '');

            /* 🔴 CHECK EMPTY POLARIS */
                if ($oracleCode === '') {
                    $emptyErrors[] = "Row {$rowNumber}: Oracle Code is empty";
                    $rowNumber++;
                    continue;
                }

                if ($activityId === '') {
                    $emptyErrors[] = "Row {$rowNumber}: Activity ID is empty";
                    $rowNumber++;
                    continue;
                }

                /* 🔴 CHECK EMPTY REMARK */
                if ($remart === '') {
                    $emptyErrors[] = "Row {$rowNumber}: Remark is empty";
                    $rowNumber++;
                    continue;
                }
                
            

                $projectUnit = $this->projectUnitModel->where('oracle_code', $oracleCode)->get()->getRow();

                if ($projectUnit) {
                    $projectUnitId = $projectUnit->id;
                    $taskInfo = $this->taskModel->where(['created_from_template'=>$masterTask,'task_gen_date'=>$taskGenDate,'project_unit'=>$projectUnitId])->get()->getResult();
                    if($taskInfo) {
                        foreach($taskInfo as $task) {

                            
                            $taskId = $task->id;
                            $activity = $this->taskStaffActivityModel->where(['task_activity_id'=>$activityId,'task_id'=>$taskId])->get()->getRow(); //task_activity_id
                            //
                            $this->taskStaffActivityModel->where(['task_activity_id'=> $activityId,'task_id' => $taskId])->set([
                                    'completed_at' => date('Y-m-d H:i:s'),
                                    'complated_by' => session('user_data')['id'],
                                    'status'    => 'completed',
                                    'progress'  => 'completed',
                                ])->update();
                                $commtns = [
                                        'task_id' => $taskId,
                                        'user_id' => session('user_data')['id'] ?? null,
                                        'activity_id' => $activityId,
                                        'comment' => $remart,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'status'	    => 1,
                                        'created_by'	=> session('user_data')['id'],
                                ];
                        

                            /* 🔹 UPDATE TASK PROGRESS */
                            $totalActivities = $this->taskStaffActivityModel->where('task_id', $taskId)->groupBy('task_activity_id')->countAllResults();

                            $completedActivities = $this->taskStaffActivityModel->where(['task_id' => $taskId,'status'  => 'completed'])->groupBy('task_activity_id')->countAllResults();

                            $progress = ($totalActivities > 0) ? ($completedActivities / $totalActivities) * 100 : 0;

                           
                            $taskUpdate = [
                                'progress' => $progress,
                                'status'   => ($progress >= 100) ? 'Completed' : 'Pending'
                            ];

                            $this->taskModel->update($taskId, $taskUpdate);
                            $this->activitycommentsModel->insert($commtns);
                           // echo $this->taskModel->getLastQuery(); 
                        }
                       
                    }

                } else {
                     $invalidCode[] = "Row {$rowNumber}: Invalid Oracle Code ({$oracleCode})";
                }
             $rowNumber++;
                      
            }
            /* 🔴 RETURN ERRORS IF ANY */
            if (!empty($emptyErrors) || !empty($invalidCode)) {
                return $this->response->setJSON([
                    'success' => false,
                    //'message' => 'Validation errors found',
                    'msg_errors'  => array_merge($emptyErrors, $invalidCode)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data Inserted Successfully'
            ]);
       }
       else{
            return $this->response->setJSON([
                'success'   => false,
                'message'   => 'Data Not Found'
            ]);
       }
    }
}