<?php
namespace App\Models;

use CodeIgniter\Model;

class ClientsModel extends Model {
    protected $table = 'clients';
    protected $allowedFields = ['id','name','email','phone','profile','join_date','note','created_at','updated_at','status'];
    protected $primaryKey ='id';

    function getClients($search,$filter) {

        $builder = $this->db->table('clients as c')
        ->select('c.id, c.name, c.email, c.phone, c.join_date, c.profile, COUNT(a.id) as total_visits,SUM(a.duration) as spendtime , SUM(a.price) spend,a.booking_date as lastvisit')
        ->join('appointments as a', 'c.id = a.client_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('c.name', $search)
                ->orLike('c.phone', $search)
                ->orLike('c.email', $search)
                ->groupEnd();
        }

        $builder->groupBy('c.id')
            ->orderBy('c.id', 'DESC');
        return $builder->get()->getResultArray();
    }
}