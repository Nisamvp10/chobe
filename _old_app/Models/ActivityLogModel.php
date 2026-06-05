<?php
namespace App\Models;
use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['activity_task_id', 'staff_id', 'started_at', 'ended_at', 'duration'];
}
