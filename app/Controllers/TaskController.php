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
class TaskController extends Controller {

    protected $branchModel;
    protected $taskModel;
    protected $taskassignModel;
    protected $notificationModel;
    protected $taskImgModel;
    protected $uploadImg;
    protected $projects;
    function __construct() {
        $this->branchModel = new BranchesModel();
        $this->taskModel = new TaskModel();
        $this->taskassignModel = new AssigntaskModel();
        $this->notificationModel = new NotificationModel();
        $this->taskImgModel = new TaskimagesModel();
        $this->uploadImg = new UploadImages();
        $this->projects = new ProjectsModel();
        
    }

    function index($taskStatus= false) {

        $page = "Tasks";
        $branches = $this->branchModel->where('status','active')->findAll();
        $taskStatus = $taskStatus;
        return view('admin/task/index',compact('page','branches','taskStatus'));
    }

    function create() {

        $page = "Create New Task";
        $branches = $this->branchModel->where('status','active')->findAll();
        $projects = $this->projects->where('is_active',1)->findAll();
        return view('admin/task/create',compact('page','branches','projects'));
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
            'branch' => 'required',
            'priority' => 'required',
            'duedate' => 'required',
            
        ];

        (empty($taskId) ? $rules['project'] = 'required' : '' );

        if(!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false , 'errors' => $this->validator->getErrors()]);
        }
        $validStatus = false;
        $validMsg = '';

        $data = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'overdue_date'  => $this->request->getPost('duedate'),
            'priority'      => $this->request->getPost('priority'),
            'branch'        => $this->request->getPost('branch'),
            'project_id'       => $this->request->getPost('project'),
            'status'        => 1,
        ];
       

        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadResult = $this->uploadImg->uploadimg($file, 'taskfiles');
            $image = json_decode($uploadResult, true);
             $taskFiles = [
                'image_url'   => base_url($image['file']),
                'file_ext'    => $image['file_ext']
                ];
        } else {
            $image = ['status' => false];
        }

       
       
        if ($taskId) {
          
            $data['progress'] = $this->request->getPost('progress');
            $data['status'] = $this->request->getPost('status');
            $update = $this->taskModel->update($taskId,$data);
            $staffs = $this->request->getPost('staff');
            $roles  = $this->request->getPost('role') ?? [];
            $personPriority  = $this->request->getPost('personpriority') ?? [];
           
            foreach ($staffs as $index => $staffId) {
                $roleId = $roles[$index];
                $personPriorityId =  $personPriority[$index];

                // Check if assignment exists
                $existing = $this->taskassignModel
                                ->where('task_id', $taskId)
                                ->where('staff_id', $staffId)
                                ->first();

                if ($existing) {
                    // Update only if role is different
                    if ($existing['role'] != $roleId) {
                        $this->taskassignModel->update($existing['id'], [
                            'role' => $roleId
                        ]);
                    }
                     if ($existing['priority'] != $personPriorityId) {
                        $this->taskassignModel->update($existing['id'], [
                            'priority' => $personPriorityId
                        ]);
                    }
                } else {
                    // Insert new assignment
                    $this->taskassignModel->insert([
                        'task_id'  => $taskId,
                        'staff_id' => $staffId,
                        'role'     => $roleId,
                        'priority' => $personPriorityId
                    ]);
                }
            }

            $validStatus = $update;
            $validMsg =  $update ? 'Task updated' : 'Failed to update task';
        }else{
                $personPriority  = $this->request->getPost('personpriority');
                $staffs = $this->request->getPost('staff');
                if(!empty($staffs)) {
                        if ($taskId = $this->taskModel->insert($data)) {

                        $taskFiles['task_id'] = $taskId;
                        $this->taskImgModel->insert($taskFiles);
                    
                        $role =   $this->request->getPost('role');
                        $personPriority  = $this->request->getPost('personpriority');
                
                            foreach ($staffs as $index => $staff) {
                                $assign = [
                                    'task_id'  => $taskId,
                                    'staff_id' => $staff,
                                    'role'     => $role[$index], 
                                    'priority' =>  $this->request->getPost('priority'),// $personPriority[$index], 
                                ];
                                $notify = [
                                    'user_id' =>  $staff,
                                    'task_id'   => $taskId,
                                    'type'    => 'new_task',
                                    'title'   => 'New Task',
                                    'created_by' => session('user_data')['id'],
                                    'message' => 'A new Task has been created by .'.session('user_data')['username']
                                ];
                                
                                $this->taskassignModel->insert($assign);
                                $this->notificationModel->insert($notify);
                            }
                            $validStatus = true;
                            $validMsg = 'New Task Added Successfully';
                    
                    
                }else {
                    $validMsg = lang('Custom.formError');
                }
            } else{
                $validMsg = 'Please select at least one participant for the task ';
            }
        }
        return $this->response->setJSON(['success' => $validStatus,'message' => $validMsg]);
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
        $alltask = $this->taskModel->getTasks('','',$filter,$searchInput,$startDate,$endDate); // or ->findAll()
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
                    'priority'  => $task['priority'],
                    'status'    => $task['status'],
                    'overdue_date' => $task['overdue_date'],
                    'progress'  => $task['progress'],
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
                $existingProfiles = array_column($groupData[$taskId]['users'], 'img');
                if (!empty($task['name']) && count($groupData[$taskId]['users']) < 8 && !in_array($task['profileimg'], $existingProfiles)) {
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
        $notifiytask = $this->request->getGet('notifiytask');
        $alltasks = $this->taskModel->getMytask('','',$notifiytask); // or ->findAll()
        $groupData = [];
        foreach ($alltasks as &$task) {
            $taskId = $task['id'];

            if (!isset($groupData[$taskId])) {
                $groupData[$taskId] = [

                    'id'        => encryptor($task['id']),
                    'title'     => $task['title'],
                    'storeId'   => $task['store'],
                    'description' => $task['description'],
                    'branch_name' => $task['branch_name'],
                    'priority'  => $task['priority'],
                    'status'    => $task['status'],
                    'overdue_date' => $task['overdue_date'],
                    'progress'  => $task['progress'],
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
                $existingProfiles = array_column($groupData[$taskId]['users'], 'img');
                if (!empty($task['name']) && count($groupData[$taskId]['users']) < 8 && !in_array($task['profileimg'], $existingProfiles)) {
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
    
}