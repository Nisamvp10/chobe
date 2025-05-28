<?php
namespace App\Models;

use CodeIgniter\Model;

class AssigntaskModel extends Model {
protected $table = 'task_assignees';
protected $allowedFields = ['id','task_id','staff_id','role','status'];
protected $primaryKey = 'id';

function getParticipants ($id) {

    $builder = $this->db->table('task_assignees ta')
        ->select('ta.staff_id,ta.role,u.name,r.role_name as roleName')
        ->join('tasks t','t.id = ta.task_id')
        ->join('users u','ta.staff_id = u.id')
        ->join('roles r','u.role = r.id')
        ->where('ta.task_id',$id);
        $result = $builder->get()->getResultArray();
        echo $this->db->getLastQuery();
}

}