<?php
namespace App\Models;

use CodeIgniter\Model;

class TaskStaffActivityModel extends Model {
    protected $table = 'task_staff_activities';
    protected $allowedFields = ['id','task_id','task_activity_id','staff_id','status','complated_by','commet_status','started_at','completed_at','progress','started_by','is_open','created_at','updated_at'];
    protected $primaryKey = 'id';
}