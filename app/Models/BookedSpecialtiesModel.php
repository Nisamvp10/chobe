<?php
namespace App\Models;
use CodeIgniter\Model;

class BookedSpecialtiesModel extends Model {
    protected $table = "booked_specialties";
    protected $allowedFields = ['booking_id','specialties_id'];
    protected $primaryKey = 'id';
    
}