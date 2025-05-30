<?php
namespace App\Controllers;

use App\Models\AssigntaskModel;
use CodeIgniter\Controller;
use App\Models\BranchesModel;
use App\Models\TaskModel;
use App\Models\NotificationModel;
class TaskController extends Controller {

    protected $branchModel;
    protected $taskModel;
    protected $taskassignModel;
    protected $notificationModel;

    function __construct() {
        $this->branchModel = new BranchesModel();
        $this->taskModel = new TaskModel();
        $this->taskassignModel = new AssigntaskModel();
        $this->notificationModel = new NotificationModel();
    }

    function index() {

        $page = "Tasks";
        $branches = $this->branchModel->where('status','active')->findAll();
        return view('admin/task/index',compact('page','branches'));
    }

    function create() {

        $page = "Create New Task";
        $branches = $this->branchModel->where('status','active')->findAll();
        return view('admin/task/create',compact('page','branches'));
    }

    function save () {

        if(!haspermission('','create_task')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.invalidRequest')]);
        }

        $rules = [
            'title' => 'required',
            'description' => 'required|min_length[3]',
            'branch' => 'required',
            'priority' => 'required',
            'duedate' => 'required'
        ];

        if(!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false , 'errors' => $this->validator->getErrors()]);
        }
        $validStatus = false;
        $validMsg = '';

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'overdue_date' => $this->request->getPost('duedate'),
            'priority' => $this->request->getPost('priority'),
            'branch'   => $this->request->getPost('branch'),
            'status'  => 1,
        ];

        
        
        $taskId = decryptor($this->request->getPost('taskId'));
        if ($taskId) {
            $data['progress'] = $this->request->getPost('progress');
            $data['status'] = $this->request->getPost('status');
            $update = $this->taskModel->update($taskId,$data);
            $staffs = $this->request->getPost('staff');
            $roles  = $this->request->getPost('role') ?? [];

            foreach ($staffs as $index => $staffId) {
                $roleId = $roles[$index];

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
                } else {
                    // Insert new assignment
                    $this->taskassignModel->insert([
                        'task_id'  => $taskId,
                        'staff_id' => $staffId,
                        'role'     => $roleId
                    ]);
                }
            }

            $validStatus = $update;
            $validMsg =  $update ? 'Task updated' : 'Failed to update task';
        }else{
            if ($taskId = $this->taskModel->insert($data)) {

                    $staffs = $this->request->getPost('staff');
                    $role =   $this->request->getPost('role');

                    foreach ($staffs as $index => $staff) {
                        $assign = [
                            'task_id'  => $taskId,
                            'staff_id' => $staff,
                            'role'     => $role[$index], 
                        ];
                        $notify = [
                            'user_id' =>  $staff,
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
            }
            else {
                $validMsg = lang('Custom.formError');
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
       $alltask = $this->taskModel->getTasks('',''); // or ->findAll()
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
                    'users'     => [],
                ];

                if (!empty($task['profileimg']) || !empty($task['name'])) {
                    $groupData[$taskId]['users'][] = [

                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                        'role'      => $task['role'],
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
       $alltasks = $this->taskModel->getMytask('',''); // or ->findAll()
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
                    'users'     => [],
                ];

                if (!empty($task['profileimg']) || !empty($task['name'])) {
                    $groupData[$taskId]['users'][] = [

                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                        'role'      => $task['role'],
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
                'created_by' => session('user_data')['id'],
                'title'   => 'Task '.$getTask['title'].' Status Change ',
                'message' => 'Task '.$getTask['title'].' Status Change to '.ucwords(str_replace('_', ' ', $new_status)).' By '.session('user_data')['username']
            ];
             $this->notificationModel->insert($notify);
        }
      
        $updated = $this->taskModel->update($task_id, ['status' => $new_status]);
        return $this->response->setJSON([
            'success' => $updated,
            'message' => $updated ? 'Task updated' : 'Failed to update task'
        ]);
    }

    function myTask() {
        $page = "My Tasks";
        $branches = $this->branchModel->where('status','active')->findAll();
        return view('admin/task/mytask',compact('page','branches'));
    }
    
}