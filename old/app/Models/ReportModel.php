<?php

namespace App\Models;

use CodeIgniter\Model;
class ReportModel extends Model
{
    protected $table = 'tasks';   // <-- important
    protected $primaryKey = 'id';
  public function getReports($search = '', $filter = '',$startDate ='' , $endDate = '', $prounit ='')
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