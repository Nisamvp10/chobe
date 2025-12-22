<?php
namespace App\Models;

use CodeIgniter\Model;
class ActivityReplayModel extends Model{

    protected $table = "activity_task_replies";
    protected $primaryKey ='id';
    protected $allowedFields = ['id','task_id','user_id','master_task_id','master_activity_id','reply_text'];

    function getHistory($taskId = false) {
        $builder = $this->db->table('activity_task_replies t')
            ->select('u.name,u.profileimg,t.reply_text,t.created_at')
            ->join('users u','t.user_id = u.id')
            ->where('t.task_id',$taskId);
        $query = $builder->get()->getResultArray();
        return $query;
    }

    public function getReplay($taskId,$activityId) {
        $builder = $this->db->table('task_staff_activities as tsa')
                  ->select('u.name,atr.user_id,u.profileimg,atr.created_at,atr.reply_text')
                  ->join('activity_task_replies as atr','tsa.id = atr.task_id')
                  ->join('users u','atr.user_id = u.id')
                  ->where('atr.master_task_id',$taskId)
                  ->where('atr.master_activity_id',$activityId);
        return $builder->get()->getResultArray();
    }
}