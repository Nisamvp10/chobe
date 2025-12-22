<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Settings extends Controller{
    protected $userData;
    function __construct(){
        $this->userData = session()->get('user_data');
    }

    function index(){
        if($this->userData['role'] ==1){
            return view('settings/index');
        }else{
            redirect()->to('dashboard');
        }
    }

    function save(){
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $rules = [
            'company_name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[10]',
            'address' => 'required|min_length[5]',
            //'tax_number' => 'required|min_length[5]',
            //'notification_email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $postData = $this->request->getPost();
        foreach($postData as $key => $value){
           saveSettings($key,$value);
        }

        try {
           // $this->settingsModel->save($data);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update settings'
            ]);
        }
    
    }
}