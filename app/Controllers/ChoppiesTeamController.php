<?php
namespace App\Controllers;
use App\Controller;
use App\Models\BranchesModel;
use App\Models\RolesModel;
use App\Controllers\UploadImages;
use App\Models\UserModel;
use App\Models\ServiceModel;
use App\Models\SpecialtiesModel;
use App\Models\CategoryModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ClientsModel;
use App\Models\MasterroleModel;

class ChoppiesTeamController extends BaseController{
    protected $branchModel;
    protected $roleModel;
    protected $imageUploader;
    protected $specialityModel ;
    protected $categoryModel;
    protected $clientsModel;
    protected $masterroleModel;

    function __construct(){
        $this->branchModel = new BranchesModel();
        $this->roleModel = new RolesModel();
        $this->imageUploader = new UploadImages();
        $this->specialityModel = new SpecialtiesModel();
        $this->categoryModel = new CategoryModel();
        $this->clientsModel = new ClientsModel();
        $this->masterroleModel = new MasterroleModel();
    }
    public function index()
    {
        $routes = (haspermission('','view_staff')) ? 'admin/choppies-team/index' : '404error';
        $page = (haspermission('','view_staff')) ? 'Choppies Team' : lang('custom.accessDenied');

        return view($routes);
    }
    
    function list() {

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Request'
            ]);
        }
        if (!haspermission(session('user_data')['role'],'view_staff')) {
             return $this->response->setJSON([
                'success' => false,
                'message' => 'Permission Denied'
            ]);
        }
        $userModel = new UserModel();

        $search = $this->request->getPost('search');
        $filter = $this->request->getPost('filter');
        $branch = $this->request->getPost('branch');

        $staff = $userModel->getUsers($search,$filter,$branch);//12 only choppies team

        foreach ($staff as &$staffKey) {
            $staffKey['encrypted_id'] = encryptor($staffKey['id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'staff' => $staff
        ]);
    }
    
    function create($id = false){
        
          $userModel = new UserModel();
          $serviceModel = new ServiceModel();
        if ($id){

            $page = "Edit Choppies Team";
            $id = decryptor($id);
            $data = $userModel->where('id',$id)->first();
            $selectedSpecialties = $this->specialityModel->getSpecialty($id);

        }else{
            $selectedSpecialties = [];
            $data = '';
            $page = "Add Choppies Team";
        }
        $branches = $this->clientsModel->where('status',1)->findAll();
        $positiondata = $this->masterroleModel->where('status','active')->findAll();
        $roles = $this->roleModel->findAll();
        $services = $this->categoryModel->getCategory();
      
        return view('admin/choppies-team/create',compact('page','branches','roles','data','services','selectedSpecialties','positiondata'));
    }

    function save(){

        $userModel = new UserModel();
        $validSuccess = false;
        $validMsg = '';

        $id = decryptor($this->request->getPost('staffId'));

        if (!$this->request->isAJAX())
        {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Invalid Request"
            ]);
        }
        if(!haspermission(session('user_data')['role'],'create_staff') ) {
            return $this->response->setJSON(['success' =>false,'message' => 'Permission Denied']);
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'position' => 'required',
            'hire_date' => 'required|valid_date[Y-m-d]', // assuming YYYY-MM-DD
            'status' => 'required',
            //'branch' => 'required|numeric', // or string based on your table
            //'role' => 'required',
        ];
        if (empty($id)) {
           // $rules['password'] = 'required|min_length[6]|max_length[50]';
            $rules['email'] = 'required|valid_email|max_length[100]|is_unique[users.email]';
        }
        if(!empty($this->request->getPost('password'))) {
          //  $rules['password'] = 'required|min_length[6]|max_length[50]';
        }
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        $file = $this->request->getFile('file');
        $image =   ($file->isValid() && !$file->hasMoved() ? json_decode($this->imageUploader->uploadimg($file,'user'),true): ['status'=>false]);

        $data = [
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'position_id' => $this->request->getPost('position'),
            'hire_date' => $this->request->getPost('hire_date'),
            'booking_status' => $this->request->getPost('status'),
            'store_id' => $this->request->getPost('branch'),
            'status' => 2,
        ];

        $selectedServices = $this->request->getPost('services'); 
        $specialtyData = [];
        if (!empty($selectedServices)) {

            foreach ($selectedServices as $service) {
                $specialtyData[] = [
                    'speciality' => $service
                ];
            }
        }
         
        if($image['status'] == true)
        {
            if($id)
            {
                $user = $userModel->where('id', $id)->first();
                updateImage($user['profileimg']);
            }
            $data['profileimg'] = base_url($image['file']);
        }
        if ($id) {
                if (!empty($selectedServices)) {
                    $this->specialityModel->where('staff_id', $id)->delete();
                    foreach ($specialtyData as &$row) {
                        $row['staff_id'] = $id;
                    }
                }
                if(!empty($this->request->getPost('password'))) {
                    $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                }
            if ($userModel->update($id,$data)) {
                if(!empty($specialtyData)) {
                    $this->specialityModel->insertBatch($specialtyData);
                }
                $validSuccess = true;
                $validMsg = "Updated Successfully";
            }else {
                $validMsg = "Somthing went wrong Please try agin later";
            }
        }else {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            $data['email'] =  $this->request->getPost('email');
            if ($lastId = $userModel->insert($data)) {
                
                if (!empty($selectedServices)) {
                    foreach ($specialtyData as &$row) {
                        $row['staff_id'] = $lastId;
                    }
                    $this->specialityModel->insertBatch($specialtyData);
                }
                $validSuccess = true;
                $validMsg = "New User Added Successfully";
            }else {
                $validMsg = "Somthing went wrong Please try agin later";
            }
        }
        
        return $this->response->setJSON([
            'success' => $validSuccess,
            'message' => $validMsg,
        ]);
    }
}