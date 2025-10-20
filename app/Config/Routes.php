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

$routes->post('/folder/create','FolderController::create');
$routes->post('/folder/delete/(:num)','TrashController::deleteFolder/$1');
$routes->post('/folder/restore/(:num)','TrashController::restoreFolder/$1');

$routes->post('/file/delete/(:num)','TrashController::deleteFile/$1');
$routes->post('/file/restore/(:num)','TrashController::restoreFile/$1');
$routes->post('/file/purge/(:num)','TrashController::permanentlyDeleteFile/$1');

$routes->post('/file/upload', 'FileController::upload');
$routes->get('/file/download/(:num)', 'FileController::download/$1');


