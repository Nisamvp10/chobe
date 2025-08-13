<?php
namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model {
    protected $table = 'tasks';
    protected $allowedFields= ['id','title','description','status','project_id','priority','progress','branch','overdue_date','completed_at'];
    protected $primaryKey ='id';
 
    function getTasks($limit=false,$orderBy=false) {

        // $builder = $this->db->table('tasks as t')
        //             ->select('t.id,t.title,t.description,t.status,t.completed_at,t.priority,t.overdue_date,b.branch_name,b.id as store,t.created_at,u.profileimg,u.name,u.id as userId,t.progress,a.role,a.priority as userPriority,ti.image_url')
        //             ->join('branches as b','t.branch = b.id')
        //             ->join('task_assignees as a','t.id = a.task_id')
        //             ->join('task_images ti', 't.id = ti.task_id', 'left')
        //             ->join('users u','a.staff_id =u.id');

        $builder = $this->db->table('tasks as t')
            ->select('
                t.id, t.title, t.description, t.status, t.completed_at, 
                t.priority, t.overdue_date, b.branch_name, b.id as store, 
                t.created_at, u.profileimg, u.name, u.id as userId, 
                t.progress, a.role, a.priority as userPriority, ti.image_url
            ')
            ->join('branches as b', 'b.id = t.branch', 'left')
            ->join('task_assignees as a', 'a.task_id = t.id')
            ->join('users as u', 'u.id = a.staff_id')
            ->join('task_images as ti', 'ti.task_id = t.id', 'left')
            ->orderBy('t.id', 'DESC');
            
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

     function getMytask($limit=false,$orderBy=false,$notificationTask=false) {

        $userId = session('user_data')['id'];

        $taskIds = $this->db->table('task_assignees')
            ->select('task_id')
            ->where('staff_id', $userId)
            ->groupBy('task_id')
            ->get()
            ->getResultArray();

        $myTaskIds = array_column($taskIds, 'task_id');


         $builder = $this->db->table('tasks as t')
                    ->select('t.id,t.title,t.description,t.status,t.completed_at,t.priority,t.overdue_date,b.branch_name,b.id as store,t.created_at,u.profileimg,u.name,u.id as userId,t.progress,a.role,a.priority as userPriority,ti.image_url')
                    ->join('branches as b','t.branch = b.id')
                    ->join('task_assignees as a','t.id = a.task_id')
                     ->join('task_images ti', 't.id = ti.task_id', 'left')
                    ->join('users u','a.staff_id =u.id')
                     ->whereIn('t.id', $myTaskIds);
                    if ($orderBy) {
                        $builder->orderBy($orderBy);
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