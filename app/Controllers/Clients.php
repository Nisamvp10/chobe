<?php
namespace App\Controllers;
use App\Models\UserModel;
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
    protected $userModel;

    function __construct() {
        $this->categoryModel = new CategoryModel();
        $this->branchModel = new BranchesModel();
        $this->serviceModel = new ServiceModel();
        $this->clientsModel = new ClientsModel();
        $this->userModel = new UserModel();
    }

    function index() {

        $page = (!haspermission(session('user_data')['role'],'view_clients') ? lang('Custom.accessDenied') : 'Clients' );
        return view('admin/clients/index',compact('page'));
    }

    function create ($id=false) {
        $page = (!haspermission('','create_client') ? lang('Custom.accessDenied') : 'Add New Client' );
        $clientGroup = [] ;
        if($id) {
            $page = "Edit Client";
            $id = decryptor($id);
            $data = $this->clientsModel->getClinentById($id);
            if(!empty($data)) {
                foreach ($data as $client) {
                    if(!isset($clientGroup[$client['id']])) {
                        $clientGroup[$client['id']] =[
                            'id' => $client['id'],
                            'name'  => $client['name'],
                            'address' => $client['note'],
                            'clientInfo' => [],
                        ];
                    }

                    if(!empty($client['authorized_personnel'])) {
                          $clientGroup[$client['id']]['clientInfo'][] = [
                             'authorized_personnel' => $client['authorized_personnel'],
                            'email' => $client['email'] ?? '',
                            'infoId' => $client['infoId'] ?? '',
                            'phone' => $client['phone'] ?? '',
                            'designation' => $client['designation'] ?? ''
                          ];
                    }
                }
            }
        }else
        {    
            $page = "Add New Client";
            $data = [];
        }
        // echo '<pre>';
        $clientGroup = array_values($clientGroup);
        // var_dump($clientGroup);exit();
        $services =$this->serviceModel->where('is_active' , 1)->findAll();
        $branches = $this->branchModel->where('status',1)->findAll();
        $selectedSpecialties = [];
        return view('admin/clients/create',compact('page','services','selectedSpecialties','branches','clientGroup'));
        
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
       //echo  $this->clientsModel->getLastQuery();
        $contactsGroupped = [];
        foreach ($clients as $client) {
            $client['encrypted_id'] = encryptor($client['id']);
            if(!isset($contactsGroupped[$client['id']])) {

                $contactsGroupped[$client['id']] = [
                    'encrypted_id' => encryptor($client['id']),
                    'name' => $client['name'],
                    'address' => $client['note'] ?? '',
                    'clientsInfo' => [],
                ];

                if(!empty($client['authorized_personnel'])) {
                      $contactsGroupped[$client['id']]['clientsInfo'][] = [
                        'authorized_personnel' => $client['authorized_personnel'],
                        'email' => $client['email'] ?? '',
                        'phone' => $client['phone'] ?? '',
                        'designation' => $client['designation'] ?? ''
                      ];
                }
            }else{
                 if(!isset($contactsGroupped[$client['infoId']])) {

                    if(!empty($client['authorized_personnel'])) {
                        $contactsGroupped[$client['id']]['clientsInfo'][] = [
                            'authorized_personnel' => $client['authorized_personnel'],
                            'email' => $client['email'] ?? '',
                            'phone' => $client['phone'] ?? '',
                            'designation' => $client['designation'] ?? ''
                        ];
                    }
                }
            }
        }
       
        $clients = array_values($contactsGroupped);
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

                $infoIds   = $this->request->getPost('infoId'); 
                $names     = $this->request->getPost('authorized_personnel');
                $emails    = $this->request->getPost('email');
                $phones    = $this->request->getPost('phone');
                $designations = $this->request->getPost('designation');

                $submittedIds = []; 

                foreach ($names as $index => $person) {
                    if (empty(trim($person))) {
                        continue;
                    }
                    $infoId = $infoIds[$index] ?? null;

                    $contactInfo = [
                        'authorized_personnel' => $person,
                        'email'       => $emails[$index] ?? null,
                        'phone'       => $phones[$index] ?? null,
                        'designation' => $designations[$index] ?? null,
                        'client_id'   => $id,
                    ];

                    if (!empty($infoId)) {
                       //update
                        $clientContactsModal->update($infoId, $contactInfo);
                        $submittedIds[] = $infoId;
                    } else {
                       //insert
                        $newId = $clientContactsModal->insert($contactInfo);
                        $submittedIds[] = $newId;
                    }
                }

                //delete
                if (!empty($submittedIds)) {
                    $clientContactsModal->where('client_id', $id)
                                        ->whereNotIn('id', $submittedIds)
                                        ->delete();
                }

                
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

    public function delete($id)
    {
        $clientModel =  $this->clientsModel;

        if(!hasPermission('','client_delete')) {
            return $this->response->setJSON(['status' => false,'msg'=>lang('Custom.accessDenied')]);
        }
        $id = decryptor($id);
        if ($clientModel->find($id)) {
            //$taskModel->delete($id);
            $clientModel->update($id,['status'=>2]);
            return $this->response->setJSON(['status' => true,'msg' => 'Task deleted successfully!']);
        }

        return $this->response->setJSON(['status' => false, 'msg' => 'Task not found']);
    }

    public function clientBystaff($id) {
        if($id) {
           $rmUsers = $this->userModel
                ->select('users.id, users.name, up.level')
                ->join('user_position as up', 'up.id = users.position_id', 'left')
                ->where('users.store_id', $id)
                ->orderBy('up.level', 'ASC')
                ->get()
                ->getResult();

            return $this->response->setJSON([
                'status' => true,
                'rms'    => $rmUsers
            ]);

        }
        return $this->response->setJSON(['status' => false,'msg' => 'No Data Found']);
       

    }
}
