
<?php

chdir(dirname(__FILE__));
$dir = __DIR__;
$dir = str_replace('\\','/',$dir);
$tmpdir = $dir.'/temp';

require_once('../class_dicom.php');

$storescp_cmd = TOOLKIT_DIR . "/storescp -v -dhl -td 20 -ta 20 --fork " . // Be verbose, set timeouts, fork into multiple processes
  "-xf ./storescp.cfg Default " . // 配置文件
  "-od ".$tmpdir. // 保存目录
  " -xcr \"D:/xampp1/php/php.exe ./handler.php #p #f #c #a\" " .
  //"-xcr \" ./import.php \"#p\" \"#f\" \"#c\" \"#a\"\" " . // Run this script with these args after image reception
  "1104 "; // Listen on this port

		/*#p: 保存目录
       · #f: 文件名
       · #a: aec
       · #c: aet*/
system($storescp_cmd);

?>

