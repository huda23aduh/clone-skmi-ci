<?php namespace App\Controllers;

use App\Models\FolderModel;
use App\Models\FileModel;
use CodeIgniter\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        helper('format');
        
        $session = session();
        $user = $session->get('user');

        if (!$user) return redirect()->to('/login');

        $folderModel = new FolderModel();
        $fileModel   = new FileModel();

        $folders = $folderModel->where('user_id', $user['id'])
                            ->where('is_deleted', 0)
                            ->where('parent_id IS NULL OR parent_id = 0')
                            ->findAll();

        $files = $fileModel->where('user_id', $user['id'])
                        ->where('is_deleted', 0)
                        ->where('folder_id IS NULL OR folder_id = 0')
                        ->findAll();

        return view('dashboard/index', [
            'title' => 'MY Drive',
            'user' => $user,          // <<< pass user to view
            'folders' => $folders,
            'files' => $files
        ]);
    }

    public function starred()
    {
        $user = session()->get('user');
        if (!$user) return redirect()->to('/login');

        $starredModel = new \App\Models\StarredModel();
        $starredItems = $starredModel->getUserStarredItems($user['id']);

        return view('starred/view', [
            'title' => 'Starred Items',
            'user' => $user,
            'starredItems' => $starredItems
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
