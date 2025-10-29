<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DebugController extends Controller
{
    public function uploadLimits()
    {
        helper('upload');
        $limits = get_upload_limits();
        
        echo "<h1>PHP Upload Limits</h1>";
        echo "<pre>" . print_r($limits, true) . "</pre>";
        
        // Test some file sizes
        $test_sizes = [
            1024,           // 1KB
            1024 * 1024,    // 1MB
            10 * 1024 * 1024, // 10MB
            100 * 1024 * 1024, // 100MB
            500 * 1024 * 1024, // 500MB
        ];
        
        echo "<h2>File Size Tests</h2>";
        foreach ($test_sizes as $size) {
            $can_upload = can_upload_file($size);
            $status = $can_upload ? '✅ CAN upload' : '❌ CANNOT upload';
            echo "<p>{$status}: " . format_bytes($size) . "</p>";
        }
    }
}