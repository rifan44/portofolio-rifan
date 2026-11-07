<?php

namespace App\Models;

use CodeIgniter\Model;

class PortfolioSectionModel extends Model
{
    protected $table = 'portfolio_sections';
    protected $primaryKey = 'id';
    protected $allowedFields = ['section_key', 'label', 'sort_order'];
    protected $useTimestamps = false;
}
