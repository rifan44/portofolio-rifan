<?php

namespace App\Models;

use CodeIgniter\Model;

class HobbyModel extends Model
{
    protected $table = 'hobbies';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'sort_order'];
    protected $useTimestamps = false;
}
