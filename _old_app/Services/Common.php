<?php
namespace App\Services;
use App\Models\TaskModel;
use App\Models\TaskStaffActivityModel;

class Common {
    protected $taskModel;
    protected $db;
    function __construct() {
        $this->taskModel = new TaskModel();
        $this->db = \Config\Database::connect();
    }
    
    public function getBranchNameBytaskId($taskId) {
        $builder = $this->taskModel->select('tasks.id, tasks.title,tasks.task_gen_date, project_unit.store, project_unit.oldstore_name, project_unit.oracle_code, project_unit.polaris_code')
        ->join('project_unit', 'project_unit.id = tasks.project_unit')
        ->where('tasks.id', $taskId);
        $result = $builder->get()->getRow();
        return $result;
    }

    public function updateTaskActivitiesUI($template,$taskGenDate) {
      $db = \Config\Database::connect();

      
        $sql1 = "
            UPDATE task_staff_activities tsa
            INNER JOIN tasks t ON t.id = tsa.task_id
            SET tsa.commet_status = 2
            WHERE t.created_from_template = ?
            AND t.task_gen_date = ?
        ";

        $db->query($sql1, [$template, $taskGenDate]);

      
        $sql2 = "
            UPDATE tasks t
            SET t.ui = 2
            WHERE t.created_from_template = ?
            AND t.task_gen_date = ?
        ";

        $db->query($sql2, [$template, $taskGenDate]);

        return true;
    }
    private function projectsIdOrUser(){
        return userProjects();
    }

    function userProjectsTasks($limit=false,$orderBy=false,$notificationTask=false,$filter=false,$search=false) {
        $userId = session('user_data')['id'];

        $taskIds = $this->db->table('task_assignees')
            ->select('task_id')
            ->where('staff_id', $userId)
            ->groupBy('task_id')
            ->get()
            ->getResultArray();

        $myTaskIds = array_column($taskIds, 'task_id');
        $userProjects = $this->projectsIdOrUser();


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
            ->whereIn('t.project_id', $userProjects)
            ->where('t.ui',1)
            ->where('t.tasktype',1)
            ->orderBy('t.id', 'DESC');
            // $builder->groupStart()
            // ->where('t.status !=', 'Completed')
            // ->where('t.tasktype',1)
            // ->orWhere('t.created_at >=', 'DATE_SUB(NOW(), INTERVAL 1 DAY)', false)
            // ->groupEnd();

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
       
        if ($limit) {
        $builder->limit($limit);
        }

    return $results = $builder->get()->getResultArray();

    }

    public function countTaskActivities($taskId,$staffId) {
        $taskStaffActivityModel = new TaskStaffActivityModel();
        $projectOruser = $this->projectsIdOrUser();
        $builder = $taskStaffActivityModel->where(['task_id' => $taskId])->groupBy('task_activity_id');
        if(empty($projectOruser)){
            $builder->whereIn('staff_id', $staffId);
        }
        return $builder->countAllResults();
    }
}