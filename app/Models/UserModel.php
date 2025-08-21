<?php
namespace App\Models;

use CodeIgniter\Model;

Class UserModel extends Model{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields =  ['id','name', 'email','role', 'password', 'role_id','store_id','status','phone','position','hire_date','profileimg','booking_status','created_at'];
    protected function setPassword($password)
    {
        return password_hash($password,PASSWORD_DEFAULT);
    }

    function getUsers($search=false,$filter=false,$branch = false) {
    //    $builder = $this->db->table('users as u')
    //         ->select('u.id, u.name, u.email, u.phone, u.hire_date, u.profileimg, u.booking_status,u.status, u.position, r.role_name, b.branch_name as branch')
    //         ->select('GROUP_CONCAT(c.category ORDER BY c.category SEPARATOR ", ") as specialties')
    //         ->join('roles as r', 'r.id = u.role')
    //         ->join('branches as b', 'b.id = u.store_id')
    //         ->join('specialties as sp', 'sp.staff_id = u.id', 'left')
    //         ->join('categories as c', 'c.id = sp.speciality', 'left')
    //         ->groupBy('u.id')
    //         ->orderBy('u.id', 'DESC');

                $builder = $this->db->table('users as u')
                ->select('u.id, u.name, u.email, u.phone, u.hire_date, u.profileimg, u.booking_status, u.status, u.position, r.role_name, b.branch_name as branch')
                ->select('IFNULL(GROUP_CONCAT(c.category ORDER BY c.category SEPARATOR ", "), "") as specialties')
                ->join('roles as r', 'r.id = u.role', 'left')
                ->join('branches as b', 'b.id = u.store_id', 'left')
                ->join('specialties as sp', 'sp.staff_id = u.id', 'left')
                ->join('categories as c', 'c.id = sp.speciality', 'left')
                ->where('u.position !=','Develope6')
                ->groupBy('u.id')
                ->orderBy('u.id', 'DESC');



            if($filter !== 'all' && !empty($filter)){
            
               $builder->where('u.booking_status',$filter);
            }
            if($branch !== 'all' && !empty($branch)){
            
               $builder->where('b.id',$branch);
            }

            if (!empty($search)) {
                $builder->like('u.name',$search)
                ->orLike('u.email',$search)
                ->orLike('b.branch_name',$search)
                ->orLike('u.position',$search)
                ->orLike('phone',$search); 
            }

            return $builder->get()->getResultArray();
    }
    
    function getstaffRole($branchId =false) {
        $builder = $this->db->table('users as u')
            ->select('u.id,u.name,r.role_name as role')
            ->join('roles as r','r.id = u.role');
            if($branchId != 'all' && !empty($branchId)) {
                $builder->where('u.store_id',$branchId);
            }
            
        return $builder->get()->getResultArray();
    }
}