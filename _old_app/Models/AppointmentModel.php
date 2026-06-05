<?php
namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model {

    protected $table = "appointments";
    protected $allowedFields = ['client_id','staff_id','price','duration','booking_status','time','time','note'];
    protected $primaryKey ='id';

   function getAppointments() {

        $userRole = session('user_data')['role'];

        $builder = $this->db->table('appointments a') 
            ->select('a.booking_status, a.booking_date, a.time, a.note,c.name ,s.name as staff_name,branch_name')
            ->select('GROUP_CONCAT(sr.name ORDER BY sr.name SEPARATOR ", ") as specialties')
            ->join('clients as c', 'a.client_id = c.id')
            ->join('users as s', 'a.staff_id = s.id')
            ->join('booked_specialties as bs','a.id= bs.booking_id')
            ->join('services as sr','bs.specialties_id= sr.id')
            ->join('branches as b','s.store_id= b.id')
            ->groupBy('a.id');

        if ($userRole != 1) {
            $storeId = getStore();
            if ($userRole == 2) {
                $builder->where('b.id', $storeId);
            }elseif ($userRole == 5) {
                $builder->where('s.id', session('user_data')['id']);
            }
            
        }
        $query = $builder->get()->getResultArray();
        return $query;
    }

}

