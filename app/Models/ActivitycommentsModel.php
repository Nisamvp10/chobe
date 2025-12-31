<?php
namespace App\Models;

use CodeIgniter\Model;

class ActivitycommentsModel extends Model{
    protected $table ='activities_comments';
    protected $allowedFields = ['id','task_id','activity_id','user_id','comment','status','created_by','created_at'];
    protected $primaryKey = 'id';

    public function allComments($taskId=false,$activityId) {
        $builder = $this->db->table('activities_comments as ac')
                    ->select('ac.id,ac.created_at,ac.created_by,ac.comment,
                    u.name')
                    ->join('users as u','u.id= ac.user_id')
                    ->where(['ac.task_id' => $taskId,'ac.activity_id'=>$activityId])
                    ->orderBy('ac.id ASC');
        $result = $builder->get()->getResultArray();
        return $result;
    }
}