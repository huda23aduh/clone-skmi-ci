<?php

namespace App\Controllers;

use App\Models\FileModel;
use App\Models\FolderModel;

class PublicController extends BaseController
{
    public function accessFile($token)
    {
        $fileModel = new FileModel();
        $file = $fileModel->getPublicFile($token);

        if (!$file) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'File not found or not publicly accessible'
            ]);
        }

        // Serve the file for download/viewing
        return $this->downloadFile($file);
    }

    public function accessFolder($token)
    {
        $folderModel = new FolderModel();
        $folder = $folderModel->getPublicFolder($token);

        if (!$folder) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Folder not found or not publicly accessible'
            ]);
        }

        $contents = $folderModel->getPublicContents($folder->id);

        return $this->response->setJSON([
            'folder' => $folder,
            'contents' => $contents
        ]);
    }

    private function downloadFile($file)
    {
        $filePath = WRITEPATH . 'uploads/' . $file->storage_name;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'File not found on server'
            ]);
        }

        return $this->response->download($filePath, null)
            ->setFileName($file->original_name);
    }
}