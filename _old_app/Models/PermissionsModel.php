<?php

namespace App\Models;
use CodeIgniter\Model;

Class PermissionsModel extends Model{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','permission_name'];

}