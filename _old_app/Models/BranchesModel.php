<?php
namespace App\Models;

use CodeIgniter\Model;
class BranchesModel extends Model{
    protected $table = 'branches';
    protected $allowedFields = ['id','branch_name','location','oldstore_name','store_mailid','polaris_code','oracle_code','rm_store','rm_mail','status','created_at'];
    protected $primaryKey = 'id';
}