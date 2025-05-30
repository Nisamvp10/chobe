<?php
namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model {
    protected $table = 'tasks';
    protected $allowedFields= ['id','title','description','status','priority','progress','branch','overdue_date','completed_at'];
    protected $primaryKey ='id';
 
    function getTasks($limit=false,$orderBy=false,$taskType=false) {

        $builder = $this->db->table('tasks as t')
                    ->select('t.id,t.title,t.description,t.status,t.completed_at,t.priority,t.overdue_date,b.branch_name,b.id as store,t.created_at,u.profileimg,u.name,u.id as userId,t.progress,a.role')
                    ->join('branches as b','t.branch = b.id')
                    ->join('task_assignees as a','t.id = a.task_id')
                    ->join('users u','a.staff_id =u.id');
                    if(session('user_data')['role'] != 1 ) {
                        $builder->where('a.staff_id',session('user_data')['id']);
                    }
                    if($taskType == 1 ) {
                        $builder->where('a.staff_id',session('user_data')['id'])->groupBy('task_id');
                    }
                    if ($orderBy) {
                        $builder->orderBy($orderBy);
                    }
                    if ($limit) {
                        $builder->limit($limit);
                    }

        $result = $builder->get()->getResultArray();
        echo $this->db->getLastQuery();
        return $result;
                    
    }
}