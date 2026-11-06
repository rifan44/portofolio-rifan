<?php

namespace App\Models;

use CodeIgniter\Model;

class SocialLinkModel extends Model
{
    protected $table = 'social_links';
    protected $primaryKey = 'id';
    protected $allowedFields = ['platform', 'url', 'sort_order'];
    protected $useTimestamps = false;
}
