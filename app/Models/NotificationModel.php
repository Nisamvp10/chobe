<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'type', 'title', 'message', 'is_read', 'created_at'
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

    public function getStaffNotifications()
    {
        $builder = $this->db->table('notifications n');

        $user = session('user_data');
        $role = $user['role'] ?? null;
        $userId = $user['id'] ?? null;

        if ($role != 1 && $userId) { 
            // Join with staff to filter by branch
            $builder->join('users u', 'u.id = n.user_id')
                    ->where('u.id', $userId);
        }
        $builder->where('is_read',0);
        $builder->select('n.id,n.title, n.message, n.created_at');

        return $builder->orderBy('n.created_at', 'DESC')->get()->getResultArray();
    }
    

}
