<?php
namespace App\Models;
use CodeIgniter\Model;

Class RolesModel extends Model{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','role_name'];
    protected $returnType = 'object';

    function getRole($roleId)
    {
        return $this->where('id',$roleId)->first();
    }

    
}