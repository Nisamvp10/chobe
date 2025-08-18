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


class Staff extends BaseController{
    protected $branchModel;
    protected $roleModel;
    protected $imageUploader;
    protected $specialityModel ;
    protected $categoryModel;
    
    function __construct(){
        $this->branchModel = new BranchesModel();
        $this->roleModel = new RolesModel();
        $this->imageUploader = new UploadImages();
        $this->specialityModel = new SpecialtiesModel();
        $this->categoryModel = new CategoryModel();
    }

    function index()
    {
        
        $page = (!haspermission(session('user_data')['role'],'view_staff') ?  lang('Custom.accessDenied') : "Staff Management" );
        $active = 'TEST';
        $branches = $this->branchModel->where('status',1)->findAll();
        if (!haspermission(session('user_data')['role'],'view_staff')) {
             $branches = [];
        }
        return view('staff/index',compact('page','active','branches'));
    }

    function bulkindex(){
        $data = '';
        $page = "Bulk Team Data Upload";
      
        return view('staff/bulk-create',compact('page','data'));
    }
    

    function create($id = false){
        
          $userModel = new UserModel();
          $serviceModel = new ServiceModel();
        if ($id){

            $page = "Edit Team";
            $id = decryptor($id);
            $data = $userModel->where('id',$id)->first();
            $selectedSpecialties = $this->specialityModel->getSpecialty($id);

        }else{
            $selectedSpecialties = [];
            $data = '';
            $page = "Add Team";
        }
        
        $branches = $this->branchModel->where('status',1)->findAll();
        $roles = $this->roleModel->findAll();
        $services = $this->categoryModel->getCategory();
      
        return view('staff/create',compact('page','branches','roles','data','services','selectedSpecialties'));
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
            'position' => 'required|min_length[2]|max_length[50]',
            'hire_date' => 'required|valid_date[Y-m-d]', // assuming YYYY-MM-DD
            'status' => 'required',
            //'branch' => 'required|numeric', // or string based on your table
            'role' => 'required',
        ];
        if (empty($id)) {
            $rules['password'] = 'required|min_length[6]|max_length[50]';
            $rules['email'] = 'required|valid_email|max_length[100]|is_unique[users.email]';
        }
        if(!empty($this->request->getPost('password'))) {
            $rules['password'] = 'required|min_length[6]|max_length[50]';
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
            'position' => $this->request->getPost('position'),
            'hire_date' => $this->request->getPost('hire_date'),
            'booking_status' => $this->request->getPost('status'),
            'store_id' => $this->request->getPost('branch'),
            'role' => $this->request->getPost('role'),
            'status' => 2,
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
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
            if ($userModel->update($id,$data)) {
                $this->specialityModel->insertBatch($specialtyData);
                $validSuccess = true;
                $validMsg = "Updated Successfully";
            }else {
                $validMsg = "Somthing went wrong Please try agin later";
            }
        }else {
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

    function uploadExcel() {
        $status = false;
        $message = '';
        if(!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $status ,'msg' => '"Invalid Request"']);
        }
        if(!haspermission('','create_staff') ) {
            return $this->response->setJSON(['success' =>false,'message' => lang('Custom.accessDenied')]);
        }

         $file = $this->request->getFile('staff_excel');
        
        if (!$file->isValid() || $file->getExtension() === '') {
            return $this->response->setJSON(['success' =>false,'message' => 'Please upload a valid Excel file']);
        }

        $ext = $file->getClientExtension();
        if (!in_array($ext, ['xls', 'xlsx'])) {
            return $this->response->setJSON(['success' =>false,'message' => 'Only .xls or .xlsx files allowed']);
        }

        $filePath = $file->getTempName();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $staffModel = new UserModel();;
        $count = 0;
        $insertedMessages = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header row
            
            $staffData = [
                'name'           => $row[0] ?? '',
                'email'          => $row[1] ?? '',
                'phone'          => $row[2] ?? '',
                'position'       => $row[3] ?? '',
                'password' => password_hash($row[4] ?? '', PASSWORD_DEFAULT),
                'booking_status' => 1,
                'role'           => 5, //inventory staff
                'status'         => 2,
            ];

            if (!empty($staffData['name']) && !empty($staffData['email'])) {
                if($staffModel->insert($staffData)) {
                    $insertedMessages[] = " Staff {$staffData['name']} added successfully.";
                }else {
                    $insertedMessages[] = "Failed to add staff {$staffData['name']}.";
                }
                $count++;
                
            }
        }

        $insertedMessages[] = " All {$count} staff members uploaded successfully.";

        return $this->response->setJSON([
            'success'  => true,
            'inserted' => $insertedMessages
        ]);
        
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

        $staff = $userModel->getUsers($search,$filter,$branch);

        foreach ($staff as &$staffKey) {
            $staffKey['encrypted_id'] = encryptor($staffKey['id']);
        }

        return $this->response->setJSON([
            'success' => true,
            'staff' => $staff
        ]);
    }

    function delete() {

        if (!$this->request->isAjax()) {
            return $this->response->setJSON([ 'success' => false, 'message' => "Invalid Request"]);
        }
        $userModel = new UserModel();
        $validSuccess = false;
        $validMsg = "oops! Item Not Valid ";
        
        $id = decryptor($this->request->getPost('id'));

        if ($id) {
            $staffFind = $userModel->where('id',$id)->find();
            if ($staffFind) {

                if( $userModel->delete($id)){
                    $validSuccess = true;
                    $validMsg = 'Deleted successfully!';
                }else{
                    $validMsg = 'Delete failed. Please try again.';
                }
            }
        }

        return $this->response->setJSON([
            'success' => $validSuccess,
            'message' => $validMsg
        ]);
    }
    function branchStaff() {

        if (!$this->request->isAjax()) {
            return $this->response->setJSON(['success' => false , 'message' => lang('Custom.invalidRequest')]);
        }

        $branchId = $this->request->getPost('branch');
        $userModel = new UserModel();
        $branches = $userModel->getstaffRole($branchId);

        return $this->response->setJSON(['branches' => $branches]);
    }

}