<?php namespace App\Controllers;

use App\Models\FolderModel;
use App\Models\FileModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $session = session();
        $user = $session->get('user');

        if (!$user) return redirect()->to('/login');

        $folderModel = new FolderModel();
        $fileModel   = new FileModel();

        $folders = $folderModel->where('user_id', $user['id'])
                            ->where('is_deleted', 0)
                            ->findAll();

        $files = $fileModel->where('user_id', $user['id'])
                        ->where('is_deleted', 0)
                        ->findAll();

        return view('dashboard/index', [
            'title' => 'MY Drive',
            'user' => $user,          // <<< pass user to view
            'folders' => $folders,
            'files' => $files
        ]);
    }


    public function recycleBin()
    {
        $user = session('user');
        if (!$user) return redirect()->to('/login');

        $folderModel = new FolderModel();
        $fileModel = new FileModel();

        $data = [
            'title' => 'Recycle Bin',
            'folders' => $folderModel->where('user_id', $user['id'])->where('is_deleted', 1)->findAll(),
            'files' => $fileModel->where('user_id', $user['id'])->where('is_deleted', 1)->findAll(),
        ];

        return view('dashboard/recycle_bin', $data);
    }
}
