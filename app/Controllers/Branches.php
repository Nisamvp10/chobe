<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\BranchesModel;

class Branches extends Controller{
    protected $branchModel;

    function __construct(){
        $this->branchModel = new BranchesModel();
    }
    function index(){

        $page = "Branches";
        return view('branches/index',compact('page'));
    }
    function create($id=FALSE){
        if(!haspermission(session('user_data')['role'],'create_branch')) {
            return redirect()->to('custom_404');
        }
        if($id)
        {
            $id = decryptor($id);
            $branches = $this->branchModel->where('id', $id)->first();
            $data = $branches;
            $page = "Edit Branch";
        }else{
            $page = "Add New Branch";
            $data = '';
        }
       
        return view('branches/create',compact('page','data'));
    }
    function save(){

        $validStatus = false;
        $validStatus = '';
        if(!$this->request->isAJAX()){
            return $this->response->setJSON(['success'=> false, 'message' => 'invalid Request']);
        }
        if(!haspermission(session('user_data')['role'],'create_branch')) {
             return $this->response->setJSON(['success' => false, 'message' => 'Permission Denied']);
        }

        $rules = [
            'branch'    => 'required|min_length[3]|max_length[100]',
            'location'  => 'required|min_length[5]',
        ];

        if(!$this->validate($rules))
        {
            return $this->response->setJSON([
                'succeass' => false,
                'errors' => $this->validator->getErrors()        
            ]);
        }

        $branch   = $this->request->getVar('branch');
        $location = $this->request->getVar('location');
        $id       = decryptor($this->request->getVar('branchId'));

        $data = [
            'branch_name' => $branch,
            'location'  => $location,
        ];
        if($id){
            if($this->branchModel->update($id, $data)){
                
                $validStatus = true;
                $validMsg = 'Updated successfully!';

            }else{
                
                $validMsg = 'something went wrong Please Try again';
            }
        }else{
            if($this->branchModel->insert($data)){

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
    public function search()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid Request']);
        }
        if(!haspermission(session('user_data')['role'],'view_branch')) {
             return $this->response->setJSON(['success' => true, 'branches'=>[], 'message' => 'Permission Denied']);
        }

        $search = $this->request->getGet('search');
        $filter = $this->request->getGet('filer') ?? 0;
        
        if (!empty($filter) && $filter !== "all") {
            // Example filter values: '1' (active), '0' (inactive)
            $branches = $this->branchModel->where('status', $filter);
        }     
        $branches = $this->branchModel->orderBy('id', 'DESC')->findAll(); 
        foreach ($branches as &$branch) {
            $branch['encrypted_id'] = encryptor($branch['id']);
        }
        // Filter if search term provided
        if ($search) {
            $branches = array_filter($branches, function ($branche) use ($search) {
                $search = strtolower($search);
                return strpos(strtolower($branche['branch_name']), $search) !== false 
                    || strpos(strtolower($branche['location']), $search) !== false;
            });
        }
        return $this->response->setJSON([
            'success' => true,
            'branches' => array_values($branches)  
        ]);
    }
    function delete()
    {
        $id = decryptor($this->request->getVar('id'));
        $validSuccess = false;
        $validMsg = "oops! Item Not Valid ";
        if($id)
        {
            $branch = $this->branchModel->find($id);
            if($branch){
                //check assigned any staff and appoint ment in the branch 
                if( $this->branchModel->delete($id)){
                    $validSuccess = true;
                    $validMsg = 'Deleted successfully!';
                }else{
                    $validMsg = 'Delete failed. Please try again.';
                }
            }
        }
        return $this->response->setJson([
            'success' => $validSuccess,
            'message' => $validMsg
        ]);
    }
    public function view($branchId=false) 
    {
        $branch = $this->branchModel->where(['id'=>decryptor($branchId)])->get()->getRow();
      
        $page = 'Branch:'.($branch->branch_name ? $branch->branch_name :'') ;
        $storeId = $branch->id;
        return view('branches/view-staff',compact('page','storeId'));
    }
}