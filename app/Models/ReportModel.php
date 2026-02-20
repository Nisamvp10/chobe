<?php

namespace App\Models;

use CodeIgniter\Model;
class ReportModel extends Model
{
    protected $table = 'tasks';   // <-- important
    protected $primaryKey = 'id';

     public function getReports($search = '', $filter = '', $startDate = '', $endDate = '', $prounit = '', $project = '')
    {
       $builder = $this->db->table('tasks t');

        $builder = $this->db->table('tasks t');

            $builder->select([
                't.id as taskId',
                'mt.title as task_title',
                'pu.store as store_name',
                'pu.oldstore_name',
                'pu.oracle_code',
                't.created_at',
                't.status as taskStatus',
                'tsa.status as activityStatus',
                'alw.name as allocated_to',
                'alw.id as allocated_to_id',
                'assi.name as assigned_to',
                'assi.id as assigned_to_id',
                'a.id as activity_id',
                'a.activity_title',

                //  Activity status (group-based)
                "CASE 
                    WHEN SUM(tsa.status = 'completed') > 0 
                    THEN 'completed' 
                    ELSE 'pending' 
                END as activity_status",

                //  Last comment per TASK + ACTIVITY
                'COALESCE(ac.comment, "Nill") as last_comment',
                'ac.created_at as comment_time'
            ]);

            /* =================== JOINS =================== */

            $builder->join('mastertasks mt', 'mt.id = t.created_from_template', 'left');//7 mastertasks now 
            $builder->join('project_unit pu', 'pu.id = t.project_unit', 'left'); //5 projrct units now 
            $builder->join('task_staff_activities tsa', 'tsa.task_id = t.id', 'left');
            $builder->join('users alw', 'alw.id = pu.allocated_to', 'left');
            $builder->join('users assi', 'assi.id = pu.assigned_to', 'left');
            $builder->join('activities a', 'a.id = tsa.task_activity_id', 'left');

            /*  JOIN LAST COMMENT PER TASK + ACTIVITY */
            $builder->join(
                '(SELECT ac1.*
                FROM activities_comments ac1
                INNER JOIN (
                    SELECT task_id, activity_id, MAX(id) AS last_id
                    FROM activities_comments
                    GROUP BY task_id, activity_id
                ) ac2
                ON ac1.id = ac2.last_id
                ) ac',
                'ac.task_id = t.id AND ac.activity_id = a.id',
                'left'
            );

            /* =================== GROUP =================== */

            $builder->groupBy([
                't.id',
                'a.id'
            ]);
        //$builder->where('t.id', 284);

        /* GROUP BY TASK + ACTIVITY */
        $builder->groupBy([
            't.id',
            'a.id'
        ]);



        // $query = $builder->get();
        // $result = $query->getResultArray();


        /*
        |------------------------------------------------------
        | OPTIONAL: Order
        |------------------------------------------------------
        */
        // $builder->orderBy('t.id', 'ASC');
        // $builder->orderBy('a.id', 'ASC');
        //$builder->join('activities_comments ac', 'ac.task_id = t.id', 'left');
    
        if (!empty($search) && $search != 'all') {
            $builder->groupStart()
                ->like('t.title', $search)
                ->orLike('ac.comment', $search)
            ->groupEnd();
        }

        if (!empty($prounit) && $prounit != 'all') {
            $builder->where('t.project_unit', $prounit);
        }

        if (!empty($filter) && $filter != 'all') {
            $builder->where('t.status', $filter);
        }
        if (!empty($project) && $project != 'all') {
            $builder->where('t.project_id', $project);
        }

        if (!empty($startDate) && !empty($endDate)) {
            $builder->where('DATE(t.created_at) >=', $startDate);
            $builder->where('DATE(t.created_at) <=', $endDate);
        }

        return $builder->get()->getResultArray();

        
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