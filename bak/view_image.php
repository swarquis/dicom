<?php 
include_once 'conn.php';
include_once 'class_dicom.php';
if($_GET['act'] == 'view'){
	$currentdir = __DIR__;
	$currentdir = str_replace('\\','/',$currentdir);
	$id = $_GET['id'];
	$page = $_GET['page'];
		$link = getCon();
		$sql = "SELECT dcmPath FROM ".TABLE.' WHERE id='.$id;
		$res = mysqli_query($link,$sql);
		if($res && mysqli_num_rows($res)==1){
			$dcmFile = mysqli_fetch_assoc($res)['dcmPath'];
			$dcmFile1 = iconv("utf-8","gbk",$dcmFile);//磁盘文件路径
			$arr = explode('/',$dcmFile1);
			$name = end($arr);
			$dcmFile = str_replace('.dcm','',$name);//磁盘文件名gbk
			$date = date("ymd",time());
			$jpgFile = $dcmFile.$date.'.jpg';//图片文件名gbk
			$tmpdir = $currentdir.'/tmp';
			if(!file_exists($tmpdir)){
					if(mkdir($tmpdir,0777,true)){
						echo "创建缓存目录";
					}
				}
			$tmpimg = $tmpdir.'/'.$jpgFile;//磁盘图片储存目录gbk
			
			if (!file_exists($dcmFile1)) {
			  echo "源文件不存在，无法预览";
			  exit;
			}
			if(!file_exists($tmpimg)){
				//echo "生成缓存图片";
				$d = new dicom_convert;
				$d->file = $dcmFile1;			
				$img = $d->dcm_to_jpg();
				rename($img,$tmpimg);
				$tmpimg = iconv("gbk","utf-8",$tmpimg);
				$tmpimg = explode('/',$tmpimg);
				$tmpimg = end($tmpimg);
			}else{
				unlink($tmpimg);
				$tmpimg = iconv("gbk","utf-8",$tmpimg);
				$tmpimg = explode('/',$tmpimg);
				$tmpimg = end($tmpimg);
			}
			
			
		}
	}

function getdata(){
	$id = $_GET['id'];
	$link = getCon();
	$sql = "SELECT * FROM ".TABLE." WHERE id={$id}";
	$data = array();
	$res = mysqli_query($link,$sql);
	if($res && mysqli_num_rows($res)==1){
		$data = mysqli_fetch_assoc($res);
	}
	return $data;
}

 ?>