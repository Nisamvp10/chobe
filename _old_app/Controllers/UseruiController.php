<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\TaskModel;   
use App\Services\Common;
class UseruiController extends Controller{ 
    
    protected $taskModel;
    protected $common;

    public function __construct() {
        $this->common = new Common();
        $this->taskModel = new TaskModel();
    }
    
    public function index() {
        
        $route = (!haspermission('','user_ui') ? 'admin/pages-error-404':'dashboard/usertask-controller');
        $page = (haspermission('','user_ui') ? getappdata('screen_title') :lang('Custom.accessDenied') );
        //total tasks by group
        return view($route,compact('page'));

    }

    public function list() {

        if(!haspermission('','user_ui')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
                
            ]);
        }
        if(!$this->request->isAjax()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }

      $search = $this->request->getGet('search');
    
        $builder = $this->taskModel->select('tasks.id,tasks.title,tasks.task_gen_date,tasks.status');

        $builder->where([
            'tasks.tasktype' => 1,
            'tasks.ui' => 1
        ]);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('tasks.title', $search)
                ->orLike('tasks.task_gen_date', $search)
            ->groupEnd();
        }
        $builder->groupBy(['DATE(tasks.task_gen_date)', 'tasks.created_from_template'])
            ->orderBy('tasks.task_gen_date', 'DESC');
        $tasks = $builder->findAll();
        if($tasks) {
            foreach($tasks as &$task) {
                $task['task_gen_date'] = date('d-m-Y', strtotime($task['task_gen_date']));
            }
        }
        return $this->response->setJSON([
            'success' => true,
            'tasks'   => $tasks
        ]);
    }

    public function lock() {
          if(!haspermission('','user_ui')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
                
            ]);
        }
        if(!$this->request->isAjax()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }
        
        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Task ID required'
            ]);
        }

        $task = $this->taskModel->where(['tasktype' => 1, 'id' => $id])->first();

        if (!$task) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Task not found'
            ]);
        }
        $this->common->updateTaskActivitiesUI($task['created_from_template'],$task['task_gen_date']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'UI Updated Successfully'
        ]);
    }
    public function multipleDelete() {
        if(!haspermission('','user_ui')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
                
            ]);
        }
        if(!$this->request->isAjax()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }
        $activityIds = $this->request->getPost('activityIds');
        if (!$activityIds) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Task ID required'
            ]);
        }

        if($activityIds) {
            foreach($activityIds as $activityId) {
                 $tasks = $this->taskModel->where(['tasktype'=>1,'id'=>$activityId])->first();
                if($tasks) {
                    $this->common->updateTaskActivitiesUI($tasks['created_from_template'],$tasks['task_gen_date']);
                }
            }
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Done'
            ]);
        }else{
            return $this->response->setJSON([
                'success' => false,
                'message' => 'activity not found'
            ]);
        }
    }
}