<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AP_Controller extends Controller
{
    public $data=[
        'assets'=>[],
        'footer' => []
    ];

    protected $session;
    protected $userData;
    protected $template;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, 
                                    \CodeIgniter\HTTP\ResponseInterface $response, 
                                    \Psr\Log\LoggerInterface $logger)
    {
        // Always call the parent initController
        parent::initController($request, $response, $logger);

        // Load session
        $this->session = session();
        $this->userData = $this->session->get('user_data');
        

        // Example: Auto-auth check
        if (!$this->userData) {
            return redirect()->to('/login')->send();
            //exit;
        }
    }
    protected function addData($data= [])
    {
        $this->data = array_merge($this->data,$data);
    }

    protected function render($template, $data = [])
    {
        $this->addData($data);
        $this->data['template'] = $template;
        return view('layout/main', $this->data);
    }
}
