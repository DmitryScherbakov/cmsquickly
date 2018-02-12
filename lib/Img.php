<?php

class Img
{

	public static function resize($origFile, $newWidth, $newHeight, $resizeMode = null)
	{
		//getting the image dimensions
		list($origWidth, $origHeight, $origType) = getimagesize($origFile);
		if (is_numeric($origType)) {
			$origType = image_type_to_mime_type($origType);
		}

		switch ($origType) {
			case "image/jpeg":
				$myImage = imagecreatefromjpeg($origFile);
				break;
			case "image/png":
				$myImage = imagecreatefrompng($origFile);
				break;
			case "image/gif":
				$myImage = imagecreatefromgif($origFile);
				break;
			default:
				$myImage = imagecreatefromjpeg($origFile);
				break;
		}
		$ret = false;
		if (function_exists('imagescale')) {
			if (function_exists('imagesetinterpolation')) {
				imagesetinterpolation($myImage, $resizeMode ? $resizeMode : IMG_BICUBIC);
				imagescale($myImage, $newWidth, $newHeight);
			} else {
				imagescale($myImage, $newWidth, $newHeight, $resizeMode ? $resizeMode : IMG_BICUBIC);
			}
		} else {
			$tmp = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($tmp, $myImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
		}

		switch ($origType) {
			case "image/jpeg":
				$ret = imagejpeg($myImage, $origFile);
				break;
			case "image/png":
				$ret = imagepng($myImage, $origFile);
				break;
			case "image/gif":
				$ret = imagegif($myImage, $origFile);
				break;
			default:
				$ret = imagejpeg($myImage, $origFile);
				break;
		}
		imagedestroy($myImage);
		return $ret;
	}

	public static function crop($imgSrc, $thumbFile, $thumbnail_width, $thumbnail_height, $quality = 75)
	{
		//getting the image dimensions
		list($width_orig, $height_orig, $type_orig) = getimagesize($imgSrc);
		if (is_numeric($type_orig)) {
			$type_orig = image_type_to_mime_type($type_orig);
		}

		switch ($type_orig) {
			case "image/gif":
				$myImage = imagecreatefromgif($imgSrc);
				break;
			case "image/jpeg":
				$myImage = imagecreatefromjpeg($imgSrc);
				break;
			case "image/png":
				$myImage = imagecreatefrompng($imgSrc);
				break;
			default:
				$myImage = imagecreatefromjpeg($imgSrc);
				break;
		}
		$ratio_orig = $width_orig / $height_orig;

		if ($thumbnail_width / $thumbnail_height > $ratio_orig) {
			$new_height = $thumbnail_width / $ratio_orig;
			$new_width = $thumbnail_width;
		} else {
			$new_width = $thumbnail_height * $ratio_orig;
			$new_height = $thumbnail_height;
		}

		$x_mid = $new_width / 2;  //horizontal middle
		$y_mid = $new_height / 2; //vertical middle

		$bg = imagecreatefromjpeg(ROOT . 'images/fon.jpg');
		$process = imagecreatetruecolor(round($new_width), round($new_height));

		$white = imagecolorallocate($process, 255, 255, 255);
		imagefill($process, 0, 0, $white);
		imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
		$thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		imagecopy($thumb, $bg, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height);
		$white = imagecolorallocate($thumb, 255, 255, 255);
		imagefill($thumb, 1, 1, $white);
		imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ceil($thumbnail_width / 2)), ($y_mid - ceil($thumbnail_height / 2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

		imagedestroy($process);
		imagedestroy($myImage);
		imagejpeg($thumb, $thumbFile, $quality);
		chmod($thumbFile, 0755);
	}

	public static function fit($imgSrc, $thumbFile, $thumbnail_width, $thumbnail_height, $quality = 75)
	{
		//getting the image dimensions
		list($width_orig, $height_orig, $type_orig) = getimagesize($imgSrc);
		if (is_numeric($type_orig)) {
			$type_orig = image_type_to_mime_type($type_orig);
		}

		switch ($type_orig) {
			case "image/gif":
				$myImage = imagecreatefromgif($imgSrc);
				break;
			case "image/jpeg":
				$myImage = imagecreatefromjpeg($imgSrc);
				break;
			case "image/png":
				$myImage = imagecreatefrompng($imgSrc);
				break;
			default:
				$myImage = imagecreatefromjpeg($imgSrc);
				break;
		}

		//$myImage = imagecreatefromjpeg($imgSrc);
		$ratio_orig = $width_orig / $height_orig;

		if ($thumbnail_width / $thumbnail_height <= $ratio_orig) {
			$new_height = round($thumbnail_width / $ratio_orig);
			$new_width = $thumbnail_width;
		} else {
			$new_width = round($thumbnail_height * $ratio_orig);
			$new_height = $thumbnail_height;
		}

		$x_mid = $new_width / 2;  //horizontal middle
		$y_mid = $new_height / 2; //vertical middle

		$bg = imagecreatefromjpeg(ROOT . 'images/fon.jpg');
		$thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		$thumb_bg = imagecolorallocate($thumb, 0xFF, 0xFF, 0xFF);
		imagecopy($thumb, $bg, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height);
		imagecopyresampled($thumb, $myImage, (ceil($thumbnail_width / 2) - $x_mid), (ceil($thumbnail_height / 2) - $y_mid), 0, 0, $new_width, $new_height, $width_orig, $height_orig);
		imageFill($thumb, 1, 1, $thumb_bg);

		imagedestroy($myImage);
		imagejpeg($thumb, $thumbFile, $quality);
		imagedestroy($thumb);
		chmod($thumbFile, 0755);
	}

}
