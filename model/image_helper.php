<?php
function upload_and_optimize_image($file, $target_dir, $old_image = null) {
    
    if (!isset($file['name']) || $file['error'] != 0) {
        return $old_image; 
    }

    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    
    $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($imageFileType, $allowed_types)) {
        return $old_image;
    }

    
    $new_filename = uniqid() . '.webp';
    $target_path = $target_dir . $new_filename;

    
    $source_image = null;
    if ($imageFileType == 'jpg' || $imageFileType == 'jpeg') {
        $source_image = imagecreatefromjpeg($file['tmp_name']);
    } elseif ($imageFileType == 'png') {
        $source_image = imagecreatefrompng($file['tmp_name']);
        
        imagepalettetotruecolor($source_image);
        imagealphablending($source_image, true);
        imagesavealpha($source_image, true);
    } elseif ($imageFileType == 'webp') {
        $source_image = imagecreatefromwebp($file['tmp_name']);
    }

    if ($source_image) {
        
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        $max_width = 1200; 
        if ($width > $max_width) {
            $new_width = $max_width;
            $new_height = floor($height * ($max_width / $width));
            
            $temp_image = imagecreatetruecolor($new_width, $new_height);
            
           
            imagealphablending($temp_image, false);
            imagesavealpha($temp_image, true);
            
            imagecopyresampled($temp_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $source_image = $temp_image;
        }

        
        imagewebp($source_image, $target_path, 100);
        
        
        imagedestroy($source_image);

        
        if ($old_image && file_exists($target_dir . $old_image)) {
            unlink($target_dir . $old_image);
        }

        return $new_filename;
    }

    return $old_image; 
}
?>