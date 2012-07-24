<?php

class Image {

    var $image;
    var $image_type;
    var $filename;

    function load($filename) {

        $image_info = getimagesize($filename);

        $this->filename = $filename;

        $this->image_type = $image_info[2];
        if ($this->image_type == IMAGETYPE_JPEG) {

            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->image_type == IMAGETYPE_GIF) {

            $this->image = imagecreatefromgif($filename);
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            $this->image = imagecreatefrompng($filename);
            imagealphablending($this->image, false);
            imagesavealpha($this->image, true);
        }
    }

    function save($filename = false, $compression = 75, $permissions = null) {
        if (!$filename)
            $filename = $this->filename;

        if ($this->image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($this->image_type == IMAGETYPE_GIF) {

            imagegif($this->image, $filename);
        } elseif ($this->image_type == IMAGETYPE_PNG) {

            imagepng($this->image, $filename);
        }
        if ($permissions != null) {

            chmod($filename, $permissions);
        }
    }

    function addWatermark() {

        $watermark = imagecreatefrompng(PATH_ROOT . 'watermark.png');

        $watermark_width = imagesx($watermark);
        $watermark_height = imagesy($watermark);

        $dest_x = $this->getWidth() - $watermark_width - 5;
        $dest_y = $this->getHeight() - $watermark_height - 5;

        imagealphablending($this->image, true);
        imagealphablending($watermark, true);

        imagecopy($this->image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
    }

    function crop($x, $y, $w, $h) {
        $crop = imagecreatetruecolor($w, $h);
        imagecopy($crop, $this->image, 0, 0, $x, $y, $w, $h);
        $this->image = $crop;
    }

    function output() {

        if ($this->image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }

    function getWidth() {

        return imagesx($this->image);
    }

    function getHeight() {

        return imagesy($this->image);
    }

    function resizeToHeight($height) {

        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    function scale($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    function resize($width, $height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

}

?>
