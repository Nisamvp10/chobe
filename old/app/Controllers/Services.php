<?php
namespace App\Controllers;

use App\Models\CategoryModel;
use CodeIgniter\Controller;
use App\Models\ServiceModel;
use App\Controllers\UploadImages;
class Services extends Controller
{
    protected $serviceModel;
    protected $categoryModel;

    function __construct(){
        $this->serviceModel = new ServiceModel();
        $this->categoryModel = new CategoryModel();
    }

    function index(){
        if(!haspermission(session('user_data')['role'],'view_services')) {
            $page = "Permission Denied" ;
        }else{
            $page = "Services";
        }
        return view('services/index',compact('page'));
    }
    public function create($id=false)
    {
        $id = decryptor($id);

        if($id){
            $data = $this->serviceModel->find($id);
            $page = "Edit Service";
        }else{
            $data ='';
            $page = "Add New Service";
        }
        $categories = $this->categoryModel->where('is_active' , 1)->findAll();
        return view('services/create',compact('page','data','categories'));
    }
    function save(){
        if(!$this->request->isAJAX())
        {
            return $this->response->setJSON(['success' => false,'medssage' => 'invalid Request']);
        }
        if(!haspermission(session('user_data')['role'],'create_services')) { 
            return $this->response->setJSON(['success'=> false ,'message'=> 'Permission Denied']);
        }
        $imageUploader = new UploadImages();
        $validSuccess = false;
        $validMsg     = '';
        $rules = [
            'service' => 'required|min_length[3]|max_length[150]',
            'duration' => 'required',
            'price'    => 'required',
            'category' => 'required',
            'description' => 'required|min_length[5]',
        ];
        if(!$this->validate($rules))
        {
           return $this->response->setJSON(['success' => $validSuccess,'errors' => $this->validator->getErrors()]);
        }
        $id = decryptor($this->request->getVar('serviceId'));
        $file = $this->request->getFile('file');
        $image =   ($file->isValid() && !$file->hasMoved() ? json_decode($imageUploader->uploadimg($file,'services'),true): ['status'=>false]);

        $data = [
            'name'      => $this->request->getVar('service'),
            'category'  => $this->request->getVar('category'),
            'duration'  => $this->request->getVar('duration'),
            'price'     => $this->request->getVar('price'),
            'description'   => $this->request->getVar('description'),
            'is_popular'    => ($this->request->getVar('popular') == true ? 1 :0)
        ];

        if($image['status'] == true){
            
            if($id)
            {
                $services = $this->serviceModel->where('id', $id)->first();
                updateImage($services['image_url']);
            }
            $data['image_url'] = base_url($image['file']);
        };

        if($id){
            if($this->serviceModel->update($id,$data)){
                $validSuccess = true;
                $validMsg = "Updated successfully!";
            }else{
                $validMsg = 'something went wrong Please Try again';
            }
        }else{
            if($this->serviceModel->insert($data)){
                $validSuccess = true;
                $validMsg = "New Service Added";
            }else{
                $validMsg = 'something went wrong Please Try again';
            }
            
        }
        return $this->response->setJSON(['success' => $validSuccess,'message' => $validMsg]);
    }

    function list()
    {
        if (!$this->request->isAJAX()) {
            
            return $this->response->setJSON(['success'=> false ,'message'=> 'Invalid request']);
        }
        if(!haspermission(session('user_data')['role'],'view_services')) { 
            return $this->response->setJSON(['success'=> false ,'message'=> 'Invalid request']);
        }
        $search = $this->request->getVar('search');
        $filter = $this->request->getVar('filter');

        $services = $this->serviceModel->servicesLsit($filter,$search);
        foreach($services as &$branch){
            $branch['encrypted_id'] = encryptor($branch['id']);
        }
        return $this->response->setJSON(
            [
                'success'=> true ,
                'services' => $services,
        ]);
    }
}
