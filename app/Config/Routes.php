<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('media/(:segment)', 'PortfolioMedia::show/$1');
$routes->get('admin/login', 'Admin::login');
$routes->post('admin/login', 'Admin::login');
$routes->post('admin/logout', 'Admin::logout', ['filter' => 'adminauth']);

$routes->group('admin', ['filter' => 'adminauth'], static function ($routes): void {
    $routes->get('/', 'Admin::index');
    $routes->post('profile', 'Admin::saveProfile');
    $routes->post('projects/save', 'Admin::saveProject');
    $routes->post('projects/delete/(:num)', 'Admin::deleteProject/$1');
    $routes->post('projects/reorder/(:num)', 'Admin::reorderProject/$1');
    $routes->post('skills/save', 'Admin::saveSkill');
    $routes->post('skills/delete/(:num)', 'Admin::deleteSkill/$1');
    $routes->post('hobbies/save', 'Admin::saveHobby');
    $routes->post('hobbies/delete/(:num)', 'Admin::deleteHobby/$1');
    $routes->post('experiences/save', 'Admin::saveExperience');
    $routes->post('experiences/delete/(:num)', 'Admin::deleteExperience/$1');
    $routes->post('experiences/reorder/(:num)', 'Admin::reorderExperience/$1');
    $routes->post('social-links/save', 'Admin::saveSocialLink');
    $routes->post('social-links/delete/(:num)', 'Admin::deleteSocialLink/$1');
});
