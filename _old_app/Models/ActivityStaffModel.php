<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityStaffModel extends Model
{
    protected $table = 'activity_staff';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'activity_id', 
        'staff_id', 
        'status'
    ];
}
