<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'type', 'title','task_id', 'message', 'is_read', 'created_at','created_by'
    ];
    protected $useTimestamps = false;

    public function getBranchNotifications()
    {
        $builder = $this->db->table('notifications n');

        $user = session('user_data');
        $role = $user['role'] ?? null;
        $branchId = getStore() ?? null;

        if ($role != 1 && $branchId) { 
            // Join with staff to filter by branch
            $builder->join('users u', 'u.id = n.user_id')
                    ->where('u.store_id', $branchId);
        }
        $builder->where('is_read',0);
        $builder->select('n.id,n.title, n.message, n.created_at');

        return $builder->orderBy('n.created_at', 'DESC')->get()->getResultArray();
    }

    public function getStaffNotifications($isread = FAlse)
    {
        $builder = $this->db->table('notifications n');

        $user = session('user_data');
        $role = $user['role'] ?? null;
        $userId = $user['id'] ?? null;

        if ($role != 1 && $userId) { 
            // Join with staff to filter by branch
            $builder->join('users u', 'u.id = n.user_id');
             $builder->join('users cu', 'cu.id = n.created_by')
                    ->where('u.id', $userId);
        }
        if($isread) {
            $builder->where('is_read',0);
        }
        $builder->select('n.id,n.title,n.is_read, n.message, u.name,n.created_at,u.profileimg,cu.name as created_by_name,cu.profileimg as created_by_image');

        return $builder->orderBy('n.id', 'DESC')->get()->getResultArray();
    }
}
