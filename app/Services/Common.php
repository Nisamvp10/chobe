<?php
namespace App\Services;
use App\Models\TaskModel;

class Common {
    protected $taskModel;
    function __construct() {
        $this->taskModel = new TaskModel();
    }
    
    public function getBranchNameBytaskId($taskId) {
        $builder = $this->taskModel->select('tasks.id, tasks.title,tasks.task_gen_date, project_unit.store, project_unit.oldstore_name, project_unit.oracle_code, project_unit.polaris_code')
        ->join('project_unit', 'project_unit.id = tasks.project_id')
        ->where('tasks.id', $taskId);
        $result = $builder->get()->getRow();
        return $result;
    }
}