<?php namespace App\Controllers;

use App\Models\FolderModel;
use App\Models\FileModel;
use CodeIgniter\Controller;

class TrashController extends Controller
{
    protected $folderModel;
    protected $fileModel;

    public function __construct()
    {
        $this->folderModel = new FolderModel();
        $this->fileModel = new FileModel();
    }

    // mark folder and all its descendant folders and files as deleted
    public function deleteFolder($id)
    {
        $user = session('user'); if (!$user) return redirect()->to('/login');

        $folder = $this->folderModel->where('id', $id)->where('user_id', $user['id'])->first();
        if (!$folder) return redirect()->back()->with('error','Folder not found');

        $now = date('Y-m-d H:i:s');

        // gather descendant folder ids using recursion (PHP)
        $toMark = $this->collectFolderAndDescendants($id, $user['id']);

        // mark all folders
        $this->folderModel->whereIn('id', $toMark)->set(['is_deleted' => 1, 'deleted_at' => $now])->update();

        // mark files in these folders
        $this->fileModel->whereIn('folder_id', $toMark)->set(['is_deleted' => 1, 'deleted_at' => $now])->update();

        // also mark files directly in the folder being deleted? included above.
        return redirect()->back()->with('success','Folder moved to recycle bin');
    }

    protected function collectFolderAndDescendants($id, $userId)
    {
        $result = [];
        $stack = [$id];
        while (!empty($stack)) {
            $cur = array_pop($stack);
            $result[] = $cur;
            $children = $this->folderModel->where('parent_id', $cur)->where('user_id', $userId)->findAll();
            foreach ($children as $c) $stack[] = $c['id'];
        }
        return $result;
    }

    // delete file (soft)
    public function deleteFile($fileId)
    {
        $user = session('user'); if (!$user) return redirect()->to('/login');
        $file = $this->fileModel->where('id',$fileId)->where('user_id',$user['id'])->first();
        if (!$file) return redirect()->back()->with('error','File not found');

        $this->fileModel->update($fileId, ['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success','File moved to recycle bin');
    }

    public function restoreFolder($id)
    {
        $user = session('user'); if (!$user) return redirect()->to('/login');

        $toRestore = $this->collectFolderAndDescendants($id, $user['id']);

        $this->folderModel->whereIn('id', $toRestore)->set(['is_deleted' => 0, 'deleted_at' => null])->update();
        $this->fileModel->whereIn('folder_id', $toRestore)->set(['is_deleted' => 0, 'deleted_at' => null])->update();

        return redirect()->back()->with('success','Folder restored');
    }

    public function restoreFile($id)
    {
        $user = session('user'); if (!$user) return redirect()->to('/login');
        $file = $this->fileModel->where('id',$id)->where('user_id',$user['id'])->first();
        if (!$file) return redirect()->back()->with('error','File not found');

        $this->fileModel->update($id, ['is_deleted' => 0, 'deleted_at' => null]);
        return redirect()->back()->with('success','File restored');
    }


    public function permanentlyDeleteFile($id)
    {
        $user = session('user'); if (!$user) return redirect()->to('/login');
        $file = $this->fileModel->where('id',$id)->where('user_id',$user['id'])->first();
        if (!$file) return redirect()->back()->with('error','File not found');

        $storagePath = WRITEPATH . 'uploads/' . $file['storage_name'];
        if (file_exists($storagePath)) @unlink($storagePath);

        $this->fileModel->delete($id);
        return redirect()->back()->with('success','File permanently deleted');
    }

}
