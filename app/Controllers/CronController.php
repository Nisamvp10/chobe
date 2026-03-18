<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\AssigntaskModel;
use App\Models\BranchesModel;
use App\Models\TaskModel;
use App\Models\NotificationModel;
use App\Models\ProjectsModel;
use App\Models\ActivityModel;
use App\Models\ActivityStaffModel;
use App\Models\ProjectunitModel;
use App\Models\TaskactivityModel;
use App\Models\TaskStaffActivityModel;
use App\Models\CronejobrequestModel;
use App\Models\MasterTaskModel;

class CronController extends Controller {

    protected $branchModel;
    protected $taskActivityModel;
    protected $taskModel;
    protected $taskassignModel;
    protected $notificationModel;
    protected $projects;
    protected $activityModel;
    protected $projectUnitModel;
    protected $taskStaffActivityModel;
    protected $cronejobrequestModel;
    protected $mastertaskModel;
    function __construct() {
        $this->branchModel = new BranchesModel();
        $this->taskModel = new TaskModel();
        $this->taskassignModel = new AssigntaskModel();
        $this->notificationModel = new NotificationModel();
        $this->projects = new ProjectsModel();
        $this->activityModel = new ActivityModel();
        $this->projectUnitModel = new ProjectunitModel();
        $this->taskActivityModel = new TaskactivityModel();
        $this->taskStaffActivityModel = new TaskStaffActivityModel();
        $this->cronejobrequestModel = new CronejobrequestModel();
        $this->mastertaskModel = new MasterTaskModel();
    }
    public function clonejob() {
        
      $mode = 'permanent';
        $taskType = ($mode === 'permanent') ? 1 : 2;

        $taskGenDate = date('Y-m-d', strtotime('-1 day'));
        $today       = date('Y-m-d');
        $nextRunDate = $today;

        $createdCount = 0;
        $skipCount = 0;
        $messages = [];

        /*
        -----------------------------------
        LOAD DAILY TEMPLATES
        -----------------------------------
        */

        $templates = $this->taskModel
            ->join('mastertasks', 'mastertasks.id = tasks.created_from_template')
            ->where('recurrence', 'daily')
            ->where('tasks.tasktype', 1)
            ->where('mastertasks.tasktype', $taskType)
            ->where('mastertasks.status', 'active')
            ->where('next_run_date <=', $today)
            ->groupBy('project_unit')
            ->findAll();

        if (!$templates) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No templates found'
            ]);
        }

        /*
        -----------------------------------
        LOAD ACTIVITIES FOR ALL TEMPLATES
        -----------------------------------
        */
        

        $templateIds = array_column($templates,'created_from_template');

        $activities = $this->activityModel
            ->whereIn('task_id',$templateIds)
            ->where('status','active')
            ->where('activity_type',1)
            ->findAll();

        $activityMap = [];

        foreach($activities as $a){
            $activityMap[$a['task_id']][] = $a;
        }

        /*
        -----------------------------------
        LOAD EXISTING TASKS
        -----------------------------------
        */

        $existingTasks = $this->taskModel
        ->select('created_from_template,project_id,project_unit')
        ->where('task_gen_date',$taskGenDate)
        ->where('taskmode',$taskType)
        ->findAll();

     
        $taskMap = [];

        foreach($existingTasks as $t){
            $key = $t['created_from_template'].'_'.$t['project_id'].'_'.$t['project_unit'];
            $taskMap[$key] = true;
        }

        /*
        -----------------------------------
        PROCESS TEMPLATES
        -----------------------------------
        */

        foreach ($templates as $template) {

            if(empty($template['created_from_template'])) continue;

            $unitId = $template['project_unit'];

            $key = $template['created_from_template'].'_'.$template['project_id'].'_'.$unitId;

            if(isset($taskMap[$key])){

            $skipCount++;
            continue;

        }

        /*
        -----------------------------------
        CREATE TASK
        -----------------------------------
        */

        $mastertaskData = $this->mastertaskModel->where('id',$template['created_from_template'])->get()->getRow();
        if($mastertaskData->tasktype ==2){
            continue;
        }


         $newTaskId = $this->taskModel->insert([

            'project_id'=>$template['project_id'],
            'project_unit'=>$unitId,
            'title'=>$mastertaskData->title,
            'description'=>$mastertaskData->description,
            'branch'=>$template['branch'],
            'overdue_date'=>$template['overdue_date'],
            'priority'=>$template['priority'],
            'status'=>'Pending',
            'task_gen_date'=>$taskGenDate,
            'progress'=>0,
           // 'taskmode'=>$taskType,
           'taskmode'=>$mastertaskData->tasktype,
            'recurrence'=>'daily',
            'next_run_date'=>$nextRunDate,
            'created_from_template'=>$template['created_from_template'],
            'created_at'=>date('Y-m-d H:i:s')

        ]);

        if(!$newTaskId) continue;

        $createdCount++;

        $masterActivities = $activityMap[$template['created_from_template']] ?? [];

        /*
        -----------------------------------
        INSERT TASK ACTIVITIES
        -----------------------------------
        */

        $activityBatch = [];

        foreach($masterActivities as $act){

            $activityBatch[] = [
                'task_id'=>$newTaskId,
                'activity_id'=>$act['id'],
                'created_at'=>date('Y-m-d H:i:s')
            ];
        }
        if($activityBatch){
            $this->taskActivityModel->insertBatch($activityBatch);
        }

        /*
        -----------------------------------
        GET UNIT STAFF
        -----------------------------------
        */

        $unit = $this->projectUnitModel->find($unitId);

        $staffIds = [];

        if(!empty($unit['allocated_to']) && $unit['allocated_type']==='permanently'){
            $staffIds[] = $unit['allocated_to'];
        }

        if(!empty($unit['assigned_to'])){
            $staffIds[] = $unit['assigned_to'];
        }

        $staffIds = array_unique($staffIds);

        /*
        -----------------------------------
        ASSIGN STAFF
        -----------------------------------
        */

         foreach($staffIds as $staffId){

                $this->taskassignModel->insert(['task_id'=>$newTaskId,'staff_id'=>$staffId,'status'=>'assigned','created_at'=>date('Y-m-d H:i:s')]);

                $staffActivityBatch = [];

                foreach($masterActivities as $act){

                    $staffActivityBatch[] = ['task_id'=>$newTaskId,'task_activity_id'=>$act['id'],'staff_id'=>$staffId,'status'=>'pending','progress'=>'pending'];

                }

                if($staffActivityBatch){
                    $this->taskStaffActivityModel->insertBatch($staffActivityBatch);
                }

            }

        }

        /*
        -----------------------------------
        FINAL RESPONSE
        -----------------------------------
        */

        $data = [

            'success'=>true,
            'message'=>'Task process completed',
            'created_tasks'=>$createdCount,
            'skipped_tasks'=>$skipCount

        ];

        if($data['success']){
            $this->cronejobrequestModel->insert([
                'created_tasks' => $data['created_tasks'],
                'message' => $data['message'],
                'created_at' => date('Y-m-d H:i:s')
            ]);

            echo "Task process completed - created tasks: " . $data['created_tasks'] . " - skipped tasks: " . $data['skipped_tasks'];
        }
       // print_r($data);
    }
    

  private function cron()
{
    $mode = 'permanent';
    $taskType = ($mode === 'permanent') ? 1 : 2;

    $taskGenDate = date('Y-m-d', strtotime('-1 day'));
    $today       = date('Y-m-d');
    $nextRunDate = $today;

    $createdCount = 0;
    $skipCount    = 0;

    $limit  = 50;     // process only 50 templates per cron run
    $offset = 0;

    while (true) {

        /*
        ---------------------------
        LOAD TEMPLATE CHUNK
        ---------------------------
        */

        $templates = $this->taskModel
            ->where('recurrence', 'daily')
            ->where('taskmode', $taskType)
            ->where('next_run_date <=', $today)
            ->limit($limit, $offset)
            ->findAll();

        if (!$templates) {
            break;
        }

        $offset += $limit;

        /*
        ---------------------------
        LOAD PROJECTS
        ---------------------------
        */

        $projectIds = array_column($templates, 'project_id');

        $projects = $this->projects
            ->whereIn('id', $projectIds)
            ->findAll();

        $projectMap = [];
        foreach ($projects as $p) {
            $projectMap[$p['id']] = $p;
        }

        /*
        ---------------------------
        LOAD UNITS BY CLIENT
        ---------------------------
        */

        $clientIds = array_column($projects, 'client_id');

        $units = $this->projectUnitModel
            ->whereIn('client_id', $clientIds)
            ->where('status', 1)
            ->findAll();

        $unitMap = [];
        foreach ($units as $u) {
            $unitMap[$u['client_id']][] = $u;
        }

        /*
        ---------------------------
        LOAD ACTIVITIES
        ---------------------------
        */

        $templateIds = array_column($templates, 'created_from_template');

        $activities = $this->activityModel
            ->whereIn('task_id', $templateIds)
            ->where('status', 'active')
            ->where('activity_type', 1)
            ->findAll();

        $activityMap = [];
        foreach ($activities as $a) {
            $activityMap[$a['task_id']][] = $a;
        }

        /*
        ---------------------------
        LOAD EXISTING TASKS
        ---------------------------
        */

        $existingTasks = $this->taskModel
            ->select('created_from_template,project_id,project_unit')
            ->where('task_gen_date', $taskGenDate)
            ->where('taskmode', $taskType)
            ->findAll();

        $taskMap = [];
        foreach ($existingTasks as $t) {
            $key = $t['created_from_template'].'_'.$t['project_id'].'_'.$t['project_unit'];
            $taskMap[$key] = true;
        }

        /*
        ---------------------------
        PROCESS TEMPLATES
        ---------------------------
        */

        foreach ($templates as $template) {

            if (empty($template['created_from_template'])) {
                continue;
            }

            if (!isset($projectMap[$template['project_id']])) {
                continue;
            }

            $project  = $projectMap[$template['project_id']];
            $clientId = $project['client_id'];

            if (!isset($unitMap[$clientId])) {
                continue;
            }

            $units = $unitMap[$clientId];
            $masterActivities = $activityMap[$template['created_from_template']] ?? [];

            foreach ($units as $unit) {

                $key = $template['created_from_template'].'_'.$template['project_id'].'_'.$unit['id'];

                // MEMORY DUPLICATE CHECK
                if (isset($taskMap[$key])) {
                    $skipCount++;
                    continue;
                }

                /*
                ---------------------------
                INSERT TASK
                ---------------------------
                */

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
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                if (!$newTaskId) {
                    continue;
                }

                $createdCount++;

                /*
                ---------------------------
                BATCH INSERT ACTIVITIES
                ---------------------------
                */

                $activityBatch = [];

                foreach ($masterActivities as $act) {
                    $activityBatch[] = [
                        'task_id' => $newTaskId,
                        'activity_id' => $act['id'],
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }

                if ($activityBatch) {
                    $this->taskActivityModel->insertBatch($activityBatch);
                }

                /*
                ---------------------------
                STAFF COLLECTION
                ---------------------------
                */

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

                    $staffActivityBatch = [];

                    foreach ($masterActivities as $act) {
                        $staffActivityBatch[] = [
                            'task_id' => $newTaskId,
                            'task_activity_id' => $act['id'],
                            'staff_id' => $staffId,
                            'status' => 'pending',
                            'progress' => 'pending'
                        ];
                    }

                    if ($staffActivityBatch) {
                        $this->taskStaffActivityModel->insertBatch($staffActivityBatch);
                    }
                }
            }
        }
    }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Task process completed',
            'created_tasks' => $createdCount,
            'skipped_tasks' => $skipCount
        ]);
    }


    public function closeCompletedtask() {
        $completedTasks = $this->taskModel->select('id,task_gen_date')->where(['status'=>'Completed','progress' => 100,'tasktype' => 1])
        ->where('task_gen_date >',date('Y-m-d',strtotime('-7 day')))
        ->where('task_gen_date <',date('Y-m-d',strtotime('-1 day')))
        ->findAll();
        if($completedTasks){
            foreach($completedTasks as $completedTask){
            //select activity assigns
                $taskActivityStatus = $this->taskStaffActivityModel->where(['task_id' => $completedTask['id']])->findAll();
                foreach($taskActivityStatus as $taskActivityStatus){
                    if($taskActivityStatus['status'] == 'completed'){
                        // check the status generated to graterthan on day 
                        if($completedTask['task_gen_date'] < date('Y-m-d',strtotime('-1 day'))){
                            $this->taskStaffActivityModel->update($taskActivityStatus['id'], ['commet_status' => 2]);
                        }
                    }
                }
            }
        }
    }
}