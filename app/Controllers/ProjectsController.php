<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\ProjectsModel;

class ProjectsController extends Controller{

    protected $projectModel;
    
    function __construct() {

        $this->projectModel = new ProjectsModel();

    }
    function index() {
        $page = "Projects";
        return view('admin/projects/index',compact('page'));
    }

    function create($id =false)
    {
        $page = "Edit Project" ; 
        $id = decryptor($id);
        $data = $this->projectModel->where(['id'=> $id,'is_active' =>1])->first();
        return view('admin/projects/edit',compact('page','data'));
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
        ];

        if(!$this->validate($rules)){
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        $project = $this->request->getVar('project');
        $id = decryptor($this->request->getVar('projectId'));

        $data = [
            'project' => $project,
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

        $builder = $this->projectModel->select('id,project,is_active')->orderBy('id DESC');

        if($filter !=='all'){
            $builder->where('is_active',$filter);
        }

        if(!empty($search))
        {
            $builder->groupStart()
                ->like('project',$search)
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