<?php
namespace App\Models;

use CodeIgniter\Model;


class MastertaskModel extends Model{
    protected $table = 'mastertasks';
    protected $allowedFields = ['id','title','description','status','created_at','created_by','updated_at','updated_by'];
    protected $primaryKey = 'id';
}