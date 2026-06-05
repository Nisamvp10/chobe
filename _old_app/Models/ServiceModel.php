<?php
namespace App\Models;
use CodeIgniter\Model;

class ServiceModel extends Model{
    protected $table = 'services';
    protected $allowedFields =[
        'id','name','category','duration','price','description','image_url','is_popular','is_active','created_at','updated_at',
    ];
    protected $primaryKey = 'id';

    function servicesLsit($filter,$search){
         $builder = $this->db->table('services s')
        ->select('s.id,s.name,s.category,s.price,s.duration,s.description,s.image_url,s.is_popular, c.category as category_name')
        ->join('categories c', 's.category = c.id', 'left')
        //->where('s.status', 1)
        ->orderBy('s.id', 'DESC');
        //->get();
        if($filter !== 'all'){
            $builder->where('s.is_popular',$filter);
        }
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('s.name', $search)
                    ->orLike('c.category', $search)
                    ->orLike('s.description', $search)
                    ->orLike('s.price', $search)
                    ->groupEnd();
        }
        return $builder->get()->getResultArray();
    
    }
}