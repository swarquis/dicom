<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>管理员登录</title>
	<link rel="stylesheet" href="">
	<style type="text/css">
		*{margin:0 auto;}
		h1,form{margin-top:60px;text-align:center;}
	</style>
</head>
<body>
	<h1>DR影像系统管理员登录</h1>
	<form action="admin_manage.php?act=login" method='post'>
		账号：<input type="text" name="name">
		密码：<input type="password" name="password">
		<input type="submit" value="登录">
	</form>
</body>
</html>