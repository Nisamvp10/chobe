<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\TaskModel;   

class UseruiController extends Controller{ 
    
    protected $taskModel;

    public function __construct() {
        $this->taskModel = new TaskModel();
    }
    
    public function index() {
        
        $route = (!haspermission('','user_ui') ? 'admin/pages-error-404':'dashboard/usertask-controller');
        $page = (haspermission('','user_ui') ? "User UI" :lang('Custom.accessDenied') );
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
       $tasks = $this->taskModel
    ->select('created_from_template,title,task_gen_date,
              COUNT(id) as total_tasks,
              SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks')
    ->where('tasktype', 1)
    ->groupBy('created_from_template')
    ->findAll();
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
        $tasks = $this->taskModel->where(['tasktype'=>1,'created_from_template'=>$id])->find();

        if($tasks) {
            foreach($tasks  as $task) {
                if($task['status'] == 'Completed') {
                    $this->taskModel->update($task['id'],['ui'=>2]);
                }
            }
             return $this->response->setJSON([
                'success' => true,
                'message' => 'Done'
            ]);
        }else{
            return $this->response->setJSON([
                'success' => false,
                'message' => 'task not found'
            ]);
        }
    }

}