<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskimagesModel extends Model {
    protected $table = 'task_images';
    protected $allowedFields = ['id' ,'task_id', 'image_url', 'file_ext'];
    protected $primaryKey = 'id';
}