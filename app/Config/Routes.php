<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/login','AuthController::loginForm');
$routes->post('/login','AuthController::login');
$routes->post('/register','AuthController::register');
$routes->get('/logout','AuthController::logout');

$routes->get('/', function() {
  return redirect()->to('/dashboard');
});
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/recycle-bin', 'DashboardController::recycleBin');


$routes->post('/upload','UploadController::uploadFile');


$routes->group('folder', ['namespace' => 'App\Controllers'], function($routes) {
  $routes->get('view/(:num)', 'FolderController::view/$1');
  $routes->post('create', 'FolderController::create');
  $routes->post('delete/(:num)', 'FolderController::delete/$1');
  $routes->post('restore/(:num)', 'FolderController::restore/$1');

  // 👇 Add this line
  $routes->post('purge/(:num)', 'FolderController::purge/$1');
});

$routes->post('/file/delete/(:num)','TrashController::deleteFile/$1');
$routes->post('/file/restore/(:num)','TrashController::restoreFile/$1');
$routes->post('/file/purge/(:num)','TrashController::permanentlyDeleteFile/$1');

$routes->post('/file/upload', 'FileController::upload');
$routes->get('/file/download/(:num)', 'FileController::download/$1');


