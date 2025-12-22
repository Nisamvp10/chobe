<?php
use CodeIgniter\Database\BaseConnection;
use App\Model\PermissiongeneratorModel;
if (!function_exists('hasPermission')) {
    
    function hasPermission($roleId=false,$permission) {
        $db = \Config\Database::connect();
        $roleId =  session('user_data')['role'];
        $query = $db->table('role_permissions as rp')
            ->select('p.permission_name')
            ->join('permissions as p','p.id = rp.permission_id')
            ->where('rp.role_id',$roleId)
            ->where('p.permission_name',$permission)->get();
            
        if ($query->getNumRows() > 0) {
            return true;
        }
    }
}