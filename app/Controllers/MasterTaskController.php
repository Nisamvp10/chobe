<?php
namespace App\Controllers;
use App\Models\BranchesModel;
use App\Models\ProjectsModel;
use App\Models\ProjectunitModel;
use App\Models\MastertaskModel;
use App\Models\UserModel;

class MasterTaskController extends BaseCo   ntroller
{
    public $branchModel;
    public $projects;
    public $projectUnitModel;
    public $masterTaskModel;
    public $taskTypeModel;
    public $userModel;
    public function __construct()
    {
        $this->branchModel = new BranchesModel();
        $this->projects = new ProjectsModel();
        $this->projectUnitModel = new ProjectunitModel();
        $this->masterTaskModel = new MastertaskModel();
        $this->userModel = new UserModel(); 
    }
    public function index()
    {
        $page = "Master Task";
        $branches = $this->branchModel->where('status','active')->findAll();
        $projects = $this->projects->where('is_active',1)->findAll();
        $projectUnits = $this->projectUnitModel->where('status',1)->findAll();
        return view('admin/mastertask/index',compact('page','branches','projects','projectUnits'));
    }

    function list(){
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission('','task_view')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $search = $this->request->getVar('search');
        $result = $this->masterTaskModel->where('status','active')->findAll();
        if($result){
            foreach($result as &$row){
                $row['id'] = encryptor($row['id']);
            }
            return $this->response->setJSON(['success' => true,'data' => $result]);

        }
        return $this->response->setJSON(['success' => false,'message' => 'No Data Found']);
    }
    function create($id = null){
        
        if(!haspermission('','create_task')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $page = "Create Master Task";
        $branches = $this->branchModel->where('status','active')->findAll();
        $projects = $this->projects->where('is_active',1)->findAll();
        $staffs =  $this->userModel->where('role !=',1)->findAll();
        $projectUnits = $this->projectUnitModel->where('status',1)->findAll();
        $data = [];
        if($id){
            $id = decryptor($id);
            $data = $this->masterTaskModel->where('id',$id)->first();
            if(!$data){
                return redirect()->to(base_url('master-task'));
            }
            $page = "Edit Master Task";
        }
        return view('admin/mastertask/create',compact('page','branches','projects','staffs','projectUnits','data'));
    }
    function save(){
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission('','create_task')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'taskmode' => 'required',
            'project' => 'required',
        ];
        if(!$this->validate($rules)){
            return $this->response->setJSON(['success' => false,'message' => $this->validator->getErrors()]);
        }
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'taskmode' => $this->request->getPost('taskmode'),
            'status'  => 1,
            'project_unit_id' => $this->request->getPost('project'),
        ];
        
        $id = decryptor($this->request->getPost('id'));
        if($id){
            $data['id'] = $id;
            if($this->masterTaskModel->update($id,$data)){
                return $this->response->setJSON(['success' => true,'message' => 'Master Task Updated Successfully']);
            }
        }else{
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = session('user_data')['id'] ?? null;
            if($this->masterTaskModel->insert($data)){
                return $this->response->setJSON(['success' => true,'message' => 'Master Task Created Successfully']);
            }
        }
       
        return $this->response->setJSON(['success' => false,'message' => 'Failed to Create Master Task']);
    }

    function delete($id=null){
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission('','task_delete')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $id = decryptor($id);
        if($this->masterTaskModel->update($id,['status' => 2])){
            return $this->response->setJSON(['success' => true,'message' => 'Master Task Deleted Successfully']);
        }
        return $this->response->setJSON(['success' => false,'message' => 'Failed to Delete Master Task']);
    }
}