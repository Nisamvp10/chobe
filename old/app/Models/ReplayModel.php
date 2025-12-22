<?php
namespace App\Models;

use CodeIgniter\Model;

class ReplayModel extends Model {
    protected $table = "task_replies";
    protected $primaryKey ='id';
    protected $allowedFields = ['id','task_id','user_id','reply_text'];

    function getHistory($taskId = false) {
        $builder = $this->db->table('task_replies t')
            ->select('u.name,u.profileimg,t.reply_text,t.created_at')
            ->join('users u','t.user_id = u.id')
            ->where('t.task_id',$taskId);
        $query = $builder->get()->getResultArray();
        return $query;
    }
}