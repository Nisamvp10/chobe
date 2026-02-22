<?php
namespace App\Models;

use CodeIgniter\Model;

class ProjectsModel extends Model{
    protected $table ='projects';
    protected $allowedFields = ['id','project','client_id','is_active','created_at','updated_at','created_by'];
    protected $primaryKey = 'id';

    function getCategory() {
        return $this->where('is_active',1)->findAll();
    }
}