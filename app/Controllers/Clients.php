<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\CategoryModel;
use App\Models\BranchesModel;
use App\Models\ServiceModel;
use App\Models\ClientsModel;
use App\Controllers\UploadImages;
use App\Models\ClientContactsModal;

class Clients extends controller {
    protected $categoryModel;
    protected $branchModel;
    protected $serviceModel;
    protected $clientsModel;

    function __construct() {
        $this->categoryModel = new CategoryModel();
        $this->branchModel = new BranchesModel();
        $this->serviceModel = new ServiceModel();
        $this->clientsModel = new ClientsModel();
    }

    function index() {

        $page = (!haspermission(session('user_data')['role'],'view_clients') ? lang('Custom.accessDenied') : 'Clients' );
        return view('admin/clients/index',compact('page'));
    }

    function create ($id=false) {
        $page = (!haspermission('','create_client') ? lang('Custom.accessDenied') : 'Add New Client' );
        if($id) {
            $page = "Edit Client";
            $id = decryptor($id);
            $data = $this->clientsModel->where('id',$id)->first();
        }else
        {    
            $page = "Add New Client";
            $data = [];
        }
        $services =$this->serviceModel->where('is_active' , 1)->findAll();
        $branches = $this->branchModel->where('status',1)->findAll();
        $selectedSpecialties = [];
        return view('admin/clients/create',compact('page','services','selectedSpecialties','branches','data'));
        
    }

    function list() {
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false,'message' => lang('Custom.invalidRequest')]);
        }
        if(!haspermission(session('user_data')['role'],'view_clients')) {
            return $this->response->setJSON(['success' => false,'message' => lang('Custom.accessDenied')]);
        }

        $search = $this->request->getGet('search');
        $filter = $this->request->getGet('filter');

        $clients = $this->clientsModel->getClients($search,$filter);
        
       
        // $clients = $builder->findAll();
        foreach ($clients as &$client) {
            $client['encrypted_id'] = encryptor($client['id']);
            $client['spend'] = number_format($client['spend'],2);
            $minutes = (int)$client['spend'];
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            $client['spendtime'] = ($hours > 0 ? $hours . ' hr ' : '') . ($remainingMinutes > 0 ? $remainingMinutes . ' min' : '');

        }
        return $this->response->setJSON(['success' => true,'clients' => $clients]);
    }

    function save() {

        if(!$this->request->isAJAX()) {
            return $this->response->setJSON(['return' => false,'message' => 'Invalid Request']) ;
        }

        if (!haspermission(session('user_data')['role'],'create_client')) {
            return $this->response->setJSON(['success'=> false,'message' => 'Permission Denied']);
        }
        $clientContactsModal = new ClientContactsModal();
        $id = decryptor($this->request->getPost('clientId'));
        $rules = [
            'name' => 'required|min_length[3]',
           // 'phone' => 'required|regex_match[/^\+?[0-9]{10,15}$/]',
            //'joindate' => 'required'
        ];
        $messages=[];
        // if(empty($id)) {

        //     $rules['phone'] = 'required|regex_match[/^\+?[0-9]{10,15}$/]|is_unique[clients.phone]';
        //     $messages = [
        //         'phone' => [
        //             'is_unique'    => 'This phone number is already registered.',
        //             'regex_match'  => 'Please enter a valid phone number.',
        //             'required'     => 'Phone number is required.',
        //         ],
        //     ];
        // }

        $imageUploader = new UploadImages();

        if(!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

            $data = [
                'name' => $this->request->getPost('name'),
                //'email' => $this->request->getPost('email'),
                //'phone' => $this->request->getPost('phone'),
                //'join_date' => $this->request->getPost('joindate'),
                'note' => $this->request->getPost('notes'),
            ];

        // $file = $this->request->getFile('file');
        // $image =   ($file->isValid() && !$file->hasMoved() ? json_decode($imageUploader->uploadimg($file,'clients'),true): ['status'=>false]);

        //  if($image['status'] == true) {
        //     if($id) {
        //         $user = $this->clientsModel->where('id', $id)->first();
        //         updateImage($user['profile']);
        //     }
        //     $data['profile'] = base_url($image['file']);
        // }
        if ($id) {
            if($this->clientsModel->update($id,$data)) {
                
                $validStatus = true;
                $validMsg = lang('Custom.updateMsg') ;
            }else{
                $validStatus = true;
                $validMsg = lang('Custom.tryAgain');
            }
        }else {
            if ($this->clientsModel->insert($data)) {

                $authorized_personnel = $this->request->getPost('authorized_personnel');
                $emails = $this->request->getPost('email');
                $phones = $this->request->getPost('phone');
                $designations = $this->request->getPost('designation');
                $contacts = [];
                if(!empty($authorized_personnel)) {
                    
                    foreach ($authorized_personnel as $i => $person) {
                        $contacts[] = [
                            'client_id' => $this->clientsModel->insertID(),
                            'authorized_personnel' => $person,
                            'email' => $emails[$i],
                            'phone' => $phones[$i] ?? null,
                            'designation' => $designations[$i] ?? null,
                        ];
                    }
                }
                foreach ($contacts as $contact) {
                    $clientContactsModal->insert($contact);
                }

                $validStatus = true;
                $validMsg = 'New Client Added' ;

            }else{
                
                $validStatus = false;
                $validMsg = 'Oops!something went wrong Please try again' ;
            }
        }
        return $this->response->setJSON([ 'success' => $validStatus, 'message' => $validMsg ]);
    }

    function suggestPhone() {

        $phoneInput = $this->request->getPost('phone');
        $builder =  $this->clientsModel->select('id,name,phone');

        if(!empty($phoneInput)) {
            $builder->like('phone', $phoneInput, 'after');
        }
        $builder->limit(5);
        $query = $builder->get();
        $results = $query->getResult();

        $clients = [];
        foreach ($results as $row) {
            $clients[] = [
                'id' => $row->id,
                'name' => $row->name,
                'phone' => $row->phone
            ];
        }

        return $this->response->setJSON($clients);
    }
}
