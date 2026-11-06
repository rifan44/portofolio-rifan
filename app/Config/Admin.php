<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Admin extends BaseConfig
{
    public string $email;
    public string $passwordHash;

    public function __construct()
    {
        parent::__construct();
        $this->email = trim((string) env('ADMIN_EMAIL', ''));
        $this->passwordHash = trim((string) env('ADMIN_PASSWORD_HASH', ''));
    }
}
