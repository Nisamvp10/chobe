<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityModel extends Model
{
    protected $table = 'activities';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'task_id','activity_title','activity_description','status','progress','duedate'
    ];

    public function getActivities($taskId=false,$searchInput=false,$filter=false,$startDate=false,$endDate=false) {
        $builder = $this->db->table('activities as a')
            ->select('a.activity_title,a.activity_description,a.status,a.id,a.progress,a.duedate,a.created_at,
            t.priority,
            u.profileimg, u.name, u.id as userId, ')
            ->join('tasks as t','a.task_id = t.id','left')
            ->join('activity_staff as ats','a.id =  ats.activity_id')
            ->join('users as u', 'u.id = ats.staff_id')
            ->where('a.task_id',$taskId);
            if($searchInput) {
                 $builder->like('a.activity_title',$searchInput);
            }
            if($filter && $filter != 'all')  {
                $builder->where('a.status',$filter);
            }
            if(!empty($startDate) && !empty($endDate)) {
                $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
                $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));
                $builder->where('a.created_at >=', $startDate);
                $builder->where('a.created_at <=', $endDate);
            }
            
            if(session('user_data')['role'] != 1 ) {
                $builder->where('ats.staff_id',session('user_data')['id']);
            }
        $result = $builder->get()->getResultArray();
        return $result;
    }
}
