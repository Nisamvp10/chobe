<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityModel extends Model
{
    protected $table = 'activities';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'task_id','activity_title','activity_description','status','progress','activity_type','duedate'
    ];

    public function getActivities($taskId=false,$searchInput=false,$filter=false,$startDate=false,$endDate=false,$staffId=null) {
            $builder = $this->db->table('task_staff_activities as tsa')
            ->select(' tsa.task_id,
            tsa.id as activityId,
            tsa.started_at,
            tsa.commet_status,
            tsa.completed_at,
            t.id,
            tsa.task_activity_id,
            tsa.staff_id,
            a.activity_title,
            a.activity_description,
            tsa.status,
            tsa.created_at,
            tsa.progress,
            t.priority,
            u.id AS userId,
            u.name,
            u.profileimg,
            cm.name as cmName,
            cm.profileimg as cmImg,
            cm.id as cmId,
            p.store as branch_name,
            u.profileimg')

        ->join('activities as a', 'a.id = tsa.task_activity_id', 'left')
        ->join('tasks as t', 't.id = tsa.task_id', 'left')
        ->join('users as u', 'u.id = tsa.staff_id', 'left')
        ->join('users as cm', 'cm.id = tsa.complated_by','left')
        ->join('project_unit as p', 'p.id = t.project_unit', 'left')
        ->where('tsa.task_id', $taskId)
        ->groupBy('tsa.task_activity_id');
           if($staffId) { 
            $builder->where('tsa.staff_id',$staffId);
           }
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
                $result = $builder->get()->getResultArray();
            return $result;
                

    }
    public function getActivitiesold($taskId=false,$searchInput=false,$filter=false,$startDate=false,$endDate=false) {
        $builder = $this->db->table('task_staff_activities as tsa')
            ->select('tsa.task_id,tsa.task_activity_id,tsa.staff_id,a.activity_title,tsa.status,tsa.created_at,tsa.progress,
            t.priority,
            u.profileimg, u.name, u.id as userId,
            ats.status as staffStatus,tsa.commet_status')
             ->join('activities as a','tsa.task_activity_id =  a.id')
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

     public function getMytaskCount($taskId) {
       // Get list
            $assignedActivities = $this->db->table('activities as a')
                ->select('a.*')
                ->join('activity_staff as ats', 'a.id = ats.activity_id')
                ->where('a.task_id', $taskId)
                ->where('ats.staff_id', session('user_data')['id'])
                ->get()
                ->getResultArray();

            // Get count
            $totalAssigned = count($assignedActivities);
            return $totalAssigned;
    }

    function getActivity($taskId=false,$searchInput=false,$filter=false,$startDate=false,$endDate=false) {
        $builder = $this->db->table ('activities as a')
            ->select('a.activity_title,a.activity_description,a.status,a.id,a.progress,a.duedate,a.created_at,
            mt.title as task_title');
            $builder->join('mastertasks as mt','mt.id = a.task_id','left');
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
             $builder->where('a.activity_type', 1);
               $result = $builder->get()->getResultArray();
        return $result;
            
        
    }
    function getTotalActivity($taskId) {
        $builder = $this->db->table('activities');
            $builder->select("
                COUNT(*) as total_activities,
                SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_activities
            ");
            $builder->where('task_id', $taskId);
            $query = $builder->get();
            $result = $query->getRowArray();
            return $result;
    }
    public function getActivitytasks($filter=false,$taskId=false,$search=false,$startDate=false,$endDate=false) {
        $builder = $this->db->table('tasks as t')
            ->select('t.title,a.activity_title,a.created_at,a.status,a.progress,t.priority')
            ->join('activities as a ' ,'a.task_id = t.id','inner');
            if($filter && $filter != 'all')  {
                $builder->where('a.status',$filter);
            }
            if($search){
                $builder->like('a.activity_title',$search)
                    ->orLike('t.title',$search);
            }
            $result = $builder->get()->getResultArray();
            return  $result;
    }
    function getactivityBymastarTask($taskId) {
        $builder = $this->db->table('activities as a')
            ->select('a.id,a.activity_title as title')
            ->join('mastertasks as mt','mt.id = a.task_id','left')
            ->where('a.task_id',$taskId);
            $result = $builder->get()->getResultArray();
            return  $result;
    }
}
