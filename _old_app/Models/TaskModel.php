<?php
namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model {
    protected $table = 'tasks';
    protected $primaryKey ='id';
    protected $allowedFields= ['id','title','description','created_from_template','recurrence','next_run_date','task_gen_date','task_gen_date','status','taskmode','ui','tasktype','project_id','priority','progress','branch','project_unit','project_unit_status','overdue_date','completed_at'];

    protected $useSoftDeletes = false; // IMPORTANT 

    function getTasks($limit=false,$orderBy=false,$filter = false,$searchInput=false,$startDate=false,$endDate=false,$taskProject=false,$taskLimit=false,$offset=false) {


        $builder = $this->db->table('tasks as t')
            ->select('
                t.id, t.title, t.description, t.status, t.completed_at, t.project_id,t.project_unit,t.next_run_date,
                b.polaris_code,b.oracle_code,b.oldstore_name,
                t.priority, t.overdue_date, b.store as branch_name, b.id as store, 
                t.created_at, u.profileimg, u.name, u.id as userId, 
                t.progress, a.role, a.priority as userPriority,ti.image_url,
                t.created_from_template,
                t.taskmode,t.task_gen_date,
                c.id as clientId')
            ->join('project_unit as b', 'b.id = t.project_unit', 'left')
            ->join('clients as c', 'b.client_id = c.id', 'left')
            ->join('task_assignees as a', 'a.task_id = t.id')
            ->join('users as u', 'u.id = a.staff_id')
            ->join('user_position as up', 'u.position_id = up.id', 'left')
            ->join('task_images as ti', 'ti.task_id = t.id', 'left')
            // ->where('up.type !=',1)
            ->where('t.tasktype',1)
            ->orderBy('t.id', 'DESC');
            if($filter && $filter != 'all')  {
               // $filter = ($filter == 'pending' ? 'Pending' : ($filter == "progress" ? 'In_Progres' :'Completed'));
                $builder->where('t.status',$filter);
            }
            if($taskProject && $taskProject != 'all')  {
               // $filter = ($filter == 'pending' ? 'Pending' : ($filter == "progress" ? 'In_Progres' :'Completed'));
                $builder->where('t.project_id',$taskProject);
            }
            if($searchInput) {
                $builder->groupStart();
                 $builder->like('t.title',$searchInput);
                 $builder->orLike('b.store',$searchInput);
                 $builder->orLike('b.polaris_code',$searchInput);
                 $builder->orLike('b.oracle_code',$searchInput);
                 $builder->orLike('b.oldstore_name',$searchInput);
                 $builder->orLike('u.name',$searchInput);
                 $builder->groupEnd();
            }
            if(!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
                $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));
                $builder->where('t.task_gen_date >=', $startDate);
                $builder->where('t.task_gen_date <=', $endDate);
            }else{
                //show last 3 days data only  today yesterday day before yesterday
                $builder->where('t.task_gen_date >=', date('Y-m-d 00:00:00', strtotime('-3 days')));
            }
            $builder->where('t.ui ', 1);
            if(session('user_data')['role'] != 1 ) {
                $builder->where('a.staff_id',session('user_data')['id']);
            }
            if ($orderBy) {
                $builder->orderBy($orderBy);
            }
            if ($taskLimit) {
                $builder->limit($taskLimit);
            }

        $result = $builder->get()->getResultArray();
        return $result;
                    
    }

     function getMytask($limit=false,$orderBy=false,$notificationTask=false,$filter=false,$search=false) {
        $userId = session('user_data')['id'];

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
            //->whereIn('t.id', $myTaskIds)
            ->where('a.staff_id',session('user_data')['id'])
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
}