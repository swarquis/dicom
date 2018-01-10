<?php
	
	require_once 'get_records.php';
	session_start();
	if($_POST){

		$keyword = $_POST['keywords'];
		$_SESSION['keywords'] = $keyword;
		$_SESSION['date1'] = $_POST['date1'];
		$_SESSION['date2'] = $_POST['date2'];
		$data = showInfo($_SESSION['keywords'],$_SESSION['date1'],$_SESSION['date2']);
		$page = getCurrentPage();
	}else{
		$keyword = '';
		$data = showInfo($_SESSION['keywords'],$_SESSION['date1'],$_SESSION['date2']);
		$page = getCurrentPage();
	}
	
	
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>DR影像系统</title>
	<link rel="stylesheet" href="">
	<style type='text/css'>
		*{
			margin:0 auto;
		}
		h1{text-align:center;}
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
	<h1>DR影像系统</h1>
	<br/>
	<br/>
	<form action="#" method="post">
			请输入要搜索的病人姓名：<input type="text" name="keywords" value="<?php echo $_SESSION['keywords'] ?>">
			<br/>
			请输入要查询的日期范围:<span style="color:red">(格式2001/01/01)</span>：<input type="date" name="date1" id="" value="<?php echo $_SESSION['date1'] ?>"> 到 <input type="date" name="date2" id="" value="<?php echo $_SESSION['date2'] ?>">
			<input type="submit" value="搜索">
	</form>
	<br/>
	<br/>
	<div style="margin-left:150px;">下载源文件：鼠标右键点击病人名字另存为</div>
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

			<td><a href="get_records.php?act=download&page=<?php echo $page ?>&id=<?php echo $patient['id'] ?>"><?php echo $patient['name'] ?></a></td>

			<td><?php echo $patient['age'] ?></td>

			<td><?php echo strtolower(trim($patient['sex']))=="m"?"男":"女" ?></td>

			<td><a href="./dcmViewer/examples/wadouri/dcmViewer.php?act=view&id=<?php echo $patient['id'] ?>&page=<?php echo $page ?>"><?php echo $patient['jcbw'] ?></a></td>

			<td><?php echo $patient['jcrq'] ?></td>

			<td><a href="get_records.php?act=del&id=<?php echo $patient['id'] ?>">删除记录</a></td>
		</tr>
		<?php endforeach; ?>
	</table>
	
		<div class="page"><?php page($_SESSION['keywords'],$_SESSION['date1'],$_SESSION['date2']) ?></div>
		
		
	
</body>
</html>