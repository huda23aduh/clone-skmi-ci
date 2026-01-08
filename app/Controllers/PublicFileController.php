<?php

namespace App\Controllers;

class PublicFileController extends BaseController
{
    protected $fileModel;
    
    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        $this->fileModel = new \App\Models\FileModel();
    }

    public function previewPublic($fileId)
    {
        helper('format');
    
        // Get file information - only public files
        $file = $this->fileModel->getPublicFileByFileId($fileId);
        
        if (!$file) {
            return $this->response->setStatusCode(404)->setBody('File not found or not publicly accessible');
        }
    
        $filePath = WRITEPATH . 'uploads/' . $file['storage_name'];
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setBody('File not found on server');
        }
    
        $fileExtension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $fileType = $this->getFileType($fileExtension);
    
        // Log public preview activity (optional - only if user is logged in)
        if (session()->has('user')) {
            $this->logActivity(
                session()->get('user')['id'], 
                'public_file_view', 
                'Viewed public file: ' . $file['original_name'],
                ['file_id' => $fileId]
            );
        }
    
        $data = [
            'title' => 'Public File: ' . $file['original_name'],
            'file' => $file,
            'fileType' => $fileType,
            'fileExtension' => $fileExtension,
            // 'fileUrl' => base_url('view-file/download/' . $file['storage_name']), // Updated URL
            'fileUrl' => base_url('uploads/' . $file['storage_name']),
            'mimeType' => $this->getMimeType($fileExtension),
            'isPublic' => true,
            'layout' => 'public', // Use public layout
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Public File: ' . $file['original_name'], 'url' => current_url()]
            ]
        ];
    
        // Use public layout template
        switch ($fileType) {
            case 'image':
                return view('preview/image_preview_public', $data);
            case 'pdf':
                return view('preview/pdf_preview_public', $data);
            case 'text':
                return view('preview/text_preview_public', $data);
            case 'audio':
                return view('preview/audio_preview_public', $data);
            case 'video':
                return view('preview/video_preview_public', $data);
            case 'document':
                return view('preview/document_preview_public', $data);
            case 'archive':
                return $this->previewArchivePublic($data);
            case 'code':
                return $this->previewCodePublic($data);
            case 'csv':
                return $this->previewCsvPublic($data);
            default:
                return view('preview/unsupported_preview_public', $data);
        }
    }
    
    /**
     * Direct download for public files
     */
    public function downloadPublic($storageName)
    {
        $file = $this->fileModel->where('storage_name', $storageName)
                                ->where('is_public', 1)
                                ->where('is_deleted', 0)
                                ->first();
        
        if (!$file) {
            return $this->response->setStatusCode(404)->setBody('File not found or not publicly accessible');
        }

        // Hardcode the path for testing
        $filePath = WRITEPATH . 'uploads/' . $storageName;
        
        // Test if file exists
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)
                ->setBody("File not found at: {$filePath}");
        }
        
        // Test file size
        $fileSize = filesize($filePath);
        if ($fileSize === 0) {
            return $this->response->setStatusCode(500)
                ->setBody("File is empty (0 bytes) at: {$filePath}");
        }
        
        // Manually set headers and serve file
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
        header('Content-Length: ' . $fileSize);
        header('Pragma: public');
        
        // Clear output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Read and output the file
        readfile($filePath);
        exit();
    }
    /**
     * Helper method to get file type
     */
    private function getFileType($extension)
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $pdfTypes = ['pdf'];
        $textTypes = ['txt', 'md', 'log'];
        $audioTypes = ['mp3', 'wav', 'ogg', 'flac'];
        $videoTypes = ['mp4', 'avi', 'mov', 'mkv', 'webm'];
        $documentTypes = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'odt', 'ods'];
        $archiveTypes = ['zip', 'rar', 'tar', 'gz', '7z'];
        $codeTypes = ['php', 'js', 'html', 'css', 'py', 'java', 'cpp', 'c', 'json', 'xml'];
        $csvTypes = ['csv'];
    
        if (in_array($extension, $imageTypes)) return 'image';
        if (in_array($extension, $pdfTypes)) return 'pdf';
        if (in_array($extension, $textTypes)) return 'text';
        if (in_array($extension, $audioTypes)) return 'audio';
        if (in_array($extension, $videoTypes)) return 'video';
        if (in_array($extension, $documentTypes)) return 'document';
        if (in_array($extension, $archiveTypes)) return 'archive';
        if (in_array($extension, $codeTypes)) return 'code';
        if (in_array($extension, $csvTypes)) return 'csv';
        
        return 'unsupported';
    }
    
    /**
     * Helper method to get MIME type
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'mkv' => 'video/x-matroska',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'php' => 'application/x-php',
            'js' => 'application/javascript',
            'html' => 'text/html',
            'css' => 'text/css',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'csv' => 'text/csv'
        ];
    
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
    
    /**
     * Log activity helper
     */
    private function logActivity($userId, $type, $description, $metadata = [])
    {
        if (!$userId) return;
        
        $activityLogModel = new \App\Models\ActivityLogModel();
        $activityLogModel->insert([
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'metadata' => json_encode($metadata),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Preview archive file (public version)
     */
    private function previewArchivePublic($data)
    {
        return view('preview/archive_preview_public', $data);
    }
    
    /**
     * Preview code file (public version)
     */
    private function previewCodePublic($data)
    {
        return view('preview/code_preview_public', $data);
    }
    
    /**
     * Preview CSV file (public version)
     */
    private function previewCsvPublic($data)
    {
        return view('preview/csv_preview_public', $data);
    }
}