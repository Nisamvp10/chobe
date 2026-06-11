<?php
namespace App\Models;

use CodeIgniter\Model;

class ActivitycommentsModel extends Model{
    protected $table ='activities_comments';
    protected $allowedFields = ['id','task_id','activity_id','user_id','comment','status','created_by','created_at'];
    protected $primaryKey = 'id';

    public function allComments($taskId=false,$activityId =false) {
        $builder = $this->db->table('activities_comments as ac')
                    ->select('ac.id,ac.created_at,ac.created_by,ac.comment,
                    u.name')
                    ->join('users as u','u.id= ac.user_id')
                    ->where(['ac.task_id' => $taskId]);
                    if($activityId) {
                        $builder->where('ac.activity_id',$activityId);
                    }
                    $builder->orderBy('ac.id ASC');
        $result = $builder->get()->getResultArray();
        return $result;
    }
    public function updateCommentUsernames()
    {
        $db = \Config\Database::connect();
        $limit = 500;
        $offset = 0;

        do {
            $builder = $db->table('activities_comments c');
            $builder->join('users u', 'c.user_id = u.id');
            $builder->select('c.id, u.name');
            $builder->limit($limit, $offset);
            $results = $builder->get()->getResult();

            foreach ($results as $row) {
                $db->table('activities_comments')
                ->where('id', $row->id)
                ->update(['user_name' => $row->name]);
                echo (string) db_connect()->getLastQuery();
            }

            $offset += $limit;
            
        } while (count($results) > 0);
        
        return "Done";
}
}