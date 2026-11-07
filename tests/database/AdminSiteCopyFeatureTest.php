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

    public function testBulkSiteCopyEditorIsNoLongerExposedInAdmin(): void
    {
        $this->expectException(CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->call('POST', 'admin/site-copy', []);
    }
}
