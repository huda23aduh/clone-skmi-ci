<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/login','AuthController::loginForm');
$routes->post('/login','AuthController::login');
$routes->post('/register','AuthController::register');
$routes->get('/logout','AuthController::logout');

$routes->group('', ['filter' => 'auth'], function($routes) {
  $routes->get('/', function() {
    return redirect()->to('/dashboard');
  });
  $routes->get('/dashboard', 'DashboardController::index');
  $routes->get('/recycle-bin', 'DashboardController::recycleBin');
  
  
  $routes->post('/upload','UploadController::uploadFile');
  
  
  $routes->group('folder', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('view/(:num)', 'FolderController::view/$1');
    $routes->post('create', 'FolderController::create');
    $routes->post('delete/(:num)', 'TrashController::deleteFolder/$1');
    $routes->post('restore/(:num)', 'TrashController::restoreFolder/$1');
  
    
    $routes->post('purge/(:num)', 'FolderController::purge/$1');
  
    $routes->post('rename/(:num)', 'FolderController::rename/$1');
    $routes->post('rename', 'FolderController::rename');
    $routes->get('info/(:num)', 'FolderController::getFolderInfo/$1');
  });
  
  $routes->group('file', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->post('delete/(:num)','TrashController::deleteFile/$1');
    $routes->post('restore/(:num)','TrashController::restoreFile/$1');
    $routes->post('purge/(:num)','TrashController::permanentlyDeleteFile/$1');
    
    $routes->post('upload', 'FileController::upload');
    $routes->get('download/(:num)', 'FileController::download/$1');
    $routes->post('compress', 'FileController::compress');
    $routes->get('extract/(:num)', 'FileController::extract/$1');
  
    $routes->post('rename/(:num)', 'FileController::rename/$1');
    $routes->post('rename', 'FileController::rename');
    $routes->get('info/(:num)', 'FileController::getFileInfo/$1');
  });
  
  // Bulk operations routes
  $routes->post('bulk/delete', 'BulkController::delete');
  $routes->post('bulk/restore', 'BulkController::restore');
  $routes->post('bulk/purge', 'BulkController::purge');
  $routes->post('bulk/delete-info', 'BulkController::getBulkDeleteInfo');
  
  $routes->get('preview/(:num)', 'PreviewController::preview/$1');
  $routes->get('preview/info/(:num)', 'PreviewController::getFileInfo/$1');
  $routes->get('writable/uploads/(.+)', 'FileController::serveFile/$1');
  
  $routes->group('starred', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'DashboardController::starred');
      $routes->post('toggle', 'StarredController::toggle');
      $routes->get('check', 'StarredController::checkStarred');
  });
  
  
  $routes->group('activity-log', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'ActivityLogController::index');
    $routes->get('data', 'ActivityLogController::getActivityData');
    $routes->post('clear', 'ActivityLogController::clearLogs');
    $routes->get('export', 'ActivityLogController::export');
  });

  $routes->group('summary', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'SummaryPageController::index');
    $routes->get('data', 'SummaryPageController::getSummaryData');
  });

  $routes->get('debug/upload-limits', 'DebugController::uploadLimits');
  
  $routes->group('control-center', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'ControlCenterController::index');
    $routes->get('members', 'ControlCenterController::getMembers');
    $routes->post('members', 'ControlCenterController::createMember');
    $routes->post('members/(:num)/toggle-status', 'ControlCenterController::toggleMemberStatus/$1');
    $routes->delete('members/(:num)', 'ControlCenterController::deleteMember/$1');
  });
  
  $routes->group('profile', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'ProfileController::index');
    $routes->post('update', 'ProfileController::updateProfile');
    $routes->post('upload-image', 'ProfileController::uploadProfileImage');
    $routes->post('update-language', 'ProfileController::updateLanguage');
    $routes->get('activity-chart-data', 'ProfileController::getActivityChartData');

    $routes->post('email/add', 'EmailController::addEmail');
    $routes->post('email/set-primary/(:num)', 'EmailController::setPrimary/$1');
    $routes->post('email/remove/(:num)', 'EmailController::removeEmail/$1');
    $routes->get('emails', 'EmailController::getUserEmails');
    $routes->get('verify-email/(:any)', 'EmailController::verifyEmail/$1');
  });
});

