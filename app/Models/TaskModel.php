<?php
namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model {
    protected $table = 'tasks';
    protected $primaryKey ='id';
    protected $allowedFields= ['id','title','description','created_from_template','recurrence','next_run_date','status','taskmode','tasktype','project_id','priority','progress','branch','project_unit','overdue_date','completed_at'];

    protected $useSoftDeletes = false; // IMPORTANT

    function getTasks($limit=false,$orderBy=false,$filter = false,$searchInput=false,$startDate=false,$endDate=false,$taskProject=false) {


        $builder = $this->db->table('tasks as t')
            ->select('
                t.id, t.title, t.description, t.status, t.completed_at, t.project_id,t.project_unit,t.next_run_date,
                b.polaris_code,
                t.priority, t.overdue_date, b.store as branch_name, b.id as store, 
                t.created_at, u.profileimg, u.name, u.id as userId, 
                t.progress, a.role, a.priority as userPriority,ti.image_url,
                t.created_from_template,
                c.id as clientId')
            ->join('project_unit as b', 'b.id = t.project_unit', 'left')
            ->join('clients as c', 'b.client_id = c.id', 'left')
            ->join('task_assignees as a', 'a.task_id = t.id')
            ->join('users as u', 'u.id = a.staff_id')
            ->join('user_position as up', 'u.position_id = up.id', 'left')
            ->join('task_images as ti', 'ti.task_id = t.id', 'left')
            ->where('up.type !=',1)
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
                 $builder->like('t.title',$searchInput);
            }
            if(!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
                $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));
                $builder->where('t.created_at >=', $startDate);
                $builder->where('t.created_at <=', $endDate);
            }
            
            if(session('user_data')['role'] != 1 ) {
                $builder->where('a.staff_id',session('user_data')['id']);
            }
            if ($orderBy) {
                $builder->orderBy($orderBy);
            }
            if ($limit) {
                $builder->limit($limit);
            }

        $result = $builder->get()->getResultArray();
        return $result;
                    
    }

     function getMytask($limit=false,$orderBy=false,$notificationTask=false,$filter=false) {
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
                b.polaris_code,
                t.priority, t.overdue_date, b.store as branch_name, b.id as store, 
                t.created_at, u.profileimg, u.name, u.id as userId, 
                t.progress, a.role, a.priority as userPriority,ti.image_url')
           ->join('project_unit as b', 'b.id = t.project_unit', 'left')
            ->join('task_assignees as a', 'a.task_id = t.id')
            ->join('users as u', 'u.id = a.staff_id')
            ->join('task_images as ti',  'ti.task_id = t.id', 'left')
            //->whereIn('t.id', $myTaskIds)
            ->where('a.staff_id',session('user_data')['id'])
            ->orderBy('t.id', 'DESC');
            $builder->groupStart()
            ->where('t.status !=', 'Completed')
            ->orWhere('t.created_at >=', 'DATE_SUB(NOW(), INTERVAL 1 DAY)', false)
            ->groupEnd();

                    if ($orderBy) {
                        $builder->orderBy($orderBy);
                    }
                     if($filter && $filter != 'all')  {
                    // $filter = ($filter == 'pending' ? 'Pending' : ($filter == "progress" ? 'In_Progres' :'Completed'));
                        $builder->where('t.status',$filter);
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