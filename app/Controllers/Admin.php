<?php

namespace App\Controllers;

use App\Models\ExperienceModel;
use App\Models\ProfileModel;
use App\Models\ProjectModel;
use App\Models\ProjectTechnologyModel;
use App\Models\SkillModel;
use App\Models\SocialLinkModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Model;

class Admin extends BaseController
{
    public function login(): string|RedirectResponse
    {
        $session = session();
        if ($session->get('is_admin')) {
            return redirect()->to(site_url('admin'));
        }

        $config = config('Admin');
        $configured = $config->email !== '' && $config->passwordHash !== '';
        $error = null;

        if ($this->request->getMethod() === 'POST') {
            $email = strtolower(trim((string) $this->request->getPost('email')));
            $password = (string) $this->request->getPost('password');
            $throttler = service('throttler');
            $throttleKey = 'admin-login-' . hash('sha256', $this->request->getIPAddress());

            if (! $configured) {
                $error = 'Login admin belum dikonfigurasi. Atur ADMIN_EMAIL dan ADMIN_PASSWORD_HASH di .env.';
            } else {
                $emailMatches = hash_equals(strtolower($config->email), $email);
                $passwordMatches = password_verify($password, $config->passwordHash);

                if ($emailMatches && $passwordMatches) {
                    $throttler->remove($throttleKey);
                    $session->regenerate(true);
                    $session->set(['is_admin' => true]);

                    return redirect()->to(site_url('admin'));
                }

                $error = $throttler->check($throttleKey, 5, 600)
                    ? 'Email atau password tidak sesuai.'
                    : 'Terlalu banyak percobaan. Tunggu sebelum mencoba kembali.';
            }
        }

        return view('admin/login', [
            'error' => $error,
            'configured' => $configured,
            'profile' => (new ProfileModel())->first(),
        ]);
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(site_url('admin/login'));
    }

    public function index(): string
    {
        return view('admin/dashboard', [
            'profile' => (new ProfileModel())->first(),
            'projects' => (new ProjectModel())->withTechnologies(),
            'skills' => (new SkillModel())->orderBy('sort_order', 'ASC')->findAll(),
            'experiences' => (new ExperienceModel())->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
            'socialLinks' => (new SocialLinkModel())->orderBy('sort_order', 'ASC')->findAll(),
            'errors' => session()->getFlashdata('errors') ?? [],
            'message' => session()->getFlashdata('message'),
        ]);
    }

    public function saveProfile(): RedirectResponse
    {
        $data = $this->request->getPost([
            'full_name', 'role', 'headline', 'summary', 'about', 'location', 'focus', 'availability', 'email',
        ]);
        $rules = [
            'full_name' => 'required|max_length[120]',
            'role' => 'required|max_length[160]',
            'headline' => 'required|max_length[220]',
            'summary' => 'required|max_length[1000]',
            'about' => 'required|max_length[2000]',
            'location' => 'permit_empty|max_length[120]',
            'focus' => 'permit_empty|max_length[120]',
            'availability' => 'permit_empty|max_length[160]',
            'email' => 'required|valid_email|max_length[190]',
        ];
        if (! $this->validateData($data, $rules)) {
            return $this->invalidForm('/admin#profile');
        }

        $model = new ProfileModel();
        $profile = $model->find(1);
        if (! $profile) {
            return $this->withErrors('/admin#profile', ['profile' => 'Data profil belum tersedia.']);
        }

        $newImages = [];
        foreach (['profile_photo' => 'profile', 'logo_photo' => 'logo'] as $field => $prefix) {
            $upload = $this->storeImageUpload($field, $prefix);
            if ($upload['error'] !== null) {
                foreach ($newImages as $filename) {
                    $this->removeStoredImage($filename);
                }
                return $this->withErrors('/admin#profile', [$field => $upload['error']]);
            }
            if ($upload['filename'] !== null) {
                $newImages[$field] = $upload['filename'];
            }
        }

        $values = array_map(static fn ($value): string => trim((string) $value), $data);
        foreach (['profile_photo', 'logo_photo'] as $field) {
            if ($this->request->getPost('remove_' . $field) === '1') {
                $values[$field] = null;
            }
            if (isset($newImages[$field])) {
                $values[$field] = $newImages[$field];
            }
        }

        $db = db_connect();
        $db->transBegin();
        $model->update(1, $values);
        $db->transComplete();
        if (! $db->transStatus()) {
            foreach ($newImages as $filename) {
                $this->removeStoredImage($filename);
            }
            return $this->withErrors('/admin#profile', ['database' => 'Profil tidak berhasil disimpan. Periksa koneksi database.']);
        }

        foreach (['profile_photo', 'logo_photo'] as $field) {
            if (($profile[$field] ?? null) !== ($values[$field] ?? null)) {
                $this->removeStoredImage($profile[$field] ?? null);
            }
        }
        return $this->success('/admin#profile', 'Profil berhasil diperbarui.');
    }

    public function saveProject(): RedirectResponse
    {
        $data = $this->request->getPost([
            'id', 'number', 'category', 'category_label', 'year', 'status', 'title', 'description', 'details', 'demo_url', 'source_url', 'stack',
        ]);
        $rules = [
            'id' => 'permit_empty|is_natural_no_zero',
            'number' => 'permit_empty|max_length[8]',
            'category' => 'required|in_list[web,game,infra]',
            'category_label' => 'required|max_length[80]',
            'year' => 'required|max_length[20]',
            'status' => 'required|max_length[60]',
            'title' => 'required|max_length[160]',
            'description' => 'required|max_length[2000]',
            'details' => 'permit_empty|max_length[5000]',
            'stack' => 'permit_empty|max_length[720]',
        ];
        if (! $this->validateData($data, $rules)) {
            return $this->invalidForm('/admin#projects');
        }

        $demoInput = trim((string) ($data['demo_url'] ?? ''));
        $sourceInput = trim((string) ($data['source_url'] ?? ''));
        $demoUrl = $this->externalUrl($demoInput);
        $sourceUrl = $this->externalUrl($sourceInput);
        if (($demoInput !== '' && $demoUrl === null) || ($sourceInput !== '' && $sourceUrl === null)) {
            return $this->withErrors('/admin#projects', ['demo_url' => 'Tautan proyek harus menggunakan URL http:// atau https:// yang valid.']);
        }

        $projectModel = new ProjectModel();
        $technologyModel = new ProjectTechnologyModel();
        $projectId = (int) ($data['id'] ?? 0);
        $stack = array_slice(array_values(array_unique(array_filter(array_map('trim', explode(',', (string) ($data['stack'] ?? '')))))), 0, 12);
        $existingProject = $projectId > 0 ? $projectModel->find($projectId) : null;
        $maximumProjectOrder = (int) ($projectModel->selectMax('sort_order')->first()['sort_order'] ?? 0);
        $sortOrder = $existingProject ? (int) $existingProject['sort_order'] : $maximumProjectOrder + 1;
        if ($projectId > 0 && ! $existingProject) {
            return $this->withErrors('/admin#projects', ['project' => 'Proyek yang akan diubah tidak ditemukan.']);
        }
        $upload = $this->storeImageUpload('cover_image', 'project');
        if ($upload['error'] !== null) {
            return $this->withErrors('/admin#projects', ['cover_image' => $upload['error']]);
        }
        $coverImage = $upload['filename']
            ?? ($this->request->getPost('remove_cover_image') === '1' ? null : ($existingProject['cover_image'] ?? null));
        $values = [
            'number' => trim((string) ($data['number'] ?? '')) ?: str_pad((string) ($sortOrder), 2, '0', STR_PAD_LEFT),
            'category' => $data['category'],
            'category_label' => trim((string) $data['category_label']),
            'year' => trim((string) $data['year']),
            'status' => trim((string) $data['status']),
            'title' => trim((string) $data['title']),
            'description' => trim((string) $data['description']),
            'details' => trim((string) ($data['details'] ?? '')),
            'demo_url' => $demoUrl,
            'source_url' => $sourceUrl,
            'cover_image' => $coverImage,
            'sort_order' => $sortOrder,
        ];

        $db = db_connect();
        $db->transStart();
        if ($projectId > 0) {
            $projectModel->update($projectId, $values);
        } else {
            $projectId = (int) $projectModel->insert($values, true);
        }
        $technologyModel->where('project_id', $projectId)->delete();
        foreach ($stack as $order => $technology) {
            $technologyModel->insert(['project_id' => $projectId, 'technology' => mb_substr($technology, 0, 60), 'sort_order' => $order + 1]);
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            $this->removeStoredImage($upload['filename']);
            return $this->withErrors('/admin#projects', ['database' => 'Proyek tidak berhasil disimpan. Periksa koneksi database.']);
        }

        if (($existingProject['cover_image'] ?? null) !== $coverImage) {
            $this->removeStoredImage($existingProject['cover_image'] ?? null);
        }

        return $this->success('/admin#projects', 'Proyek berhasil disimpan.');
    }

    public function deleteProject(int $id): RedirectResponse
    {
        $model = new ProjectModel();
        $project = $model->find($id);
        if ($model->delete($id)) {
            $this->removeStoredImage($project['cover_image'] ?? null);
        }
        return $this->success('/admin#projects', 'Proyek berhasil dihapus.');
    }

    public function reorderProject(int $id): RedirectResponse
    {
        return $this->reorder('/admin#projects', 'Proyek', new ProjectModel(), $id);
    }

    public function saveSkill(): RedirectResponse
    {
        $data = $this->request->getPost(['name']);
        if (! $this->validateData($data, ['name' => 'required|max_length[60]'])) {
            return $this->invalidForm('/admin#skills');
        }

        $name = trim((string) $data['name']);
        $model = new SkillModel();
        if ($model->where('name', $name)->first()) {
            return $this->withErrors('/admin#skills', ['name' => 'Skill tersebut sudah ada.']);
        }
        $maximumOrder = (int) ($model->selectMax('sort_order')->first()['sort_order'] ?? 0);
        $model->insert(['name' => $name, 'sort_order' => $maximumOrder + 1]);

        return $this->success('/admin#skills', 'Skill berhasil ditambahkan.');
    }

    public function deleteSkill(int $id): RedirectResponse
    {
        (new SkillModel())->delete($id);
        return $this->success('/admin#skills', 'Skill berhasil dihapus.');
    }

    public function saveExperience(): RedirectResponse
    {
        $data = $this->request->getPost(['id', 'type', 'date_range', 'title', 'organization', 'description']);
        $rules = [
            'id' => 'permit_empty|is_natural_no_zero',
            'type' => 'required|in_list[education,organization,work,achievement,project]',
            'date_range' => 'required|max_length[60]',
            'title' => 'required|max_length[160]',
            'organization' => 'required|max_length[160]',
            'description' => 'required|max_length[2000]',
        ];
        if (! $this->validateData($data, $rules)) {
            return $this->invalidForm('/admin#experience');
        }

        $model = new ExperienceModel();
        $id = (int) ($data['id'] ?? 0);
        $existingExperience = $id > 0 ? $model->find($id) : null;
        if ($id > 0 && ! $existingExperience) {
            return $this->withErrors('/admin#experience', ['experience' => 'Riwayat yang akan diubah tidak ditemukan.']);
        }
        $maximumOrder = (int) ($model->selectMax('sort_order')->first()['sort_order'] ?? 0);
        $values = [
            'type' => $data['type'],
            'date_range' => trim((string) $data['date_range']),
            'title' => trim((string) $data['title']),
            'organization' => trim((string) $data['organization']),
            'description' => trim((string) $data['description']),
            'sort_order' => $existingExperience ? (int) $existingExperience['sort_order'] : $maximumOrder + 1,
        ];
        if ($id > 0) {
            $model->update($id, $values);
        } else {
            $model->insert($values);
        }

        return $this->success('/admin#experience', 'Riwayat berhasil disimpan.');
    }

    public function deleteExperience(int $id): RedirectResponse
    {
        (new ExperienceModel())->delete($id);
        return $this->success('/admin#experience', 'Riwayat berhasil dihapus.');
    }

    public function reorderExperience(int $id): RedirectResponse
    {
        return $this->reorder('/admin#experience', 'Riwayat', new ExperienceModel(), $id);
    }

    public function saveSocialLink(): RedirectResponse
    {
        $data = $this->request->getPost(['platform', 'url']);
        if (! $this->validateData($data, ['platform' => 'required|max_length[40]', 'url' => 'required|max_length[500]'])) {
            return $this->invalidForm('/admin#contact');
        }
        $url = $this->externalUrl((string) $data['url']);
        if ($url === null) {
            return $this->withErrors('/admin#contact', ['url' => 'Tautan harus menggunakan URL http:// atau https:// yang valid.']);
        }

        $model = new SocialLinkModel();
        $model->insert([
            'platform' => trim((string) $data['platform']),
            'url' => $url,
            'sort_order' => ((int) ($model->selectMax('sort_order')->first()['sort_order'] ?? 0)) + 1,
        ]);

        return $this->success('/admin#contact', 'Tautan berhasil ditambahkan.');
    }

    public function deleteSocialLink(int $id): RedirectResponse
    {
        (new SocialLinkModel())->delete($id);
        return $this->success('/admin#contact', 'Tautan berhasil dihapus.');
    }

    private function externalUrl(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            return null;
        }
        $parts = parse_url($value);
        if (! is_array($parts) || ! isset($parts['host']) || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return null;
        }
        return $value;
    }

    /** @return array{filename: ?string, error: ?string} */
    private function storeImageUpload(string $field, string $prefix): array
    {
        $file = $this->request->getFile($field);
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return ['filename' => null, 'error' => null];
        }
        if (! $file->isValid() || $file->hasMoved()) {
            return ['filename' => null, 'error' => 'File gambar gagal diunggah. Coba pilih ulang file-nya.'];
        }
        if ($file->getSize() > 2 * 1024 * 1024) {
            return ['filename' => null, 'error' => 'Ukuran gambar maksimal 2 MB.'];
        }

        $imageInfo = @getimagesize($file->getTempName());
        $mime = is_array($imageInfo) ? ($imageInfo['mime'] ?? '') : '';
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $width = (int) ($imageInfo[0] ?? 0);
        $height = (int) ($imageInfo[1] ?? 0);
        if (! isset($extensions[$mime]) || $file->getMimeType() !== $mime) {
            return ['filename' => null, 'error' => 'Gunakan gambar JPG, PNG, atau WebP yang valid.'];
        }
        if ($width < 1 || $height < 1 || $width > 6000 || $height > 6000 || $width * $height > 20000000) {
            return ['filename' => null, 'error' => 'Dimensi gambar terlalu besar. Gunakan gambar maksimal 6000 × 6000 piksel.'];
        }

        $directory = WRITEPATH . 'uploads/portfolio';
        if (! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            return ['filename' => null, 'error' => 'Folder penyimpanan gambar tidak dapat dibuat.'];
        }

        try {
            $filename = $prefix . '-' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
            $file->move($directory, $filename);
        } catch (\Throwable) {
            return ['filename' => null, 'error' => 'Gambar tidak berhasil disimpan. Periksa izin folder writable.'];
        }

        return ['filename' => $filename, 'error' => null];
    }

    private function removeStoredImage(?string $filename): void
    {
        if ($filename === null || ! preg_match('/^(?:profile|logo|project)-[a-f0-9]{32}\\.(?:jpg|png|webp)$/D', $filename)) {
            return;
        }

        $path = WRITEPATH . 'uploads/portfolio/' . $filename;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function reorder(string $redirect, string $label, Model $model, int $id): RedirectResponse
    {
        $direction = (string) $this->request->getPost('direction');
        if (! in_array($direction, ['up', 'down'], true)) {
            return $this->withErrors($redirect, ['order' => 'Arah pengurutan tidak valid.']);
        }

        $db = db_connect();
        $db->transBegin();
        $rows = $model->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
        $current = null;
        foreach ($rows as $index => $row) {
            if ((int) $row['id'] === $id) {
                $current = $index;
                break;
            }
        }
        if ($current === null) {
            $db->transRollback();
            return $this->withErrors($redirect, ['order' => $label . ' tidak ditemukan.']);
        }

        $target = $current + ($direction === 'up' ? -1 : 1);
        if (! isset($rows[$target])) {
            $db->transRollback();
            return $this->success($redirect, $direction === 'up' ? 'Item ini sudah berada di urutan pertama.' : 'Item ini sudah berada di urutan terakhir.');
        }

        [$rows[$current], $rows[$target]] = [$rows[$target], $rows[$current]];
        foreach ($rows as $index => $row) {
            if ($model->update($row['id'], ['sort_order' => $index + 1]) === false) {
                $db->transRollback();
                return $this->withErrors($redirect, ['order' => 'Urutan ' . strtolower($label) . ' tidak berhasil disimpan.']);
            }
        }
        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->withErrors($redirect, ['order' => 'Urutan ' . strtolower($label) . ' tidak berhasil disimpan.']);
        }

        return $this->success($redirect, $label . ' berhasil dipindahkan.');
    }

    private function invalidForm(string $redirect): RedirectResponse
    {
        return $this->withErrors($redirect, $this->validator->getErrors());
    }

    /** @param array<string, string> $errors */
    private function withErrors(string $redirect, array $errors): RedirectResponse
    {
        session()->setFlashdata('errors', $errors);
        return redirect()->to(site_url(ltrim($redirect, '/')));
    }

    private function success(string $redirect, string $message): RedirectResponse
    {
        session()->setFlashdata('message', $message);
        return redirect()->to(site_url(ltrim($redirect, '/')));
    }
}
