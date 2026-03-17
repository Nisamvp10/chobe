<?php
namespace App\Models;

use CodeIgniter\Model;

class ProjectunitlogModel extends Model{
    protected $table = 'project_unit_log';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_unit_id',
        'rm_id',
        'sm_id',
        'allocate_to_id',
        'assign_to_id',
        'created_at',	
    ];

    //create a log .txt files for changes 
    public  function projectunitlog($data){
        
    }
}