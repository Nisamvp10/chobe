<?php
namespace App\Models;

use CodeIgniter\Model;

class ClientsModel extends Model {
    protected $table = 'clients';
    protected $allowedFields = ['id','name','email','phone','profile','join_date','note','created_at','updated_at','status'];
    protected $primaryKey ='id';

    function getClients($search,$filter) {

        $builder = $this->db->table('clients as c')
        ->select('c.id, c.name, c.email, c.note, c.join_date, c.profile,cc.authorized_personnel,cc.email,cc.phone,cc.designation,cc.id as infoId' )
        ->join('client_contacts as cc', 'c.id = cc.client_id', 'left')
        ->where('c.status',1);
        

        if (!empty($search)) {
            $builder->groupStart()
                ->like('c.name', $search)
                ->orLike('cc.phone', $search)
                ->orLike('cc.email', $search)
                ->groupEnd();
        }

        $builder->orderBy('c.id', 'DESC');
        return $builder->get()->getResultArray();
    }

    function getClinentById($id) {
         $builder = $this->db->table('clients as c')
        ->select('c.id, c.name, c.email, c.note, c.join_date, c.profile,cc.authorized_personnel,cc.email,cc.phone,cc.designation,cc.id as infoId' )
        ->join('client_contacts as cc', 'c.id = cc.client_id', 'left')
        ->where('c.id',$id)
        ->get()->getResultArray();
        return $builder;
    }
}