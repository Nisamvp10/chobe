<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectunitModel extends Model {
    protected $table = 'project_unit';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id,','contact_person','store','oldstore_name','store_mailid','polaris_code','oracle_code','rm_mail','status','project_unit_type','created_at',	
    ];
    
}