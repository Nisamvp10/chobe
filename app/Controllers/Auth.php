<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\LoginHistoryModel;
use App\Models\PermissiongeneratorModel;
use App\Models\RolePermissionModel;
use App\Models\RolesModel;

Class Auth extends Controller{

    public function login(){

        if(session()->get('isLoggedIn')){
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin(){

        $session    = session();
        $userModel  =   new UserModel();
        $roleModel  =   new RolesModel();
        $loginHistory = new LoginHistoryModel();
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if(!$this->validate($rules)){

            return redirect()->back()->withInput()->with('errors',$this->validator->getErrors());
        }

        $userName = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $userData = $userModel->where('email',$userName)->first();
      

        if($userData)
        {
            $loginHistorydata = [
                'user_id'    =>  $userData['id'],
                'login_time'    => date('Y-m-d H:i:s'),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
            ];
            $loginHistorydata['status'] = 2;
            if($userData && password_verify($password,$userData['password']))
            {
                if ($userData['status'] === 'approved') {
                    
                    $type = $roleModel->getRole($userData['role']);
                    
                    $loginData = [
                        'id'    =>  $userData['id'],
                        'username'  => $userData['name'],
                        'role'      => $userData['role'],
                        'type'      => $type->role_name,
                        'isLoggedIn' => true
                    ];
                    $session->set('user_data',$loginData);
                    $this->getUserPermissions($userData['role']);
                    $loginHistorydata['status'] = 1;
                    $loginHistory->insert($loginHistorydata);

                    return redirect()->to('dashboard');
                    
                } elseif ($userData['status'] === 'pending') {

                    $loginHistory->insert($loginHistorydata);
                    return redirect()->back()->withInput()->with('errors', ['Your account is pending approval. Please contact the admin.']);
                } else {

                    $loginHistory->insert($loginHistorydata);
                    return redirect()->back()->withInput()->with('errors', ['Your account has been blocked. Please contact the admin.']);
                }
            }else{
                $loginHistory->insert($loginHistorydata);
                return redirect()->back()->withInput()->with('errors',['Password Incorrect']);
            }
        }else{
            return redirect()->back()->withInput()->with('errors',['User not found']);
        }
    }
    private function getUserPermissions($roleId){
        $permisionsAccss = new PermissiongeneratorModel();
        $permissions = $permisionsAccss->getPermissionByRole($roleId);
        session()->set('permissions',array_column($permissions, 'permission_name'));

    }
    function logout() {
        session()->destroy();
        redirect()->to('login');
    }
}