<?php
namespace App\Models;

use CodeIgniter\Model;

class TaskactivityModel extends Model {
    protected $table = 'task_activities';
    protected $allowedFields = ['id','task_id','activity_id','status','created_at'];
    protected $primaryKey = 'id';

    public function getActivities($taskId=false,$searchInput=false,$filter=false,$startDate=false,$endDate=false) {
        $builder = $this->db->table ('task_activities as ta')
            ->select('ac.activity_title,ac.activity_description,tsa.status,ac.id,ac.duedate,ta.created_at,t.priority,tsa.progress')
            ->join('tasks as t','t.id = ta.task_id','left')
            ->join('activities as ac','ta.activity_id = ac.id','left')
            ->join('task_staff_activities as tsa','tsa.task_activity_id = ta.activity_id','left');
            if($searchInput) {
                 $builder->like('a.activity_title',$searchInput);
            }
            if($filter && $filter != 'all')  {
                $builder->where('a.status',$filter);
            }
            if(!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
                $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));
                $builder->where('ta.created_at >=', $startDate);
                $builder->where('ta.created_at <=', $endDate);
            }
               $result = $builder->get()->getResultArray();
        return $result;
            
    }
     public function getMytaskCount($taskId) {
       // Get list
            $assignedActivities = $this->db->table('task_staff_activities as tsa')
                ->select('tsa.*')
                ->join('task_activities as ta', 'ta.id = tsa.task_activity_id')
                ->where('ta.task_id', $taskId)
                ->where('tsa.staff_id', session('user_data')['id'])
                ->get()
                ->getResultArray();

            // Get count
            $totalAssigned = count($assignedActivities);
            return $totalAssigned;
    }
}