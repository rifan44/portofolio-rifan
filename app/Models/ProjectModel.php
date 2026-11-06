<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $allowedFields = ['number', 'category', 'category_label', 'year', 'status', 'title', 'description', 'details', 'demo_url', 'source_url', 'cover_image', 'sort_order'];
    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function withTechnologies(): array
    {
        $projects = $this->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
        $technologyModel = new ProjectTechnologyModel();

        foreach ($projects as &$project) {
            $project['stack'] = $technologyModel
                ->where('project_id', $project['id'])
                ->orderBy('sort_order', 'ASC')
                ->findColumn('technology') ?? [];
        }
        unset($project);

        return $projects;
    }
}
