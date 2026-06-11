<?php
namespace App\Services;
use App\Models\TaskModel;
use App\Models\UserModel;
class Common {
    protected $taskModel;
    protected $userinfoModel;
    function __construct() {
        $this->taskModel = new TaskModel();
        $this->userinfoModel = new UserModel();
    }
    
    public function getBranchNameBytaskId($taskId) {
        $builder = $this->taskModel->select('tasks.id, tasks.title,tasks.task_gen_date, project_unit.store, project_unit.oldstore_name, project_unit.oracle_code, project_unit.polaris_code')
        ->join('project_unit', 'project_unit.id = tasks.project_unit')
        ->where('tasks.id', $taskId);
        $result = $builder->get()->getRow();
        return $result;
    }

    public function updateTaskActivitiesUI($template,$taskGenDate) {
      $db = \Config\Database::connect();

      
        $sql1 = "
            UPDATE task_staff_activities tsa
            INNER JOIN tasks t ON t.id = tsa.task_id
            SET tsa.commet_status = 2
            WHERE t.created_from_template = ?
            AND t.task_gen_date = ?
        ";

        $db->query($sql1, [$template, $taskGenDate]);

      
        $sql2 = "
            UPDATE tasks t
            SET t.ui = 2
            WHERE t.created_from_template = ?
            AND t.task_gen_date = ?
        ";

        $db->query($sql2, [$template, $taskGenDate]);

        return true;
    }
    function getUserdata(){

        $session = session('user_data')['id'];
        if($session){
            $result = $this->userinfoModel->select('name')->where('id',$session)->get()->getRow();
           return $result;
        }
    }
}