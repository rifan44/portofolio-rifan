<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class AdminHobbyFeatureTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = 'App';

    public function testAdminShowsStructuredContentSectionsInsteadOfBulkCopyEditor(): void
    {
        $this->db->table('profiles')->insert([
            'id' => 1,
            'full_name' => 'Test Person',
            'role' => 'Developer',
            'headline' => 'Building useful things',
            'summary' => 'A short introduction.',
            'about' => 'A short profile.',
            'location' => 'Indonesia',
            'focus' => 'Web development',
            'availability' => null,
            'email' => 'test@example.org',
        ]);
        $this->withSession(['is_admin' => true]);

        $response = $this->get('admin/');
        $body = $response->response()->getBody();

        $this->assertSame(200, $response->response()->getStatusCode());
        $this->assertStringContainsString('Informasi diri', $body);
        $this->assertStringContainsString('Hobi & minat', $body);
        $this->assertStringNotContainsString('Cari tulisan', $body);
        $this->assertStringNotContainsString('Simpan tulisan halaman', $body);
    }

    public function testAuthenticatedAdminCanAddAHobby(): void
    {
        $tokenName = csrf_token();
        $token = csrf_hash();
        $this->withSession([
            'is_admin' => true,
            $tokenName => $token,
        ]);

        $saveResponse = $this->call('POST', 'admin/hobbies/save', [
            $tokenName => $token,
            'name' => 'Bersepeda',
        ]);
        $hobby = $this->db->table('hobbies')
            ->where('name', 'Bersepeda')
            ->get()
            ->getRowArray();

        $this->assertContains($saveResponse->response()->getStatusCode(), [302, 303]);
        $this->assertNotNull($hobby);
    }

    public function testAuthenticatedAdminCanRemoveAHobby(): void
    {
        $hobbyId = $this->db->table('hobbies')->insert([
            'name' => 'Bersepeda',
            'sort_order' => 1,
        ], true);
        $tokenName = csrf_token();
        $token = csrf_hash();
        $this->withSession([
            'is_admin' => true,
            $tokenName => $token,
        ]);

        $response = $this->call('POST', 'admin/hobbies/delete/' . $hobbyId, [
            $tokenName => $token,
        ]);
        $remaining = $this->db->table('hobbies')->where('id', $hobbyId)->countAllResults();

        $this->assertContains($response->response()->getStatusCode(), [302, 303]);
        $this->assertSame(0, $remaining);
    }

    public function testHobbyIsRenderedInAboutSection(): void
    {
        $this->db->table('profiles')->insert([
            'id' => 1,
            'full_name' => 'Test Person',
            'role' => 'Developer',
            'headline' => 'Building useful things',
            'summary' => 'A short introduction.',
            'about' => 'A short profile.',
            'location' => 'Indonesia',
            'focus' => 'Web development',
            'availability' => null,
            'email' => 'test@example.org',
        ]);
        $emptyResponse = $this->get('/');
        $emptyBody = $emptyResponse->response()->getBody();
        $this->assertStringNotContainsString('<h3>Hobi &amp; minat</h3>', $emptyBody);

        $this->db->table('hobbies')->insert([
            'name' => 'Bersepeda',
            'sort_order' => 1,
        ]);

        $response = $this->get('/');

        $this->assertSame(200, $response->response()->getStatusCode());
        $body = $response->response()->getBody();
        $this->assertStringContainsString('<h3>Hobi &amp; minat</h3>', $body);
        $this->assertStringContainsString('Bersepeda', $body);
    }
}
