<?php
namespace App\Models;

use CodeIgniter\Model;

class ReplayModel extends Model {
    protected $table = "task_replies";
    protected $primaryKey ='id';
    protected $allowedFields = ['id','task_id','user_id','reply_text'];
}