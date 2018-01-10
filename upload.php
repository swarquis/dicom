<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>上传文件</title>
	<link rel="stylesheet" href="">
	<style type='text/css'>
		*{margin:0 auto;}
		h1{text-align:center;margin-top:50px;}
		table{margin-top:100px;background-color:#abcdef; text-align:center;width:500px; height:200px;}
	</style>
</head>
<body>
	<h1>dcm文件上传页面,请上传dcm格式文件</h1>
<form action="server.php" method='post' enctype='multipart/form-data'>
	
	<table border='1' cellspacing="0" cellpadding="0">
		<tr>
			<td>选择文件</td>
			<td><input type="file" name="dcm[]"></td>
		</tr>
		<tr>
			<td>选择文件</td>
			<td><input type="file" name="dcm[]"></td>
		</tr>
		<tr>
			<td>选择文件</td>
			<td><input type="file" name="dcm[]"></td>
		</tr>
		<tr>
			<td>选择文件</td>
			<td><input type="file" name="dcm[]"></td>
		</tr>
		<tr>
			<td>选择文件</td>
			<td><input type="file" name="dcm[]"></td>
		</tr>
		<tr>
			<td colspan='2'><input type="submit" value="上传图片"></td>
		</tr>
	</table>

</form>
	
</body>
</html>