<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectTechnologyModel extends Model
{
    protected $table = 'project_technologies';
    protected $primaryKey = 'id';
    protected $allowedFields = ['project_id', 'technology', 'sort_order'];
    protected $useTimestamps = false;
}
