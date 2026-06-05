<?php
namespace App\Models;
use CodeIgniter\Model;

Class PermissiongeneratorModel extends Model{
    protected $table = 'permission_access';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','user_id','role_id','permission_id'];

    function getPermissionByRole($roleId){

        //$db = \Config\Database::connect();
        $builder = $this->db->table('permission_access as pa');
        $builder->select('p.permission_name,pa.permission_id');
        $builder->join('permissions as p','p.id = pa.permission_id','LEFT');
        $builder->where('pa.role_id', $roleId);
        $query = $builder->get();
        return $query->getResultArray();

    }
}