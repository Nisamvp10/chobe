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

      $search = $this->request->getGet('search');
    //    $tasks = $this->taskModel
    // ->select('created_from_template,title,task_gen_date,
    //           COUNT(id) as total_tasks,
    //           SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks')
    // ->where('tasktype', 1)
    // ->groupBy('created_from_template')
    // ->findAll();
        $builder = $this->taskModel->select('tasks.id,tasks.title,tasks.task_gen_date,tasks.status, pu.store as store,pu.polaris_code')
    ->join('project_unit as pu','tasks.project_unit = pu.id');

        $builder->where([
            'tasks.tasktype' => 1,
            'tasks.ui' => 1
        ]);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('tasks.title', $search)
                ->orLike('pu.store', $search)
                ->orLike('pu.polaris_code', $search)
                ->orLike('tasks.task_gen_date', $search)
            ->groupEnd();
        }
        $builder->orderBy('tasks.id', 'desc');
        $tasks = $builder->findAll();
        if($tasks) {
            foreach($tasks as &$task) {
                $task['task_gen_date'] = date('d M Y', strtotime($task['task_gen_date']));
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
        $tasks = $this->taskModel->where(['tasktype'=>1,'id'=>$id])->first();

        if($tasks) {
            // foreach($tasks  as $task) {
                //if($tasks['status'] == 'Completed') {
                    $this->taskModel->update($tasks['id'],['ui'=>2]);
                //}
            // }
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
        if($activityIds) {
            foreach($activityIds as $activityId) {
                 $tasks = $this->taskModel->where(['tasktype'=>1,'id'=>$activityId])->first();
                if($tasks) {
                   $this->taskModel->update($tasks['id'],['ui'=>2]);
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