<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectunitModel extends Model {
    protected $table = 'project_unit';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id,','store','oldstore_name','polaris_code','oracle_code','contact_number','rm_mail','client_id','manager_id','regional_manager_id','start_date','status','project_unit_type','created_at',	
    ];
    
}