<?php
namespace App\Models;

use CodeIgniter\Model;

class UserMangeProjectsModel extends Model{
    protected $table ='user_branches';
    protected $allowedFields = ['id','user_id','project_id','created_at','updated_at'];
    protected $primaryKey = 'id';

}