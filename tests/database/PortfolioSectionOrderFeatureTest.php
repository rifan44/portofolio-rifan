<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/** @internal */
final class PortfolioSectionOrderFeatureTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = 'App';

    public function testHomepageKeepsHeroFirstAndUsesDefaultSectionOrderWithoutHobbies(): void
    {
        $this->insertProfile();
        $this->db->table('hobbies')->insert(['name' => 'Bermain Game', 'sort_order' => 1]);

        $response = $this->get('/');
        $body = $response->response()->getBody();
        $positions = array_map(
            static fn (string $id): int|false => strpos($body, 'id="' . $id . '"'),
            ['home', 'about', 'work', 'skills', 'experience', 'contact'],
        );

        $this->assertSame(200, $response->response()->getStatusCode());
        $this->assertNotContains(false, $positions);
        for ($index = 1; $index < count($positions); $index++) {
            $this->assertLessThan($positions[$index], $positions[$index - 1]);
        }
        $this->assertStringNotContainsString('Hobi &amp; minat', $body);
        $this->assertStringNotContainsString('Bermain Game', $body);
        $this->assertStringContainsString('class="visual-index">01 <span>-</span> 06</div>', $body);
    }

    public function testHomepageCanRenderConfiguredSectionsAfterThePinnedHero(): void
    {
        $this->insertProfile();
        $this->db->table('portfolio_sections')->update(['sort_order' => 6]);
        $this->db->table('portfolio_sections')->where('section_key', 'contact')->update(['sort_order' => 1]);
        $this->db->table('portfolio_sections')->where('section_key', 'about')->update(['sort_order' => 2]);
        $this->db->table('portfolio_sections')->where('section_key', 'projects')->update(['sort_order' => 3]);
        $this->db->table('portfolio_sections')->where('section_key', 'skills')->update(['sort_order' => 4]);
        $this->db->table('portfolio_sections')->where('section_key', 'experience')->update(['sort_order' => 5]);

        $response = $this->get('/');
        $body = $response->response()->getBody();
        $heroPosition = strpos($body, 'id="home"');
        $contactPosition = strpos($body, 'id="contact"');
        $aboutPosition = strpos($body, 'id="about"');
        $projectPosition = strpos($body, 'id="work"');
        $contactNavPosition = strpos($body, 'href="#contact"');
        $aboutNavPosition = strpos($body, 'href="#about"');

        $this->assertSame(200, $response->response()->getStatusCode());
        $this->assertNotFalse($heroPosition);
        $this->assertNotFalse($contactPosition);
        $this->assertNotFalse($aboutPosition);
        $this->assertNotFalse($projectPosition);
        $this->assertNotFalse($contactNavPosition);
        $this->assertNotFalse($aboutNavPosition);
        $this->assertLessThan($contactPosition, $heroPosition);
        $this->assertLessThan($aboutPosition, $contactPosition);
        $this->assertLessThan($projectPosition, $aboutPosition);
        $this->assertLessThan($aboutNavPosition, $contactNavPosition);
        $this->assertStringContainsString('02 / KONTAK', $body);
        $this->assertStringContainsString('03 / TENTANG SAYA', $body);
    }

    public function testAdminCanSeeAndChangeTheSectionOrder(): void
    {
        $this->insertProfile();
        $this->db->table('hobbies')->insert(['name' => 'Bermain Game', 'sort_order' => 1]);
        $this->withSession(['is_admin' => true]);

        $adminResponse = $this->get('admin/');
        $adminBody = $adminResponse->response()->getBody();
        $this->assertSame(200, $adminResponse->response()->getStatusCode());
        $this->assertStringContainsString('Urutan section', $adminBody);
        $this->assertStringContainsString('Headline &amp; perkenalan', $adminBody);
        $this->assertStringContainsString('admin/sections/reorder/', $adminBody);
        $this->assertStringNotContainsString('Hobi &amp; minat', $adminBody);
        $this->assertStringNotContainsString('Bermain Game', $adminBody);

        $project = $this->db->table('portfolio_sections')->where('section_key', 'projects')->get()->getRowArray();
        $tokenName = csrf_token();
        $token = csrf_hash();
        $this->withSession([
            'is_admin' => true,
            $tokenName => $token,
        ]);

        $response = $this->call('POST', 'admin/sections/reorder/' . $project['id'], [
            $tokenName => $token,
            'direction' => 'up',
        ]);
        $ordered = $this->db->table('portfolio_sections')->orderBy('sort_order', 'ASC')->get()->getResultArray();

        $this->assertContains($response->response()->getStatusCode(), [302, 303]);
        $this->assertSame('projects', $ordered[0]['section_key']);
        $this->assertSame('about', $ordered[1]['section_key']);
        $this->assertSame(1, $this->db->table('hobbies')->where('name', 'Bermain Game')->countAllResults());
    }

    private function insertProfile(): void
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
    }
}
