<?php
namespace App\Models;

use CodeIgniter\Model;
class BranchesModel extends Model{
    protected $table = 'branches';
    protected $allowedFields = ['id','branch_name','location','status','created_at'];
    protected $primaryKey = 'id';
}