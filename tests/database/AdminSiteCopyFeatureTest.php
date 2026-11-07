<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class AdminSiteCopyFeatureTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = 'App';

    public function testAuthenticatedAdminCanSavePublicCopy(): void
    {
        $tokenName = csrf_token();
        $token = csrf_hash();
        $this->withSession([
            'is_admin' => true,
            $tokenName => $token,
        ]);
        $response = $this->call('POST', 'admin/site-copy', [
            $tokenName => $token,
            'copy' => ['code_title' => 'developer-v2.ts'],
        ]);

        $saved = $this->db->table('site_copy')
            ->where('copy_key', 'code_title')
            ->get()
            ->getRowArray();

        $this->assertContains($response->response()->getStatusCode(), [302, 303]);
        $this->assertSame('developer-v2.ts', $saved['value'] ?? null);
    }
}
