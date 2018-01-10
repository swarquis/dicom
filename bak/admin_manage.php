<?php 
include_once 'conn.php';
session_start();
//error_reporting(E_ALL^E_NOTICE^E_WARNING);

login();
logout();
del_records();

//登录
function login(){
	if($_GET['act'] == 'login'){

		$name = $_POST['name'];
		$pwd = $_POST['password'];
		if(empty($name) || empty($pwd)){
			echo "登录信息不能为空";
			echo "<meta http-equiv='refresh' content=1;url='admin_login.php'>";
		}
		$name = htmlspecialchars(trim($name));
		$pwd = htmlspecialchars(trim($pwd));
		$pwd = md5($pwd);
		$link = getCon();
		$sql = "SELECT id FROM admin WHERE name='{$name}' AND password='{$pwd}'";
		$res = mysqli_query($link,$sql);

		if($res && mysqli_num_rows($res)==1){
			$row = mysqli_fetch_assoc($res);
			$_SESSION['userid'] = $row['id'];
			echo "<script type='text/javascript'>alert('登录成功.')</script>";
			echo "<meta http-equiv='refresh' content=0;url='admin.php'>";
		}else{
			echo "<script type='text/javascript'>alert('登录失败.')</script>";
			echo "<meta http-equiv='refresh' content=0;url='admin_login.php'>";
		}
		}
	
}

//推出
function logout(){
	if($_GET['act'] == 'logout'){
		$_SESSION['userid'] = null;
		session_destroy();
		echo "退出成功.";
		echo "<meta http-equiv='refresh' content=1;url='admin_login.php'>";
	}
}

function checkuser(){
	$status = $_SESSION['userid'];
	if(empty($status)){
		echo "请先登录";
		return false;
		//echo "<meta http-equiv='refresh' content=1;url=admin_login.php>";
	}else{return true;}

}
//总记录数
function filter($name,$date1,$date2){
	$tmppage = $_GET['page'];
	if(!is_numeric($tmppage)){
		header("location:admin.php?page=1");
	}
	$tmp1 = $date1;
	$tmp2 = $date2;
	$name = htmlspecialchars(trim($name));
	$datefrom = empty($tmp1)?0:(int)$tmp1;
	$dateto = empty($tmp2)?0:(int)$tmp2;
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
		$where = $wherename;
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

function showInfo($date1,$date2){

	$timestamp1 = dconv($date1);
	$timestamp2 = dconv($date2);
	
	$where = filter($timestamp1,$timestamp2);
	if($timestamp1 > $timestamp2){
		echo "<script type='text/javascript'>alert('请确保起始日期小于截止日期!')</script>";
	}
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

	$sql2 = "SELECT id,name,age,sex,jcbw,jcrq FROM ".TABLE." ".$where." LIMIT {$offset},".PAGESIZE; 

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
					echo "<meta http-equiv='refresh' content=1;'admin.php?page={$page}'>";
				}else{
					if(unlink($dcmPath)){
						echo "源文件删除成功.";
						$res2 = mysqli_query($link,$sql2);
							if($res2 && mysqli_affected_rows($link) == 1){
								
								echo "数据删除成功";
								echo "<meta http-equiv='refresh' content=1;'admin.php?page={$page}'>";

							}else{
								echo "数据删除失败";
								echo "<meta http-equiv='refresh' content=1;'admin.php?page={$page}'>";
							}
					
				}else{
					echo "源文件删除失败";
					echo "<meta http-equiv='refresh' content=1;'admin.php?page={$page}'>";

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

	$timestamp1 = dconv($date1);
	$timestamp2 = dconv($date2);
	$where = filter($name,$timestamp1,$timestamp2);
	$tmppage = getCurrentPage();
	//总记录数
	$link = getCon();
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
	
	$start = $currentPage-4;
	if($start < 1){
		$start = 1;
	}
	$end = $currentPage+5;

	
	if($end > $totalPages){
		$end = $totalPages;
	}
	
	for($p=$start;$p<=$end;$p++){

		if($p == $currentPage){
			echo $p;
		}else{

			echo "<a href='admin.php?page={$p}'>{$p}</a>";
		}
		

	}
	echo "<br/><span>当前第".$currentPage."页/共".$totalPages."页</span><br/>";

}


 ?>