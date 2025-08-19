<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\TaskModel;
use App\Models\UserModel;
use App\Models\ActivityModel;
use App\Models\ActivityStaffModel;


class ActivitiesController extends Controller {
protected $taskModel;
protected $staffModal;
protected $activityModel;
protected $userModel;



    function __construct(){
        $this->taskModel = new TaskModel();
        $this->staffModal = new UserModel();
        $this->activityModel = new ActivityModel();
        $this->userModel = new UserModel();
    }

    function activities($id=false) {
        $id = decryptor($id);
        $task = $this->taskModel->where('id',$id)->first();
        if(!empty($task)) {
            $staff =  $this->staffModal->findAll();
            $page = "Task : " .$task['title'];
        }else{
            $page = '';
            $staff = '';
        }
        $data = '';
        
        $activityId =$id;
        return view('admin/activities/index',compact('page','data','id','staff','activityId'));
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
            'status' => 'required',
            
        ];

        if(!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false , 'errors' => $this->validator->getErrors()]);
        }
        $validStatus = false;
        $validMsg = '';

        $data = [
            'activity_title'        => $this->request->getPost('title'),
            'activity_description'  => $this->request->getPost('description'),
            'status'                => $this->request->getPost('status'),
            'task_id'               => $taskId,
        ];
       
            $activityModel = new ActivityModel();
            $activitiesStaff = new ActivityStaffModel();
               
                $staffs = $this->request->getPost('staff');
                if(!empty($staffs)) {
                        if ($activitytaskId = $activityModel->insert($data)) {

                      
                
                            foreach ($staffs as $index => $staff) {
                                $assign = [
                                    'activity_id'  => $activitytaskId,
                                    'staff_id' => $staff,
                                ];
                                $activitiesStaff->insert($assign);
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

        }else{
            $validMsg = 'Please select at least one participant for the task ';
        }
                        
        return $this->response->setJSON(['success' => $validStatus,'message' => $validMsg]);
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

        $activityTasks = $this->activityModel->getActivities($taskId,$search,$filter,$startDate,$endDate);
        $groupData = [];
        $allusers = $this->userModel->select('id,name,profileimg')->where(['status'=>'approved','booking_status'=>1])->findAll();

        foreach($activityTasks as &$task) {
            $taskId = $task['id'];
            if(!isset($groupData[$taskId])) {
                $groupData[$taskId] = [
                    'id'            => encryptor($task['id']),
                    'title'         => $task['activity_title'],
                    'description'   => $task['activity_description'],
                    'priority'      =>  $task['priority'],
                    'status'        => $task['status'],
                    'progress'      => $task['progress'],
                    'overdue_date'  => $task['duedate'],
                    'createdAt'     => $task['created_at'],
                    'allUsers'      => $allusers,
                    'users'         => [],
                ];

                if(!empty($task['profileimage']) || !empty($task['name'])) {
                    $groupData[$taskId]['users'][] = [
                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                    ];
                }
            }else{
                $existingProfiles = array_column($groupData[$taskId]['users'],'img');
                if(!empty($task['name'] && count($groupData[$taskId]['users']) < 8 )) {
                    $groupData[$taskId]['users'] [] = [
                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                    ];
                }
            }
        }

        $tasks = array_values($groupData);
        return $this->response->setJSON([ 'success'=>true,'task' => $tasks]);
    }


}
