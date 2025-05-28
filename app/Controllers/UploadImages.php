<?php
namespace   App\Controllers;
use CodeIgniter\Controller;
class UploadImages extends Controller{
    function uploadimg($file,$folder)
    {
        $fileName = $file;
        //$folder = 'uploads/'.$folder;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            
            $uploadPath = 'uploads/'.$folder;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $filePath = $uploadPath . '/' . $newName;  
            return json_encode([
                'status'  => true,
                'message' => 'Image uploaded successfully!',
                'file'    => $filePath
            ]);

        }else{
            return json_encode([
                'status'  => true,
                'message' => 'Image uploaded successfully!',
            ]);
        }
    }
}