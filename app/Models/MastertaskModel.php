<?php
namespace App\Models;
use App\Services\Teamheadtasks;
use CodeIgniter\Model;


class MastertaskModel extends Model{

    protected $table = 'mastertasks';
    protected $allowedFields = ['title','description','tasktype','project_unit_id','status','created_at','created_by','updated_at','updated_by'];
    protected $primaryKey = 'id';


    function mastertasks(){
        $this->teamheadtasks = new Teamheadtasks();
        if(hasRole() == 3) {
            $projectId = $this->teamheadtasks->roleByProjectId();
            return $this->where('project_unit_id', $projectId)->findAll();
        }
        else{
            return $this->where('status','active')->findAll();
        }
    }
}
        