<?php

namespace App\Models;

use CodeIgniter\Model;

class ExperienceModel extends Model
{
    protected $table = 'experiences';
    protected $primaryKey = 'id';
    protected $allowedFields = ['type', 'date_range', 'title', 'organization', 'description', 'sort_order'];
    protected $useTimestamps = false;
}
