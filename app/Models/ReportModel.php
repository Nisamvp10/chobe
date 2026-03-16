<?php

namespace App\Models;

use CodeIgniter\Model;
class ReportModel extends Model
{
    protected $table = 'tasks';   // <-- important
    protected $primaryKey = 'id';

public function getReports($search='', $filter='', $startDate='', $endDate='', $prounit='', $project='', $user=false, $limit=200, $offset=0,$templateId=false,$taskGenDate=false,$userId = false)
{
    $builder = $this->db->table('tasks t');

    $builder->select("
        t.id as taskId,
        t.task_gen_date,
        mt.title as task_title,
        pu.store as store_name,
        pu.oldstore_name,
        pu.oracle_code,
        t.created_at,
        t.task_gen_date,
        t.status as taskStatus,
        alw.name as allocated_to,
        alw.id as allocated_to_id,
        assi.name as assigned_to,
        assi.id as assigned_to_id,
        a.id as activity_id,
        a.activity_title,
        tsa.id as tsaactivityId,
        tsa.status as activityStatus,
        COALESCE(ac.comment,'Nill') as last_comment
    ");
    

    $builder->join('mastertasks mt','mt.id=t.created_from_template','left');
    $builder->join('project_unit pu','pu.id=t.project_unit','left');
    $builder->join('task_staff_activities tsa','tsa.task_id=t.id','left');
    $builder->join('activities a','a.id=tsa.task_activity_id','left');

    $builder->join('users alw','alw.id=pu.allocated_to','left');
    $builder->join('users assi','assi.id=pu.assigned_to','left');

    /* FAST COMMENT JOIN */

    $builder->join(
        "(SELECT task_id,activity_id,MAX(id) last_id
        FROM activities_comments
        GROUP BY task_id,activity_id) ac2",
        "ac2.task_id=t.id AND ac2.activity_id=a.id",
        "left"
    );

    $builder->join(
        "activities_comments ac",
        "ac.id=ac2.last_id",
        "left"
    );

    if($templateId && $taskGenDate){
        $builder->where('t.created_from_template',$templateId);
        $builder->where('t.task_gen_date',$taskGenDate);
    }

    if($search){
        $builder->groupStart()
        ->like('mt.title',$search)
        ->orLike('ac.comment',$search)
        ->groupEnd();
    }

    if($prounit && $prounit!='all')
        $builder->where('t.project_unit',$prounit);

    if($filter && $filter!='all')
        $builder->where('t.status',$filter);

    if($project && $project!='all')
        $builder->where('t.project_id',$project);

    if($startDate && $endDate){
        $builder->where('t.task_gen_date >=',$startDate.' 00:00:00');
        $builder->where('t.task_gen_date <=',$endDate.' 23:59:59');
    }
    if($userId){
        $builder->where('tsa.staff_id',session('user_data')['id']);
    }

    $builder->where('t.tasktype',1);

    $builder->groupBy(['t.id','a.id']);

    $builder->orderBy('t.id','DESC');

   // $builder->limit($limit,$offset);

    return $builder->get()->getResultArray();
}
public function getNearestDate()
{
    return $this->db->table('tasks')
        ->select('task_gen_date')
        ->where('task_gen_date <=', date('Y-m-d'))
        ->orderBy('task_gen_date', 'DESC')
        ->limit(1)
        ->get()
        ->getRow()
        ->task_gen_date ?? date('Y-m-d');
}
  public function _____getReportsOLd($search = '', $filter = '',$startDate ='' , $endDate = '', $prounit ='')
    {
        $subQueryActivities = "
            SELECT 
                a.task_id,
                COUNT(a.id) AS total_activities,
                SUM(CASE WHEN a.status = 'Pending' THEN 1 ELSE 0 END) AS pending_activities,
                SUM(CASE WHEN a.status = 'In_Progress' THEN 1 ELSE 0 END) AS inprogress_activities,
                SUM(CASE WHEN a.status = 'Completed' THEN 1 ELSE 0 END) AS completed_activities
            FROM activities a
            GROUP BY a.task_id
        ";

        $subQueryActivityStaff = "
            SELECT 
                a.task_id,
                COUNT(DISTINCT sa.staff_id) AS total_staff,
                COUNT(sa.id) AS total_activity_staff
            FROM activities a
            LEFT JOIN activity_staff sa ON sa.activity_id = a.id
            GROUP BY a.task_id
        ";

        $subQueryTaskAssignees = "
            SELECT 
                ta.task_id,
                COUNT(DISTINCT ta.staff_id) AS total_task_staff
            FROM task_assignees ta
            GROUP BY ta.task_id
        ";

        $builder = $this->db->table('tasks t');

        $builder->select("
            t.id AS task_id,
            t.title,
            t.status as master_task_status,
            act.total_activities,
            act.pending_activities,
            act.inprogress_activities,
            act.completed_activities,
            tas.total_task_staff,
            actst.total_staff,
            actst.total_activity_staff,
            pu.id as unit_id,
            pu.store as store
        ");

        $builder->join("($subQueryActivities) act", "act.task_id = t.id", "left");
        $builder->join("($subQueryActivityStaff) actst", "actst.task_id = t.id", "left");
        $builder->join("($subQueryTaskAssignees) tas", "tas.task_id = t.id", "left");
        $builder->join("project_unit as pu", "t.project_unit = pu.id", "left");


        $builder->groupBy("
            t.id,
            t.title,
            act.total_activities,
            act.pending_activities,
            act.inprogress_activities,
            act.completed_activities,
            tas.total_task_staff,
            actst.total_staff,
            actst.total_activity_staff
        ");

        if (!empty($search)) {
            $builder->like('t.title', $search);
        }

        if (!empty($filter) && $filter !== 'all') {
            $builder->where('t.status', $filter);
        }
        if (!empty($prounit) && $prounit !== 'all') {
            $builder->where('t.project_unit', $prounit);
        }
         if(!empty($startDate) && !empty($endDate)) {
            $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
            $endDate   = date('Y-m-d 23:59:59', strtotime($endDate));
            $builder->where('t.created_at >=', $startDate);
            $builder->where('t.created_at <=', $endDate);
        }

        return $builder->get()->getResultArray();
    }
}