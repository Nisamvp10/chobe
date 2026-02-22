<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\ProjectsModel;
use App\Models\ClientsModel;

class ProjectsController extends Controller{

    protected $projectModel;
    protected $clientsModel;
    
    function __construct() {

        $this->projectModel = new ProjectsModel();
        $this->clientsModel = new ClientsModel();

    }
    function index() {
        $page = "Projects";
        $clients = $this->clientsModel->where('status',1)->orderBy('id','DESC')->findAll();
        return view('admin/projects/index',compact('page','clients'));
    }

    function create($id =false)
    {
        $page = "Edit Project" ; 
        $id = decryptor($id);
        $data = $this->projectModel->where(['id'=> $id,'is_active' =>1])->first();
        $clients = $this->clientsModel->where('status',1)->orderBy('id','DESC')->findAll();
        return view('admin/projects/edit',compact('page','data','clients'));
    }

    function save(){
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'message' => ' Invalid Request']);
        }
        if(!haspermission('','create_project')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $validSuccess = false;
        $validMsg = '';

        $rules = [
            'project' => 'required|min_length[3]|max_length[100]',
            'client' => 'required',
        ];

        if(!$this->validate($rules)){
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        $project = $this->request->getVar('project');
        $client = $this->request->getVar('client');
        $id = decryptor($this->request->getVar('projectId'));

        $data = [
            'project' => $project,
            'client_id' => $client,
        ];
        
        if($id)
        {
            if($this->projectModel->update($id, $data)){

                $validSuccess = true;
                $validMsg = "Updated successfully!";
            }else{
                $validMsg = 'something went wrong Please Try again';
            }
        }else{
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = session('user_data')['id'];
            if($this->projectModel->insert($data)){

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
    function projectList(){

        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission('','view_projects')) {
            return $this->response->setJSON(['success' => false,'message' => ' Permission Denied']);
        }
        $search = $this->request->getVar('search');
        $filter = $this->request->getVar('filter');

        $builder = $this->projectModel->select('projects.id,projects.project,projects.is_active,clients.name as client_name')->orderBy('projects.id DESC')->join('clients', 'clients.id = projects.client_id');

        if($filter !=='all'){
            $builder->where('projects.is_active',$filter);
        }

        if(!empty($search))
        {
            $builder->groupStart()
                ->like('projects.project',$search)
                ->like('clients.name',$search)
                ->groupEnd();
        }
        $builder->where('projects.is_active',1);
        $projects = $builder->findAll();
        foreach($projects as &$project){
            $project['encrypted_id'] = encryptor($project['id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'projects' => $projects,
        ]);
    }
    function delete()
    {
        $id = decryptor($this->request->getVar('id'));
        $validSuccess = false;
        $validMsg = "oops! Item Not Valid ";
        if($id)
        {
            $branch = $this->projectModel->find($id);
            if($branch){
                //check assigned any staff and appoint ment in the branch 
                if( $this->projectModel->update($id,['is_active'=>0])){
                    $validSuccess = true;
                    $validMsg = 'Branch Inactive successfully!';
                }else{
                    $validMsg = 'Oops. Please try again.';
                }
            }
        }
        return $this->response->setJson([
            'success' => $validSuccess,
            'message' => $validMsg
        ]);
    }
    function unlock()
    {
        $id = decryptor($this->request->getVar('id'));
        $validSuccess = false;
        $validMsg = "oops! Item Not Valid ";
        if($id)
        {
            $branch = $this->projectModel->find($id);
            if($branch){
                //check assigned any staff and appoint ment in the branch 
                if( $this->projectModel->update($id,['is_active'=>1])){
                    $validSuccess = true;
                    $validMsg = 'Project Active successfully!';
                }else{
                    $validMsg = 'Oops. Please try again.';
                }
            }
        }
        return $this->response->setJson([
            'success' => $validSuccess,
            'message' => $validMsg
        ]);
    }
}