<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table = 'profiles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['full_name', 'role', 'headline', 'summary', 'about', 'location', 'focus', 'availability', 'email', 'updated_at'];
    protected $useTimestamps = false;
}
