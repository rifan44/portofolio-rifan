<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteCopyModel extends Model
{
    protected $table = 'site_copy';
    protected $primaryKey = 'id';
    protected $allowedFields = ['copy_key', 'section', 'label', 'value', 'sort_order'];
    protected $useTimestamps = false;
}
