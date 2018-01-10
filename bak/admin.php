<?php

	require_once 'admin_manage.php';
	session_start();
	if(checkuser() == false){
		$data = null;
		header("Location:admin_login.php");
	}
	if($_POST){
		$_SESSION['date1'] = $_POST['date1'];
		$_SESSION['date2'] = $_POST['date2'];	
	$data = showInfo($_SESSION['date1'],$_SESSION['date2']);
	$page = getCurrentPage();
	}else{
		$data = showInfo($_SESSION['date1'],$_SESSION['date2']);
		$page = getCurrentPage();
	}

	
	
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>查看记录</title>
	<link rel="stylesheet" href="">
	<style type='text/css'>
		*{
			margin:0 auto;
		}
		h1,.logout{text-align:center;}
		table{
			text-align:center;
		}
		.page{
			text-align:center;
		}
		.page>a{
			margin-left:20px;
			margin-right:20px;
		}
		form{
			text-align:center;
		}
	</style>
</head>
<body>
	<h1>DR记录表</h1>
	<br/>
	<br/>
	<div class="logout"><a href="admin_manage.php?act=logout">退出</a></div>
	<form action="#" method="post">
			<!-- 请输入要搜索的病人姓名：<input type="text" name="keywords">
			<input type="submit" value="搜索"> -->
			请输入要查询的日期范围<span style="color:red">(鼠标移动到文本框点击倒三角选择日期)</span>：<input type="date" name="date1" id="" value="<?php echo $_SESSION['date1'] ?>"> 到 <input type="date" name="date2" id="" value="<?php echo $_SESSION['date2'] ?>">
			<input type="submit" value="查询">

	</form>
	<br/>
	<br/>
	
	<table border='1' cellpadding='0' cellspacing='0' width='80%' bgcolor='#ABCDEF'>
		<tr>
			<td>序号</td>
			<td>姓名</td>
			<td>年龄</td>
			<td>性别</td>
			<td>检查部位</td>
			<td>检查日期</td>
			<td>操作</td>
		</tr>
		<?php $i = ($page-1)*PAGESIZE+1;foreach($data as $k=>$patient): ?>
		<tr>
			<td><?php echo $i++ ?></td>
			<td><?php echo $patient['name'] ?></td>
			<td><?php echo $patient['age'] ?></td>
			<td><?php echo $patient['sex'] ?></td>
			<td><?php echo $patient['jcbw'] ?></td>
			<td><?php echo $patient['jcrq'] ?></td>
			<td><a href="admin_manage.php?act=del&id=<?php echo $patient['id'] ?>">删除记录</a></td>
		</tr>
		<?php endforeach; ?>
	</table>
		<div class="page"><?php page($_SESSION['date1'],$_SESSION['date2']) ?></div>
		
		
	
</body>
</html>