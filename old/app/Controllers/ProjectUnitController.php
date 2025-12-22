<?php
namespace App\Controllers;

use CodeIgniter\Controller;

use App\Models\ProjectunitModel;

class ProjectUnitController extends Controller
{
    protected $projectUnitModel;

    function __construct() {
        $this->projectUnitModel = new ProjectunitModel();
    }
    public function index()
    {
        $page = (!hasPermission('','view_project_unit')) ?  lang('Custom.accessDenied') : 'Project Unit';
        return view('admin/project_unit/index',compact('page'));
    }

    function save() {
        $validStatus = false;
        $validStatus = '';
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success'=> false, 'message' => 'invalid Request']);
        }
        if(!haspermission(session('user_data')['role'],'create_project_unit')) {
             return $this->response->setJSON(['success' => false, 'message' => 'Permission Denied']);
        }

        $rules = [
            'store'    => 'required|min_length[3]|max_length[100]',
            'rmname'  => 'required|min_length[2]',
            'rm_mail'  => 'required|min_length[2]',
            'status'  => 'required',
        ];

        if(!$this->validate($rules))
        {
            return $this->response->setJSON([
                'succeass' => false,
                'errors' => $this->validator->getErrors()        
            ]);
        }

        $store   = $this->request->getVar('store');
        $location = $this->request->getVar('location');

        $contactPerson = $this->request->getVar('rmname');
        $rm_mail = $this->request->getVar('rm_mail');
        $oldstore = $this->request->getVar('oldstore');
        $storemail = $this->request->getVar('storemail');
        $polaris_code = $this->request->getVar('polaris_code');
        $oracle_code = $this->request->getVar('oracle_code');

        $id       = decryptor($this->request->getVar('branchId'));

        $data = [
            'store' => $store,
            'oldstore_name' => $oldstore,
            'store_mailid' => $storemail,
            'polaris_code' => $polaris_code,
            'oracle_code' => $oracle_code,
            'contact_person'  => $contactPerson,
            'rm_mail' => $rm_mail,
            'status'    => 1,
            'project_unit_type' => $this->request->getPost('status'),
        ];

        if($id){
            if($this->projectUnitModel->update($id, $data)){
                
                $validStatus = true;
                $validMsg = 'Updated successfully!';

            }else{
                
                $validMsg = 'something went wrong Please Try again';
            }
        }else{
            if($this->projectUnitModel->insert($data)){

                $validStatus = true;
                $validMsg = 'New Branch Adedd';

            }else{
                
                $validMsg = 'something went wrong Please Try again';
            }
        }
        return $this->response->setJSON([
            'success' => $validStatus,
            'message' => $validMsg
        ]);
    }

    function list() {
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission('','view_project_unit')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $search = $this->request->getVar('search');
        $filter = $this->request->getVar('filter');

        $builder = $this->projectUnitModel->select('id,store,contact_person,oracle_code,polaris_code,rm_mail')->orderBy('id DESC');

        if($filter !=='all'){
            $builder->where('is_active',$filter);
        }

        if(!empty($search))
        {
            $builder->groupStart()
                ->like('store',$search)
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
}