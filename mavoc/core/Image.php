<?php

namespace mavoc\core;

use imagick;

// This is not chainable for future development reasons and to promote one way of doing things.
class Image {
    public $_file;
    public $_image;

    public function __construct($file) {
        $this->file($file);
    }   

    public function file($file) {
        $this->_file = $file;
        $this->_image = new imagick($file);
    }

    public function resize($width, $height) {
        $original_width = $this->_image->getImageWidth();
        $original_height = $this->_image->getImageHeight();

        // Resize image down largest side.
        /*
        if($original_width > $original_height) {
            $this->_image->resizeImage(0, $height, Imagick::FILTER_LANCZOS, 1);
        } else {
            $this->_image->resizeImage($width, 0, Imagick::FILTER_LANCZOS, 1);
        }
         */

        $this->_image->cropThumbnailImage($width, $height);
    }

    public function save() {
        $this->_image->writeImage($this->_file);
    }

    // https://www.php.net/manual/en/imagick.stripimage.php
    public function stripMeta() {
        // Don't strip image profile (can affect colors).
        $profiles = $this->_image->getImageProfiles('icc', true);

        $this->_image->stripImage();

        if(!empty($profiles)) {
            $this->_image->profileImage('icc', $profiles['icc']);
        }
    }
}
