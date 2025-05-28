<?php
namespace App\Controllers;
use App\Models\PermissionsModel;
use App\Models\UserModel;
use App\Models\RolesModel;
use App\Models\RolepermissionsModel;

Class Permissions extends BaseController{
    function index(){
        $permissionsModel = new PermissionsModel();
        $page = "Permissions";
        return view('admin/permissions/index',compact('page'));
    }
    function list() {
        $users = new UserModel();

        if (!$this->request->isAJAX()){
            return $this->reponse->setJSON(['success' => false,'message' => "Invalid Request"]);
        }

        $userModel = new UserModel();

        $search = $this->request->getVar('search');
        $filter = $this->request->getVar('filter');
        $branch = $this->request->getVar('branch');

        $staff = $userModel->getUsers($search,$filter,$branch);

        foreach ($staff as &$staffKey) {
            $staffKey['encrypted_id'] = encryptor($staffKey['id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'staff' => $staff
        ]);
    }
    
    function checkpermission($id=false) {
        if (!haspermission(session('user_data')['role'], 'permissions_view')) {
            return redirect()->to('custom_404');
        }
        //$id = decryptor($id);
        $rolesModel = new RolesModel();
        $permissionsModel = new PermissionsModel();
        $permisions =  $permissionsModel->findAll();
        $roles = $rolesModel->findAll();
        return view('admin/permissions/permissions',compact('permisions','roles'));
    }

    function controls() {
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }

        $rolesModel = new RolesModel();
        $permissionsModel = new PermissionsModel();
        $rolePermisions = new RolePermissionsModel();

        $roleId = $this->request->getGet('role') ?? 1;
        $search = $this->request->getPost('search');
        $filter = $this->request->getPost('filter');
        $permisions =  $permissionsModel->findAll();
        $roleId = ( $roleId ?  $roleId  : 1);
        $assigned = $rolePermisions->where('role_id', $roleId)->findAll();
        $assignedPermissionIds = array_column($assigned, 'permission_id');

        return $this->response->setJSON([
            'success' =>true,
            'permissions' => $permisions,
            'assignedPermissions' => $assignedPermissionIds
        ]);
    }
    function save() {

        if(!$this->request->isAJAX()) {

            return $this->response->setJSON(['success' => FALSE, 'message' => 'Invalid Request']);
        }
        $roleValidate = [
            'role' => 'required'
        ];

        if(!$this->validate($roleValidate)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }
        $roleId = $this->request->getPost('role');
        $rolePermisions = new RolePermissionsModel();

        $permissions = $this->request->getPost('permissions');
        $rolePermisions->where('role_id',$roleId)->delete();

        if (!empty($permissions)) {
            foreach ($permissions as $permission) {
               $rolePermisions->insert([
                    'role_id'       => $roleId,
                    'permission_id' => $permission
                ]);
            }
        }

         return $this->response->setJSON(['success' => true, 'message' => 'Permission Successfull Added']);
        
    }
}