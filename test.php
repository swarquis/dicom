<?php 
//include_once 'nanodicom.php';
//include_once 'class_dicom.php';
header("content-type:text/html;charset=utf-8");
$file = "D:/raw_113856P20161126006_黎素碧.dcm";
$file = iconv("utf-8","gbk",$file);

var_dump(file_exists($file));
//$d = new dicom_tag;

//$d->file = $file;
$exe_dir = "D:/xampp1/htdocs/dicom/dcmtk_exe/bin";
//$cmd = $exe_dir . "/dcmdump -M +L +Qn $file";
$cmd = $exe_dir . "/dcmdump -v -d +L $file";
$res = exec($cmd);
var_dump($res);
/*$d->dcm_to_tn();

system("ls -lsh $file*");

$job_end = time();
$job_time = $job_end - $job_start;
print "Created JPEG and thumbnail in $job_time seconds.\n";*/
/*try
	{
		//echo "20) Gets the images from the dicom object if they exist. This example is for gd\n";
		//var_dump(file_exists($dcmFile));
		$dcmFile ="D:/raw_113856P20161126006_黎素碧.dcm";
		$dcmFile = iconv("utf-8","gbk",$dcmFile);
		$dicom  = Nanodicom::factory($dcmFile, 'pixeler');
		if ( ! file_exists($dcmFile.'.jpg'))
		{
			$images = $dicom->set_driver('imagick')->get_images();
			//$images = $dicom->get_images();

			if ($images !== FALSE)
			{
				foreach ($images as $index => $image)
				{
					$dicom->write_image($image, $dcmFile);


				}
			}
			else
			{
				echo "传输格式不支持\n";
			}
			$images = NULL;
		}
		else
		{
			echo "图片已存在\n";
		}
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n";
	}
*/
 ?>