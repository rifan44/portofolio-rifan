<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/** @internal */
final class SiteCopyMigrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = 'App';

    public function testMigrationCreatesEditablePortfolioCopyDefaults(): void
    {
        $rows = $this->db->table('site_copy')->get()->getResultArray();
        $copy = array_column($rows, 'value', 'copy_key');

        $this->assertGreaterThanOrEqual(80, count($rows));
        $this->assertSame('developer.ts', $copy['code_title'] ?? null);
        $this->assertStringContainsString('{nama}', $copy['code_content'] ?? '');
        $this->assertSame('sambil praktik', $copy['hero_note_learning_subtitle'] ?? null);
    }
}
