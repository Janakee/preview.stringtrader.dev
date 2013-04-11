<?php
function resizeImage($width, $height, $sourceFile, $destinationFile)
{
	$o_file = $sourceFile;
	$dotPos = strrpos($sourceFile, ".") + 1;
	$extension = substr($sourceFile, $dotPos, strlen($sourceFile) - $dotPos);
	$o_im = null;
	if (strtolower($extension) == "jpg" || strtolower($extension) == "jpeg")
	{
		$o_im = imageCreateFromJPEG($o_file) ;
	}
	else if(strtolower($extension) == "png")
	{
		$o_im = imageCreateFromPNG($o_file) ;
	}
	else if(strtolower($extension) == "gif")
	{
		$o_im = imageCreateFromGIF($o_file) ;
	}
	//$o_im = imageCreateFromJPEG($o_file);
	$t_wd = 0;
	$t_ht = 0;
	if (!isset($ermsg) && isset($o_im))
	{
		$o_wd = imagesx($o_im);
		$o_ht = imagesy($o_im);
		//if width and height is given - resize, else make thumbnail of given dimension
		if($width!="" && $height!="")
		{
			$t_wd = $width;
			$t_ht = $height;
		}
		else
		{
			//if($o_wd >= $o_ht)
			if($width!="")
			{
				if( $o_wd >= $width )
				{
					$t_wd = $width;
					$t_ht = round($o_ht * $t_wd / $o_wd);
				}
				else
				{
					$t_wd = $o_wd;
					$t_ht = $o_ht;
				}
			}
			else
			{
				if( $o_ht >= $height )
				{
					$t_ht = $height;
					$t_wd = round($o_wd * $t_ht / $o_ht);
				}
				else
				{
					$t_wd = $o_wd;
					$t_ht = $o_ht;
				}
			}
		}

		$t_im = imageCreateTrueColor($t_wd, $t_ht);
		if(strtolower($extension) == "png" || strtolower($extension) == "gif")
		{
			imagealphablending( $t_im, false );
			imagesavealpha( $t_im, true );
			if(strtolower($extension) == "gif")
			{
				$transindex = imagecolortransparent($o_im);
				if($transindex >= 0) {
					$transcol = imagecolorsforindex($o_im, $transindex);
					$transindex = imagecolorallocatealpha($t_im, $transcol['red'], $transcol['green'], $transcol['blue'], 127);
					imagefill($t_im, 0, 0, $transindex);
				}
			}
		}

		imageCopyResampled($t_im, $o_im, 0, 0, 0, 0, $t_wd, $t_ht, $o_wd, $o_ht);
		
		if(strtolower($extension) == "gif")
		{
			if($transindex >= 0) 
			{
				imagecolortransparent($t_im, $transindex);
				for($y=0; $y<$t_ht; ++$y)
				for($x=0; $x<$t_wd; ++$x)
				  if(((imagecolorat($t_im, $x, $y)>>24) & 0x7F) >= 100) imagesetpixel($t_im, $x, $y, $transindex);

			}
			imagetruecolortopalette($t_im, true, 255);
		}

		$t_file = $destinationFile;
		if (strtolower($extension) == "jpg" || strtolower($extension) == "jpeg")
		{
			if(imagejpeg($t_im,$t_file,90)) //quality==90 (from 0 to 100)
			{
				//return true;
			}
			else
			{
				return false;
			}
		}
		else if(strtolower($extension) == "png")
		{
			if(imagepng($t_im,$t_file,2)) //0-no compression, 9 - max compression
			{
				//return true;
			}
			else
			{
				return false;
			}
		}
		else if(strtolower($extension) == "gif")
		{
			if(imagegif($t_im,$t_file))
			{
				//return true;
			}
			else
			{
				return false;
			}
		}

		imageDestroy($o_im);
		imageDestroy($t_im);
		return true;
	}
	else
	{
		return false;
	}
}
?>