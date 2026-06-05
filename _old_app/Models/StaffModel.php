<?php

namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table = 'staff';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'position',
        'position_id',
        'phone',
        'email',
        'hire_date',
        'specialties',
        'status'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Convert specialties array to JSON before saving
    protected $beforeInsert = ['prepareSpecialties'];
    protected $beforeUpdate = ['prepareSpecialties'];

    // Convert JSON to array after fetching
    protected $afterFind = ['formatSpecialties'];

    protected function prepareSpecialties(array $data)
    {
        if (isset($data['data']['specialties']) && is_array($data['data']['specialties'])) {
            $data['data']['specialties'] = json_encode($data['data']['specialties']);
        }
        return $data;
    }

    protected function formatSpecialties(array $data)
    {
        if (isset($data['result'])) {
            if (is_array($data['result'])) {
                foreach ($data['result'] as &$row) {
                    if (isset($row['specialties'])) {
                        $row['specialties'] = json_decode($row['specialties'], true) ?? [];
                    }
                }
            } else if (is_object($data['result'])) {
                if (isset($data['result']->specialties)) {
                    $data['result']->specialties = json_decode($data['result']->specialties, true) ?? [];
                }
            }
        }
        return $data;
    }
}