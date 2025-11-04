<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Traits\Authenticable;
use App\Models\FileModel;

class PreviewController extends Controller
{
    use Authenticable;

    protected $fileModel;
    protected $supportedPreviewTypes = [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'],
        'documents' => ['pdf', 'txt', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'],
        'audio' => ['mp3', 'wav', 'ogg', 'm4a'],
        'video' => ['mp4', 'avi', 'mov', 'mkv', 'webm'],
        'archive' => ['zip', 'rar', '7z', 'tar', 'gz', 'tgz'],
        'code' => ['php', 'js', 'css', 'html', 'xml', 'json', 'py', 'java', 'cpp', 'c', 'sql']
    ];

    public function __construct()
    {
        $this->fileModel = new FileModel();
    }

    /**
     * Preview file
     */
    public function preview($fileId)
    {
        helper('format');

        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $user;
        }

        // Get file information
        $file = $this->fileModel->getUserFile($fileId, $user['id']);
        if (!$file) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $file['storage_name'];
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setBody('File not found on server');
        }

        $fileExtension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $fileType = $this->getFileType($fileExtension);

        // Log preview activity
        $this->logPreviewActivity($user['id'], $fileId, $file['original_name']);

        // Use dashboard layout instead of preview layout
        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'fileType' => $fileType,
            'fileExtension' => $fileExtension,
            'fileUrl' => base_url('uploads/' . $file['storage_name']),
            'mimeType' => $this->getMimeType($fileExtension),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => base_url('/')],
                ['name' => 'Preview: ' . $file['original_name'], 'url' => current_url()]
            ]
        ];

        // Return appropriate preview based on file type using dashboard layout
        switch ($fileType) {
            case 'image':
                return view('preview/image_preview', $data);
            case 'pdf':
                return view('preview/pdf_preview', $data);
            case 'text':
                return view('preview/text_preview', $data);
            case 'audio':
                return view('preview/audio_preview', $data);
            case 'video':
                return view('preview/video_preview', $data);
            case 'document':
                return view('preview/document_preview', $data);
            case 'archive':
                return $this->previewArchive($data);
            case 'code':
                return $this->previewCodeDashboard($data);
                break;
            default:
                return $this->previewUnsupportedDashboard($data);
        }
    }

    /**
     * Preview code files with dashboard layout
     */
    private function previewCodeDashboard($data)
    {
        $filePath = WRITEPATH . 'uploads/' . $data['file']['storage_name'];
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'ASCII'], true);
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        
        // Limit preview to first 500KB for performance
        if (strlen($content) > 512000) {
            $content = substr($content, 0, 512000) . "\n\n... (Preview truncated - file too large)";
        }

        $data['content'] = htmlspecialchars($content);
        $data['language'] = $this->getCodeLanguage($data['fileExtension']);
        
        return view('preview/code_preview', $data);
    }

    /**
     * Get MIME type for file
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'mkv' => 'video/x-matroska',
            'webm' => 'video/webm',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Get file type category
     */
    private function getFileType($extension)
    {
        if (in_array($extension, $this->supportedPreviewTypes['images'])) {
            return 'image';
        }
        if (in_array($extension, ['pdf'])) {
            return 'pdf';
        }
        if (in_array($extension, ['txt'])) {
            return 'text';
        }
        if (in_array($extension, $this->supportedPreviewTypes['audio'])) {
            return 'audio';
        }
        if (in_array($extension, $this->supportedPreviewTypes['video'])) {
            return 'video';
        }
        if (in_array($extension, $this->supportedPreviewTypes['documents'])) {
            return 'document';
        }
        if (in_array($extension, $this->supportedPreviewTypes['code'])) {
            return 'code';
        }
        if (in_array($extension, $this->supportedPreviewTypes['archive'])) {
            return 'archive';
        }

        return 'unsupported';
    }

    private function previewArchive($data)
    {
        $filePath = WRITEPATH . 'uploads/' . $data['file']['storage_name'];
        $extension = strtolower(pathinfo($data['file']['original_name'], PATHINFO_EXTENSION));
        
        $files = [];

        if ($extension === 'zip') {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    $files[] = [
                        'name' => $stat['name'],
                        'size' => $stat['size'],
                        'compressed' => $stat['comp_size'],
                    ];
                }
                $zip->close();
            }
        } 
        elseif ($extension === 'rar' && class_exists('RarArchive')) {
            $rar = \RarArchive::open($filePath);
            foreach ($rar->getEntries() as $entry) {
                $files[] = [
                    'name' => $entry->getName(),
                    'size' => $entry->getUnpackedSize(),
                    'compressed' => null,
                ];
            }
            $rar->close();
        }
        else {
            // fallback using 7z
            $output = shell_exec("7z l ".escapeshellarg($filePath));
            $files = $this->parse7zListing($output);
        }

        $data['archiveFiles'] = $files;

        return view('preview/archive_preview', $data);
    }

    /**
     * Preview image files
     */
    private function previewImage($file, $filePath)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml'
        ];

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$extension] ?? 'image/jpeg';

        // For SVG, we can return directly
        if ($extension === 'svg') {
            return $this->response
                ->setContentType($mimeType)
                ->setBody(file_get_contents($filePath));
        }

        // For other images, create a preview view
        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'fileUrl' => base_url('uploads/' . $file['storage_name']),
            'fileType' => 'image',
            'mimeType' => $mimeType
        ];

        return view('preview/image_preview', $data);
    }

    /**
     * Preview PDF files
     */
    private function previewPdf($file, $filePath)
    {
        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'fileUrl' => base_url('uploads/' . $file['storage_name']),
            'fileType' => 'pdf'
        ];

        return view('preview/pdf_preview', $data);
    }

    /**
     * Preview text files
     */
    private function previewText($file, $filePath)
    {
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'ASCII'], true);
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        
        // Limit preview to first 100KB for performance
        if (strlen($content) > 102400) {
            $content = substr($content, 0, 102400) . "\n\n... (Preview truncated - file too large)";
        }

        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'content' => htmlspecialchars($content),
            'fileType' => 'text'
        ];

        return view('preview/text_preview', $data);
    }

    /**
     * Preview audio files
     */
    private function previewAudio($file, $filePath)
    {
        $mimeTypes = [
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4'
        ];

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$extension] ?? 'audio/mpeg';

        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'fileUrl' => base_url('uploads/' . $file['storage_name']),
            'fileType' => 'audio',
            'mimeType' => $mimeType
        ];

        return view('preview/audio_preview', $data);
    }

    /**
     * Preview video files
     */
    private function previewVideo($file, $filePath)
    {
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'mkv' => 'video/x-matroska',
            'webm' => 'video/webm'
        ];

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$extension] ?? 'video/mp4';

        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'fileUrl' => base_url('uploads/' . $file['storage_name']),
            'fileType' => 'video',
            'mimeType' => $mimeType
        ];

        return view('preview/video_preview', $data);
    }

    /**
     * Preview document files (Word, Excel, PowerPoint)
     */
    private function previewDocument($file, $filePath)
    {
        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'fileUrl' => base_url('uploads/' . $file['storage_name']),
            'fileType' => 'document'
        ];

        return view('preview/document_preview', $data);
    }

    /**
     * Preview code files
     */
    private function previewCode($file, $filePath)
    {
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'ASCII'], true);
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        
        // Limit preview to first 500KB for performance
        if (strlen($content) > 512000) {
            $content = substr($content, 0, 512000) . "\n\n... (Preview truncated - file too large)";
        }

        $extension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $language = $this->getCodeLanguage($extension);

        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'content' => htmlspecialchars($content),
            'fileType' => 'code',
            'language' => $language
        ];

        return view('preview/code_preview', $data);
    }

    /**
     * Get programming language for syntax highlighting
     */
    private function getCodeLanguage($extension)
    {
        $languages = [
            'php' => 'php',
            'js' => 'javascript',
            'css' => 'css',
            'html' => 'html',
            'xml' => 'xml',
            'json' => 'json',
            'py' => 'python',
            'java' => 'java',
            'cpp' => 'cpp',
            'c' => 'c',
            'sql' => 'sql',
            'md' => 'markdown'
        ];

        return $languages[$extension] ?? 'plaintext';
    }

    /**
     * Preview for unsupported file types
     */
    private function previewUnsupported($file, $filePath)
    {
        $data = [
            'title' => 'Preview: ' . $file['original_name'],
            'file' => $file,
            'fileType' => 'unsupported'
        ];

        return view('preview/unsupported_preview', $data);
    }

    /**
     * Get file information for preview modal
     */
    public function getFileInfo($fileId)
    {
        $user = $this->getAuthenticatedUser();
        if (!is_array($user)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $file = $this->fileModel->getUserFile($fileId, $user['id']);
        if (!$file) {
            return $this->response->setJSON(['success' => false, 'message' => 'File not found']);
        }

        $fileExtension = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
        $fileType = $this->getFileType($fileExtension);
        $isPreviewable = $fileType !== 'unsupported';

        return $this->response->setJSON([
            'success' => true,
            'file' => [
                'id' => $file['id'],
                'name' => $file['original_name'],
                'size' => $file['size'],
                'type' => $fileType,
                'extension' => $fileExtension,
                'isPreviewable' => $isPreviewable,
                'previewUrl' => base_url('preview/' . $file['id'])
            ]
        ]);
    }

    /**
     * Log preview activity
     */
    private function logPreviewActivity($userId, $fileId, $fileName)
    {
        $activityLogModel = new \App\Models\ActivityLogModel();
        
        $activityLogModel->logActivity([
            'user_id' => $userId,
            'activity_type' => \App\Models\ActivityLogModel::TYPE_FILE_PREVIEW,
            'item_type' => 'file',
            'item_id' => $fileId,
            'item_name' => $fileName,
            'description' => "Previewed file: {$fileName}"
        ]);
    }
}