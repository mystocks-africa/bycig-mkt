<?php
namespace App\Core\Files;

use Exception;

class Files 
{
    public static function uploadFile(array $file): string|false 
    {
        $uploadDir = __DIR__ . "/../../../public/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $filename = bin2hex(random_bytes(5)) . ".pdf";
        $fullPath = $uploadDir . $filename;
        
        return move_uploaded_file($file["tmp_name"], $fullPath) ? $filename : false;
    }

    public static function deleteFile(string $fileName): void
    {
        $fullPath = __DIR__ . "/../../../public/uploads/" . $fileName;
        if (file_exists($fullPath)) {
            if (!unlink($fullPath)) {
                echo "Could not delete $fullPath";
            }
        } else {
            throw new Exception("File path is invalid");
        }
    }

    public static function getFile(string $fileName): string
    {
        $fullPath = __DIR__ . "/../../../public/uploads/" . $fileName;

        if (!file_exists($fullPath)) {
            throw new Exception("File not found: " . $fileName);
        }

        return $fullPath; // returns the full system path
    }
}