<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model{
    protected $table = 'categories';
    protected $allowedFields = ['id','category','is_active'];
    protected $primaryKey = 'id';

    function getCategory() {
        return $this->where('is_active',1)->findAll();
    }
}