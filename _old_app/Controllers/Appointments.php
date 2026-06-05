<?php
namespace App\Controllers;
use App\Models\BookedSpecialtiesModel;
use CodeIgniter\Controller;
use App\Models\ServiceModel;
use App\Models\BranchesModel;
use App\Models\UserModel;
use App\Models\AppointmentModel;
use App\Models\ClientsModel;

class Appointments extends Controller {
    protected $serviceModel;
    protected $branchModel;
    protected $userModel;
    protected $appointmentModel;
    protected $clientsModel;
    function __construct() {

        $this->serviceModel = new ServiceModel();
        $this->branchModel = new BranchesModel();
        $this->userModel =  new UserModel();
        $this->appointmentModel = new AppointmentModel();
        $this->clientsModel =  new ClientsModel();
    }

    function index () {

        $page = (haspermission('','view_appointments') ? "Task List" : lang('Custom.accessDenied'));
        return view('admin/appointments/index',compact('page'));
    }

    function booking () {
        $page = (haspermission('','create_appointment') ? "Create Appointment" : lang('Custom.accessDenied'));
        
        $usersStaff  = $this->userModel->select('id,name')->where('booking_status',1)->findAll();
        $branches = $this->branchModel->where('status','active')->findAll();
        $services = $this->serviceModel->where('is_active' , 1)->findAll();
        return view('admin/appointments/booking',compact('page','services','branches','usersStaff'));

    }

    function save() {

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.invalidRequest')
            ]);
        }

        if(!hasPermission('','create_appointment')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Custom.accessDenied')
            ]);
        }
        $bookedSpecialtiesModel = new BookedSpecialtiesModel();

        $rules = [
            'phone' => 'required|min_length[5]|max_length[100]',
            'booking_date' => 'required',
            'booking_time' => 'required',
            'staff' => 'required',
            'booking_status' => 'required',
            'specialties' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }
        $phone = $this->request->getPost('phone');
        $client = $this->clientsModel->select('id')->where('phone',$phone)->get()->getRow();

        $validSuccess = false;
        $validMsg = '';

        if (!empty($client->id)) {

            $price = 0;
            $duration = 0;
            $bookedSpecialties = [];
            $selectedServices = $this->request->getPost('specialties');
            foreach ($selectedServices as $service) {
                $serviceResult = $this->serviceModel->select('price,duration')->where('id',$service)->get()->getRow();
                $price += $serviceResult->price;
                $duration += $serviceResult->duration;
                $bookedSpecialties[] =['specialties_id' => $service];

            }

            $data = [
                'client_id' => $client->id,
                'staff_id'  => $this->request->getPost('staff'),
                'duration'  => $duration,
                'price'     => $price,
                'booking_status' => $this->request->getPost('booking_status'),
                'booking_date' => date('Y-m-d'),strtotime($this->request->getPost('booking_date')),
                'time'      => $this->request->getPost('booking_time'),
                'note'      => $this->request->getPost('notes'),
            ];
            
            if ($lastid = $this->appointmentModel->insert($data)) {

                foreach ($bookedSpecialties as &$sp) {
                    $sp['booking_id'] = $lastid;
                }
                
                $bookedSpecialtiesModel->insertBatch($bookedSpecialties);

                $validSuccess = true;
                $validMsg = "Booking has been confirmed";
            }else{
                 $validMsg = lang('Custom.tryAgain');
            }
            
        }else {
            $validMsg = "Please Choose Valid Client";
        }

        return $this->response->setJSON(['success' => $validSuccess,'message' => $validMsg]);
    }

public function load()
{
    if(!$this->request->isAJAX()) {
        //return $this->response->setJSON(['success' => false,'message'=> lang('Custom.invalidrequest')]);
    }
    $start = $this->request->getGet('start'); // example: 2025-04-27T00:00:00+05:30
    $end   = $this->request->getGet('end');   // example: 2025-06-08T00:00:00+05:30

    // Optional: convert to standard date format
    $startDate = date('Y-m-d', strtotime($start));
    $endDate   = date('Y-m-d', strtotime($end));
    $appointments = $this->appointmentModel->getAppointments();
    //echo $this->appointmentModel->getLastQuery(); exit();

    $events = [];

    foreach ($appointments as $appt) {
        $events[] = [
            'title' => $appt['specialties'],//. " - " . $appt['client_name'],
            'client' => $appt['name'],
            'start' => $appt['booking_date'] . 'T' . $appt['time'],
            'time' => date("g:i A",strtotime($appt['time'])) ,
            'end'   => $appt['booking_date'] . 'T' ,//. $appt['end_time'], // optional
            'branch' => $appt['branch_name'],
            'extendedProps' => [
                'staff' => $appt['staff_name'],
                'status' => $appt['booking_status'],//($appt['booking_status'] == 1 ? 'Active' : ($appt['booking_status'] == 2 ? 'Completed' : 'Pending')),
            ]
        ];
    }

    return $this->response->setJSON($events);
}

} 
