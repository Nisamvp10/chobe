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


    function __construct(){
        $this->taskModel = new TaskModel();
        $this->staffModal = new UserModel();
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
            'activity_title'         => $this->request->getPost('title'),
            'activity_description'   => $this->request->getPost('description'),
            'status'        => $this->request->getPost('status'),
            'task_id'       => $taskId,
        ];
       

       
            $activityModel = new ActivityModel();
            $activitiesStaff = new ActivityStaffModel();
               
                $staffs = $this->request->getPost('staff');
                if(!empty($staffs)) {
                        if ($activitytaskId = $activityModel->insert($data)) {

                      
                
                            foreach ($staffs as $index => $staff) {
                                $assign = [
                                    'activity_id'  => $taskId,
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

    function activitiList() {
        $activityId = $this->request->getPost('activity');

        if(!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false , 'msg' => lang('Custom.invalidRequest')]);
        }

        if(!haspermission('','create_task')) {
            return $this->response->setJSON(['success' => false, 'message' => lang('Custom.accessDenied')]);
        }
        
    }


}
