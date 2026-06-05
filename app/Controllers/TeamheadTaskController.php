<?php
namespace App\Controllers;
use App\Models\ActivityStaffModel;
use CodeIgniter\Controller;

use App\Services\Teamheadtasks;

class TeamheadTaskController extends Controller {

    protected $teamHeadTaskService;
    protected $activityTasksAssignModel;
    function __construct() {
        $this->teamHeadTaskService = new Teamheadtasks();
        $this->activityTasksAssignModel = new ActivityStaffModel();
    }
    
    public function teamHeadTask() {
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
        $search = $this->request->getGet('search');
        $alltasks = $this->teamHeadTaskService->getMytask('',$notifiytask,$filter,$search); 
        return $this->response->setJSON([ 'success'=>true,'task' => $alltasks['task'],'pendingTasks' => $alltasks['pendingTasks'],'completedTasks' => $alltasks['completedTasks']]);
        //return $this->response->setJSON(['tasks' => $alltasks]);
    }
}