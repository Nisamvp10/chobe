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
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    function __construct() {
        $this->branchModel = new BranchesModel();
        $this->taskModel = new TaskModel();//
        $this->taskassignModel = new AssigntaskModel();//
        $this->notificationModel = new NotificationModel();//
        $this->projects = new ProjectsModel();//
        $this->activityModel = new ActivityModel();//
        $this->projectUnitModel = new ProjectunitModel();//
        $this->taskActivityModel = new TaskactivityModel();//
        $this->taskStaffActivityModel = new TaskStaffActivityModel();//
    }
    public function clonejob() {
        
        //echo "Cron job executed successfully!";
        $this->cron();
    }

    private function cron() {
       

        $mode = 'permanent';
        $taskType = ($mode === 'permanent') ? 1 : 2;

        $taskGenDate = date('Y-m-d', strtotime('-1 day'));
        $today       = date('Y-m-d');
        $nextRunDate = $today;

        $messages = [];
        $createdCount = 0;
        $skipCount = 0;

        // Get templates
        $templates = $this->taskModel->where('recurrence', 'daily')->where('taskmode', $taskType)->where('next_run_date <=', $today)->findAll();

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
                    ])
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
                    $this->notificationModel->insert([
                        'user_id' => $staffId,
                        'task_id' => $newTaskId,
                        'type' => 'new_task',
                        'title' => 'New Task',
                        'created_by' => 000001,
                        'message' => 'A daily task has been assigned to you',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
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
}