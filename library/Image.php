<?php

/**
 * filename library/Image.php
 * 
 * @author Wuild
 * @package openTracker
 */
class Image {

    /**
     * The image
     * @var resource 
     */
    var $image;

    /**
     * Image type
     * @var int 
     */
    var $image_type;

    /**
     * Image filename
     * @var string 
     */
    var $filename;

    /**
     * Load an image
     * @param string $filename 
     */
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

    /**
     * Add a watermark on the selected image. 
     */
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

    /**
     * Crop the selected image.
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h 
     */
    function crop($x, $y, $w, $h) {
        $crop = imagecreatetruecolor($w, $h);
        imagecopy($crop, $this->image, 0, 0, $x, $y, $w, $h);
        $this->image = $crop;
    }

    /**
     * Return the selected image to display. 
     */
    function output() {
        if ($this->image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($this->image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($this->image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }

    /**
     * Return selected image width
     * @return type 
     */
    function getWidth() {
        return imagesx($this->image);
    }

    /**
     * Return the selected image height
     * @return type 
     */
    function getHeight() {
        return imagesy($this->image);
    }

    /**
     * Resize the selected image height
     * @param int $height 
     */
    function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Resize the selected image width
     * @param type $width 
     */
    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);
    }

    /**
     * Rescale the selected image.
     * @param type $scale 
     */
    function scale($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);
    }

    /**
     * Resize the selected image
     * @param type $width
     * @param type $height 
     */
    function resize($width, $height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

    /**
     * Save the new image, do this if you manipulated the image,
     * @param string $filename
     * @param int $compression
     * @param int $permissions 
     */
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

}

?>
