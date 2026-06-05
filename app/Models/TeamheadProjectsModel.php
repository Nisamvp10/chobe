<?php
namespace App\Models;

use CodeIgniter\Model;

class TeamheadProjectsModel extends Model {
    protected $table = 'team_head_projects';
    protected $allowedFields = ['staff_id','project_type_id','created_at'];
    protected $primaryKey = 'id';
    //timestamp 
}