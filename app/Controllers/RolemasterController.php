<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MasterroleModel;
class RolemasterController extends Controller {

    protected $masterRoleModel;

    function __construct() 
    {
        $this->masterRoleModel = new MasterroleModel();
    }

    public function index() {
          $page = (!hasPermission('','role_master')) ? '' : 'Role Master';
          $route =   (hasPermission('','role_master')) ? 'admin/rolemaster/index' : 'admin/pages-error-404';
          $roles = $this->masterRoleModel->orderBy('level','ASC')->findAll();
          return view($route,compact('page','roles'));
    }

    public function save(){
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => ' Invalid Request']);
        }
        if(!haspermission('','role_master')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $validSuccess = false;
        $validMsg = '';

        $rules = [
            'role' => 'required|min_length[3]|max_length[100]',
        ];

        if(!$this->validate($rules)){
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        $project = $this->request->getVar('role');
        $id = decryptor($this->request->getVar('roleId'));

        $parentId = $this->request->getPost('parent_id');

        $level = 1;
        if ($parentId) {
            $parent = $this->masterRoleModel->find($parentId);
            $level = $parent['level'] + 1;
        }


        $data = [
            'name' => $project,
            'parent_id' => $parentId ?: null,
            'level' => $level
        ];
        
        if($id)
        {
            if($this->masterRoleModel->update($id, $data)){

                $validSuccess = true;
                $validMsg = "Updated successfully!";
            }else{
                $validMsg = 'something went wrong Please Try again';
            }
        }else{
            $data['created_at'] = date('Y-m-d H:i:s');
           // $data['created_by'] = session('user_data')['id'];
            if($this->masterRoleModel->insert($data)){

                $validSuccess = true;
                $validMsg = "New Project Added";
            }else{
                $validMsg = 'something went wrong Please Try again';
            }
        }

        return $this->response->setJSON([
            'success' => $validSuccess,
            'message' => $validMsg,
        ]);
    }

    function masterList(){

            if(!$this->request->isAJAX()){
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
            }
            if(!haspermission('','role_master')) {
                return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
            }
            $search = $this->request->getVar('search');
            $filter = $this->request->getVar('filter');

            $builder = $this->masterRoleModel->select('id,name,type,parent_id,level')->orderBy('id DESC');

            if($filter !=='all'){
                //$builder->where('is_active',$filter);
            }

            if(!empty($search))
            {
                $builder->groupStart()
                    ->like('name',$search)
                    ->groupEnd();
            }

            $projects = $builder->findAll();
            foreach($projects as &$project){
                $project['encrypted_id'] = encryptor($project['id']);
            }

            return $this->response->setJSON([
                'success' => true,
                'projects' => $projects,
            ]);
        }

    public function getRoleinfo($id=false) {
      
        if(!haspermission('','role_master')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        if($id) {
            $id = decryptor($id);
            $result = $this->masterRoleModel->select('id,name,parent_id,level')->where('id',$id)->get()->getRow();
        }else{
            $result = null;
        }
        return $this->response->setJSON($result);
    }
    
}