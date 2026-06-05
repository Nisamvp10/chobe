<?php
namespace App\Services;
use App\Models\TaskModel;
use App\Models\ActivityStaffModel;
use App\Models\TaskStaffActivityModel;

class Teamheadtasks {
    protected $task;
    protected $db;
    protected $taskActivityModel;
    protected $taskStaffActivityModel;
    function __construct() {

        $this->task = new TaskModel();
        $this->db = \Config\Database::connect();
        $this->activityStaffModel = new ActivityStaffModel();
        $this->taskStaffActivityModel = new TaskStaffActivityModel();
        
    }
    private function taskQuery($limit=false,$notificationTask=false,$filter=false,$search=false,$orderBy=false) {
        $userId = session('user_data')['id'];
        $role = hasRole();
            $getProjectId = $this->db->table('team_head_projects')
            ->select('project_type_id')
            ->where('staff_id', $userId)
            ->get()
            ->getRow();
        

        $taskIds = $this->db->table('task_assignees')
            ->select('task_id')
            ->where('staff_id', $userId)
            ->groupBy('task_id')
            ->get()
            ->getResultArray();

        $myTaskIds = array_column($taskIds, 'task_id');


         $builder = $this->db->table('tasks as t')
            ->select('t.id, t.title, t.description, t.status, t.completed_at, t.project_id,t.next_run_date,
                b.polaris_code,b.oracle_code,b.oldstore_name,
                t.priority, t.overdue_date, b.store as branch_name, b.id as store, 
                t.created_at, u.profileimg, u.name, u.id as userId, t.taskmode,t.task_gen_date,
                t.progress, a.role, a.priority as userPriority,ti.image_url')
            ->join('project_unit as b', 'b.id = t.project_unit', 'left')
            ->join('task_assignees as a', 'a.task_id = t.id')
            ->join('users as u', 'u.id = a.staff_id')
            ->join('task_images as ti',  'ti.task_id = t.id', 'left')
            ->join('team_head_projects as tp', 'tp.project_type_id = t.project_id', 'left')
            ->where('tp.project_type_id', $getProjectId->project_type_id);
            $builder->where('t.ui',1)
            ->where('t.tasktype',1)
            ->orderBy('t.id', 'DESC');
            

            if ($orderBy) {
                $builder->orderBy($orderBy);
            }
            if($filter && $filter != 'all')  {
                // $filter = ($filter == 'pending' ? 'Pending' : ($filter == "progress" ? 'In_Progres' :'Completed'));
                $builder->where('t.status',$filter);
            }
            if($search) {
                $builder->groupStart();
                $builder->like('t.title',$search);
                $builder->orLike('b.store',$search);
                $builder->groupEnd();
            }
            if($notificationTask) {
                $builder->where('t.id',$notificationTask);
            }
            if ($limit) {
                $builder->limit($limit);
            }
        return $results = $builder->get()->getResultArray();

    }
    function tasklistGroup(){
         $alltasks = $this->taskQuery();
         
        $pendingTasks = 0;
        $completedTasks = 0;
        
        $groupData = [];
        foreach ($alltasks as &$task) {
            $taskId = $task['id'];
            if($task['status'] == 'Pending'){
                $pendingTasks++;
            }
            if($task['status'] == 'Completed'){
                $completedTasks++;
            }

            if (!isset($groupData[$taskId])) {
                //   $activityTasksAssignModel->where(['activity_id'=> $task['id'],'staff_id'=>session('user_data')['id']])->countAllResults();
                //   $this->taskActivityModel->getMytaskCount($task['id']);
                
                $groupData[$taskId] = [

                    'id'        => encryptor($task['id']),
                    'title'     => $task['title'],
                    'storeId'   => $task['store'],
                    'description' => $task['description'],
                    'branch_name' => $task['branch_name'],
                    'project'   => $task['project_id'],
                    'total_activities' => $this->taskStaffActivityModel->where(['task_id' => $task['id'],'staff_id'=>session('user_data')['id']])->groupBy('task_activity_id')->countAllResults(),
                    'completed_activities' => $this->taskStaffActivityModel->where(['task_id' => $task['id'],'staff_id'=>session('user_data')['id'],'status' => 'Completed'])->groupBy('task_activity_id')->countAllResults(),
                    'priority'  => $task['priority'],
                    'status'    => $task['status'],
                    'overdue_date' => $task['overdue_date'],
                    'progress'  => $task['progress'],
                    'action'    => 0,//$task['action'],
                    'duedate'   => $task['next_run_date'],
                    'polarisCode'   => $task['polaris_code'],
                    'oracleCode'   => $task['oracle_code'],
                    'ducument'  => $task['image_url'],
                    'created'   => date('d-m-Y',strtotime($task['task_gen_date'])),
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
                $existingProfiles = array_column($groupData[$taskId]['users'], 'userId');
                if (!empty($task['name'])  && !in_array($task['userId'], $existingProfiles)) {
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
        return [ 'success'=>true,'task' => $tasks,'completedTasks'=>$completedTasks,'pendingTasks' =>$pendingTasks];

    }
    function tasklistByuser(){
        $alltasks = $this->taskQuery();
      // echo (string) db_connect()->getLastQuery();
        $pendingTasks = 0;
        $completedTasks = 0;
        
        $groupData = [];
        foreach ($alltasks as &$task) {
            $taskId = $task['id'];
            if($task['status'] == 'Pending'){
                $pendingTasks++;
            }
            if($task['status'] == 'Completed'){
                $completedTasks++;
            }

            if (!empty($task['id'])) {
               $totalActivities = $this->taskStaffActivityModel->where(['task_id' => $task['id'],'staff_id'=>$task['userId'],])->groupBy('task_activity_id')->countAllResults();
               $completedActivities =  $this->taskStaffActivityModel->where(['task_id' => $task['id'],'staff_id'=>$task['userId'],'status' => 'Completed','started_at !=' =>NULL])->groupBy('task_activity_id')->countAllResults();
              //  echo $this->taskStaffActivityModel->getLastQuery();


//echo    $this->taskStaffActivityModel->getLastQuery();
                $groupData[] = [

                    'id'        => encryptor($task['id']),
                    'id_'        => $totalActivities.'/'.$completedActivities,
                    'userId'    => $task['userId'],
                    'title'     => $task['title'],
                    'storeId'   => $task['store'],
                    'description' => $task['description'],
                    'branch_name' => $task['branch_name'],
                    'project'   => $task['project_id'],
                    'total_activities' => $totalActivities,//$this->taskStaffActivityModel->where(['task_id' => $task['id']])->groupBy('task_activity_id')->countAllResults(),
                    'completed_activities' => $completedActivities,//$this->taskStaffActivityModel->where(['task_id' => $task['id'],'staff_id'=>$task['userId'],'status' => 'Completed'])->groupBy('task_activity_id')->countAllResults(),
                    'priority'  => $task['priority'],
                    'status'    =>  ($totalActivities == $completedActivities ?'Completed':'Pending'),// $task['status'],
                    'overdue_date' => $task['overdue_date'],
                    'progress'  => $task['progress'],
                    'action'    => 0,//$task['action'],
                    'duedate'   => $task['next_run_date'],
                    'polarisCode'   => $task['polaris_code'],
                    'oracleCode'   => $task['oracle_code'],
                    'ducument'  => $task['image_url'],
                    'created'   => date('d-m-Y',strtotime($task['task_gen_date'])),
                    'usertype' => 'single',
                    'users'     => [
                        'img'       => $task['profileimg'],
                        'staffName' => $task['name'],
                        'userId'    => $task['userId'],
                        'role'      => $task['role'],
                        'userPriority' => $task['userPriority'],
                    ],
                ];

                // if (!empty($task['name'])) {
                //     $groupData[]['users'][] = [

                //         'img'       => $task['profileimg'],
                //         'staffName' => $task['name'],
                //         'userId'    => $task['userId'],
                //         'role'      => $task['role'],
                //         'userPriority' => $task['userPriority'],
                //     ];
                // }

                $groupData[$taskId]['duration'] = $task['status'] == 'Completed'
                    ? human_duration($task['created_at'], $task['completed_at'])
                    : human_duration($task['created_at']);
            } 
        }

        $tasks = array_values($groupData);
        return [ 'success'=>true,'task' => $tasks,'completedTasks'=>$completedTasks,'pendingTasks' =>$pendingTasks];

    }
    function taskLists()
    {
        $alltask = $this->taskQuery();
        $taskMode = session('taskMode') ?? '';
        $tasks = ($taskMode == 'Group') ? $this->tasklistGroup() : $this->tasklistByuser();
        return $tasks;    
    }
    public function getMytask($notifiytask=false,$filter=false,$search=false) {
        $task = $this->taskLists();
        return $task;
    }
    
}