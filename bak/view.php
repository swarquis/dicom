<?php 
include_once 'view_image.php';
$data = getdata();

 ?>
 <!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <title></title>
     <link rel="stylesheet" href="">
     <style type="text/css">
     	*{margin:0 auto;}
     	table{text-align:center;}
     </style>
     <script type="text/javascript"></script>
     <script type="text/javascript" src="./jquery-3.2.1.js"></script>
      
 </head>
 <body>

 	<div class="top" style="height:50px">文件名：<?php echo $tmpimg ?> &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;     图片尺寸选择：<input id="normal" type="button" value="还原">
     
     <input id="bigsize" type="button" value="查看大图">
     
     <input id="originsize" type="button" value="原图大小尺寸查看"> <a href="view_records.php?page=<?php echo $page ?>" style="color:red;float:right;font-weight:bold;font-size:30px">返回主页面</a></div>
 	<div class='pic' style="text-align:center">
     <img id="pic" style="width:500px" src="./tmp/<?php echo $tmpimg ?>" alt=""/>

     </div>
	<div class="info">
		<table border='1' cellspacing="0" cellpadding="0" bgcolor='#abcdef' width="60%">
			<tr>
				<td>姓名</td>
				<td>性别</td>
				<td>年龄</td>
				<td>检查日期</td>
				<td>检查部位</td>
				
			</tr>
			<tr>
				<td><?php echo $data['name'] ?></td>
				<td><?php echo strtolower($patient['sex'])=="m"?"男":"女" ?></td>
				<td><?php echo $data['age'] ?></td>
				<td><?php echo $data['jcrq'] ?></td>
				<td><?php echo $data['jcbw'] ?></td>
				
			</tr>
		</table>
	</div>
     
     <br/>
     <br/>
     <br/>
     <br/>
     <br/>
 </body>
 <script>
      	
	 	$(function(){
	 		$("#originsize").click(function(){
	 			var img = document.getElementById("pic");
	 			$("#pic").css("width","");
	 		});
	 		$("#bigsize").click(function(){
	 			var img = document.getElementById("pic");
	 			$("#pic").css("width","1000px");
	 		});
	 		$("#normal").click(function(){
	 			var img = document.getElementById("pic");
	 			$("#pic").css("width","500px");
	 		});
	 	})
 	</script>

 </html>