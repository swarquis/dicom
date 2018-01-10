<?php 
include_once 'conn.php';
include_once 'nanodicom.php';
session_start();
//error_reporting(E_ALL^E_NOTICE^E_WARNING);
del_records($_SESSION['keywords']);
download();
dcmtoBase64();
//过滤条件
function filter($name,$date1,$date2){
	$tmppage = $_GET['page'];
	if(!is_numeric($tmppage)){
		header("location:view_records.php?page=1");
	}
	$tmp1 = dconv($date1);
	$tmp2 = dconv($date2);
	$name = htmlspecialchars(trim($name));
	$datefrom = empty($tmp1)?0:(int)$tmp1;
	$dateto = empty($tmp2)?0:(int)$tmp2;
	//var_dump($name,$datefrom,$dateto);
	if(empty($name) && $datefrom == 0 && $dateto == 0){
		$where = '';
		return $where;
	}
	if(empty($name)){
		$wherename = '';
	}else{
		$wherename = "AND name LIKE '%{$name}%'";
	}
	if($datefrom == 0 && $dateto != 0){
		$where = "WHERE jcrqstamp={$dateto} {$wherename}";
	}else if($datefrom != 0 && $dateto == 0){
		$where = "WHERE jcrqstamp={$datefrom} {$wherename}";
		return $where;
	}else if($datefrom == 0 && $dateto == 0){
		$where = "WHERE name LIKE '%{$name}%'";
	}else if($datefrom != 0 && $dateto != 0){
		if($datefrom > $dateto){
		$where = '';
		return $where;
	}else{
		$where = "WHERE jcrqstamp BETWEEN {$datefrom} AND {$dateto} {$wherename}";
	}
		
	}

	return $where;
	}


//转换日期到时间戳
function dconv($date){
	return $tamp = strtotime($date);
}

function showInfo($name,$date1,$date2){


	$where = filter($name,$date1,$date2);
	
	$tmppage = getCurrentPage();
	$link = getCon();
	//$link = getCon();
	$sql1 = "SELECT COUNT(*) as totalrows FROM ".TABLE.' '.$where;
	
	$res = mysqli_query($link,$sql1);
	if($res && mysqli_num_rows($res)==1){
		while($row = mysqli_fetch_assoc($res)){
			$totalRows = $row['totalrows'];

		}
	}else{
		$totalRows = 0;
	}
	
	$totalPages = ceil($totalRows/(int)PAGESIZE);

	$currentPage = empty($tmppage)?1:(int)$tmppage;
	if($currentPage>$totalPages || $currentPage<1 || !is_numeric($currentPage)){
		$currentPage = 1;
	}
	$offset = ($currentPage-1)*(int)PAGESIZE;
	

	//var_dump($where);die;
	$sql2 = "SELECT id,name,age,sex,jcbw,jcrq FROM ".TABLE." ".$where." ORDER BY jcrq DESC LIMIT {$offset},".PAGESIZE; 
	
	//$sql2 = "SELECT id,name,age,sex,jcbw,jcrq FROM ".TABLE." ".$where; 
	
	$res = mysqli_query($link,$sql2);
	
	if($res && (mysqli_num_rows($res) >= 1)){		
		while($row = mysqli_fetch_assoc($res)){
			$rows[] = $row;
		}
	}

	return $rows;
}

function del_records(){
	if($_GET['act'] == 'del'){
		$del_id = $_GET['id'];
		$page = $_GET['page'];
		$sql1 = "SELECT dcmPath FROM ".TABLE." WHERE id=".$del_id;
		
		$sql2 = "DELETE FROM ".TABLE." WHERE id=".$del_id;
		$link = getCon();
		$res1 = mysqli_query($link,$sql1);
		
		if($res1 && mysqli_num_rows($res1)==1){
			while($row = mysqli_fetch_assoc($res1)){
				$dcmPath = $row['dcmPath'];
				$dcmPath = iconv("utf-8","gbk",$dcmPath);
				//var_dump($dcmPath);die;
				if(!file_exists($dcmPath)){
					echo "未找到源文件.";
					echo "<meta http-equiv='refresh' content=1;'view_records.php?page={$page}'>";
				}else{
					if(unlink($dcmPath)){
						echo "源文件删除成功.";
						$res2 = mysqli_query($link,$sql2);
							if($res2 && mysqli_affected_rows($link) == 1){
								echo "数据删除成功";
								echo "<meta http-equiv='refresh' content=1;'view_records.php?page={$page}'>";

							}else{
								echo "数据删除失败";
								echo "<meta http-equiv='refresh' content=1;'view_records.php?page={$page}'>";
							}
					
				}else{
					echo "源文件删除失败";
					echo "<meta http-equiv='refresh' content=1;'view_records.php?page={$page}'>";

				}
				}
				
			}
			
		}
	}
	
}

function getCurrentPage(){
	$_SESSION['page'] = $_GET['page'];
	$page = $_SESSION['page'];
	return $page;
}

//分页
function page($name,$date1,$date2){	
    $where = filter($name,$date1,$date2);
	$tmppage = getCurrentPage();
	//总记录数
	$link = getCon();
	$sql1 = "SELECT COUNT(*) as totalrows FROM ".TABLE." ".$where;
	$res = mysqli_query($link,$sql1);
	if($res && mysqli_num_rows($res)==1){
		while($row = mysqli_fetch_assoc($res)){
			$totalRows = $row['totalrows'];
		}
	}else{
		$totalRows = 0;
	}
	
	$totalPages = ceil($totalRows/(int)PAGESIZE);

	$currentPage = empty($tmppage)?1:(int)$tmppage;
	if($currentPage>$totalPages || $currentPage<1 || !is_numeric($currentPage)){
		$currentPage = 1;
	}
	$offset = ($currentPage-1)*(int)PAGESIZE;
	
	$start = $currentPage-4;
	if($start < 1){
		$start = 1;

	}
	$end = $currentPage+5;
	if($currentPage < 5){
		$end = 10;
	}
	
	if($end > $totalPages){
		$end = $totalPages;
		$start = $end-9;
	}
	echo "<a href='view_records.php?page=1'>首页</a>";
	for($p=$start;$p<=$end;$p++){

		if($p == $currentPage){
			echo $p;
		}else{

			echo "<a href='view_records.php?page={$p}'>{$p}</a>";
		}
		

	}
	echo "<a href='view_records.php?page={$totalPages}'>尾页</a>";
	echo "<br/><span>当前第".$currentPage."页/共".$totalPages."页</span><br/>";

}

//下载文件
function download(){
	if($_GET['act'] == 'download'){
		$page = $_GET['page'];
		$id = $_GET['id'];
		$link = getCon();
		$sql = "SELECT dcmPath FROM ".TABLE.' WHERE id='.$id;
		$res = mysqli_query($link,$sql);
		if($res && mysqli_num_rows($res)==1){
			$dcmFile = mysqli_fetch_assoc($res)['dcmPath'];
			$dcmFile1 = iconv("utf-8","gbk",$dcmFile);
			$dcmFile = explode('/',$dcmFile1);
			$dcmFile = end($dcmFile);
			 $date=date("Ymd-H:i:m");
			 Header( "Content-type:  application/octet-stream "); 
			 Header( "Accept-Ranges:  bytes "); 
			 Header( "Accept-Length: " .filesize($dcmFile1));
			 header( "Content-Disposition:  attachment;  filename= {$dcmFile}"); 
			 $fp = fopen($dcmFile1,"r");
			 $file_size = filesize($dcmFile1);
			 $buffer=1024; 
			$file_count=0; 
			while(!feof($fp) && $file_count<$file_size){ 
			$file_con=fread($fp,$buffer); 
			$file_count+=$buffer; 
			echo $file_con; 
			} 
			fclose($fp);
		 /*echo file_get_contents($dcmFile);
		 readfile($dcmFile); */
		 //var_dump($dcmFile,is_file($dcmFile));
		}else{
			echo "未找到源文件";
			echo "<meta http-equiv='refresh' content=1;url=view_records.php?page={$page}";
		}
	}
}

function dcmtoBase64(){
	if($_GET['act'] == 'view'){
		$id = $_GET['id'];
		$link = getCon();
		$sql = "SELECT dcmPath FROM ".TABLE.' WHERE id='.$id;
		$res = mysqli_query($link,$sql);
		if($res && mysqli_num_rows($res)==1){
			$dcmFile = mysqli_fetch_assoc($res)['dcmPath'];
			$dcmFile = iconv("utf-8","gbk",$dcmFile);
		}
		return $dcmFile;
		/*if(!file_exists($dcmFile)){
		echo "源文件不存在";
		echo "<meta http-equiv='refresh' content=1;url=view_records?page={$page}>";
	}else{
		var_dump($dcmFile,file_exists($dcmFile));die;
		echo "<meta http-equiv='refresh' content=0;url=./imageLoader/examples/dicomfile/view.php?file={$dcmFile}>";
	}
	}else{

	}*/
	}
}
function view(){
	if($_GET['act'] == 'view'){
		$id = $_GET['id'];
		$link = getCon();
		$sql = "SELECT dcmPath FROM ".TABLE.' WHERE id='.$id;
		$res = mysqli_query($link,$sql);
		if($res && mysqli_num_rows($res)==1){
			$dcmFile = mysqli_fetch_assoc($res)['dcmPath'];
			$dcmFile = iconv("utf-8","gbk",$dcmFile);
		}
		if(!file_exists($dcmFile)){
		echo "源文件不存在";
		echo "<meta http-equiv='refresh' content=1;url=view_records?page={$page}>";
		
	}else{
		try
	{
		//echo "20) Gets the images from the dicom object if they exist. This example is for gd\n";
		//var_dump(file_exists($dcmFile));
		$dicom  = Nanodicom::factory($dcmFile, 'pixeler');
		if ( ! file_exists($dcmFile.'.jpg'))
		{
			
			$images = $dicom->get_images();

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
	
	}
}

}

 ?>