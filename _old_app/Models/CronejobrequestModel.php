<?php
namespace App\Models;

use CodeIgniter\Model;

class CronejobrequestModel extends Model
{
    protected $table = 'cronjob_requests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['created_tasks','message','created_at'];
}