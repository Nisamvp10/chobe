<?php
namespace App\Models;
use CodeIgniter\Model;
class SpecialtiesModel extends Model{
    protected $table = 'specialties';
    protected $allowedFields = ['id','staff_id','speciality','created_at'];
    protected $primaryKey = 'id';

    function getSpecialty($staffId) {
        $builder = $this->db->table('specialties as sp')
            ->select('sp.*,c.category,sp.speciality')
            ->join('categories as c','c.id = sp.speciality','left')
            ->where('sp.staff_id',$staffId);
        return $builder->get()->getResultArray();
    }

}