<?php

namespace App\Controllers;

use App\Models\ExperienceModel;
use App\Models\ProfileModel;
use App\Models\ProjectModel;
use App\Models\PortfolioSectionModel;
use App\Models\SkillModel;
use App\Models\SocialLinkModel;
use App\Models\SiteCopyModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('portfolio', [
            'profile' => (new ProfileModel())->first(),
            'projects' => (new ProjectModel())->withTechnologies(),
            'skills' => (new SkillModel())->orderBy('sort_order', 'ASC')->findAll(),
            'experiences' => (new ExperienceModel())->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
            'sectionOrder' => (new PortfolioSectionModel())->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
            'socialLinks' => (new SocialLinkModel())->orderBy('sort_order', 'ASC')->findAll(),
            'siteCopy' => (new SiteCopyModel())->orderBy('sort_order', 'ASC')->findAll(),
        ]);
    }
}
