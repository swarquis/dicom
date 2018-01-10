<?php 
header("content-type:text/html;charset=utf-8");
error_reporting(E_ALL^E_NOTICE^E_WARNING);
define(HOST,'127.0.0.1');
define(USER,"root");
define(PASSWORD,"");
define(DATABASE,"dicom");
define(TABLE,"info");
define(PAGESIZE,5);
define(DBCHARSET,"utf8");


function getCon(){
	
	$link = mysqli_connect(HOST,USER,PASSWORD) or die("连接数据库失败: ".mysqli_connect_errno().'--'.mysqli_connect_error());
        
    mysqli_set_charset($link,'set',DBCHARSET);
    mysqli_select_db($link,DATABASE) or die("连接数据库失败: ".mysqli_connect_error($link));

	return $link;
}


function insert($link,$name,$age,$sex,$jcbw,$jcrq,$jcrqstamp,$dcmPath){
$sql = "INSERT INTO info(name,age,sex,jcbw,jcrq,jcrqstamp,dcmPath) VALUES(?,?,?,?,?,?,?)";
$stmt = mysqli_prepare($link,$sql);
mysqli_stmt_bind_param($stmt,"sisssis",$name,$age,$sex,$jcbw,$jcrq,$jcrqstamp,$dcmPath);

$exe = mysqli_stmt_execute($stmt);

if($exe && mysqli_stmt_affected_rows($stmt) == 1){
    echo "数据插入成功<br/>";
}else{
    echo "数据插入失败<br/>";
}

mysqli_stmt_close($stmt);
mysqli_close($link);
}






