<?php
use App\Models\UserMangeProjectsModel;
if(!function_exists('userProjects')) {
    function userProjects() {
        $userMangeProjectsModel = new UserMangeProjectsModel();
        $data = $userMangeProjectsModel->where('user_id',session('user_data')['id'])->findAll();
        $projectIds = array_column($data,'project_id');
        return $projectIds;
    }
}
