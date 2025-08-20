<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientContactsModal extends Model
{
    protected $table = 'client_contacts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        	'id','client_id','authorized_personnel','email','phone','designation','created_at'
    ];
}
