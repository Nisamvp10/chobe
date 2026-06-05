<?php
namespace App\Models;

use CodeIgniter\Model;

class MasterroleModel extends Model {
    protected $table ="user_position";
    protected $allowedFields = ['id','name','parent_id','level','type','created_at','status'];
    protected $primaryKey = 'id';
}