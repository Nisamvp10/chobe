<?php
namespace App\Models;
use App\Services\Teamheadtasks;
use CodeIgniter\Model;

class ProjectsModel extends Model{
    protected $table ='projects';
    protected $allowedFields = ['id','project','client_id','is_active','created_at','updated_at','created_by'];
    protected $primaryKey = 'id';

    function getCategory() {
        return $this->where('is_active',1)->findAll();
    }
    public function projectunits(){
        $this->teamheadtasks = new Teamheadtasks();
        if(hasRole() ==3){
            $projectId = $this->teamheadtasks->roleByProjectId();
            return $this->where(['id'=> $projectId,'is_active'=>1])->findAll();
        }else{
            return $this->where('is_active',1)->find();
        }
    }

}