
<?php
header("content-type:text/html;charset=utf-8");
chdir(dirname(__FILE__));
$currentdir = dirname(__FILE__);
$currentdir = str_replace('\\','/',$currentdir);
require_once "../nanodicom.php";
require_once "../common.php";
require_once "../conn.php";

// This function will log a message to a file
function logger($message) {
  $now_time = date("Y-m-d G:i:s");

  $message = "[IMPORT] $now_time - $message";

  $fh = fopen("./store_server.log", 'a') or die("无法打开日志文件");
  fwrite($fh, "\n$message\n");
  fclose($fh);

  print "$message\n";

}

$dir = (isset($argv[1]) ? trim($argv[1]) : ''); // Directory our DICOM file is
$file = (isset($argv[2]) ? trim($argv[2]) : ''); // Filename of the DICOM file
//$file = iconv("utf-8","gbk",$file);
$sent_to_ae = (isset($argv[3]) ? trim($argv[3]) : ''); // AE Title the image was sent to
$sent_from_ae = (isset($argv[4]) ? trim($argv[4]) : 'tbd'); // AE Title the image was sent from

// Lets make sure we were called correctly.
if (!$file || !$dir || !$sent_to_ae || !$sent_from_ae) {
  //logger("Missing args: " . print_r($argv, true));
  exit("参数不完整");
}
$tmpfile = $dir.'/'.$file;
$tmpfile = str_replace(' ','',$tmpfile);

if (!file_exists($tmpfile)) {
  logger($tmpfile . ": 文件不存在");
  exit;
}

try{
  $dicom = Nanodicom::factory($tmpfile, 'dumper');
        
$dicom->parse();
//print_r($d->tags);
//tbd,为获取到数据，待定
// 姓名
$name = $dicom->value(0x0010, 0x0010);

$name = empty($name)?'tbd':$name;

// 检查部位
$jcbw = $dicom->value(0x0008, 0x1030);
$jcbw = empty($jcbw)?'tbd':$jcbw;
$jcbw = iconv("gbk","utf-8",$jcbw);
//检查日期
$jcrqraw = $dicom->value(0x0008, 0x0020);//20111212
$jcrqraw = empty($jcrqraw)?0:(int)$jcrqraw;
$jcrq = strtodateformate($jcrqraw);//2011-12-12
$jcrqstamp = strtotime($jcrq);
//性别
$sex = $dicom->value(0x0010,0x0040);
$sex = empty($sex)?'tbd':$sex;
//年龄
$age = $dicom->value(0x0010,0x1010);
$age = empty($age)?0:(int)$age;
// 传输语法
$ts = $dicom->value(0x0002, 0x0010);
$ts = empty($ts)?'tbd':$ts;
//上传日期
$sent_time = date("Y-m-d H:i:s",time());

// 唯一标识符
$sop = $dicom->value(0x0008, 0x0018);
$sop = empty($sop)?'tbd':$sop;
// 唯一标识符
$accession = $dicom->value(0x0008, 0x0050);
$accession = empty($accession)?'tbd':$accession;
// 唯一标识符
$suid = $dicom->value(0x0020, 0x000d);
$suid = empty($suid)?'tbd':$suid;
//唯一标识符
$seriesuid = $dicom->value(0x0020, 0x000e);
$seriesuid = empty($seriesuid)?'tbd':$seriesuid;

// 传输日志信息
logger("收到 " . $tmpfile. " 从 $sent_to_ae 到 $sent_from_ae");


$store_dir = $currentdir."/../../".'dcm_store/'.$jcrq.'/'.$name;
$store_dir = str_replace(' ','',$store_dir);
$dcm_dir = iconv("gbk","utf-8",$store_dir);
$dcm_dir = str_replace(' ','',$dcm_dir);
//$store_dir = realpath($store_dir);
if (!file_exists($store_dir)) {
  mkdir($store_dir, 0777, true);
  logger("创建文件夹：".$dcm_dir);
}

$saved_path = $store_dir.'/'.$name.'-'.$file.'.dcm';
$saved_path = str_replace(' ','',$saved_path);

//$dcmPath = iconv("gbk","utf-8",$saved_path);
if (file_exists($saved_path)) {
  logger("文件已经存在.");
  unlink($tmpfile);
  exit;
}


if(rename($tmpfile, $saved_path)){
  logger("文件拷贝成功");
}else{
  logger("文件拷贝失败");
  exit;
}
$absdir = realpath($saved_path);
$absdir = str_replace(' ','',$absdir);
$absdbdir = iconv("gbk","utf-8",$absdir);
$absdbdir = str_replace(' ','',$absdbdir);
//$dcmPath = iconv("gbk","utf-8",$saved_path);
//chmod("$store_dir/$file.dcm", 0666);
$name = iconv("gbk","utf-8",$name);

logger("保存文件到 $absdbdir");

$link = getCon();
$name = htmlspecialchars(trim($name));


$jcbw = htmlspecialchars(trim($jcbw));
$sex = htmlspecialchars(trim($sex));
$age = htmlspecialchars(trim($age));
$ts = htmlspecialchars(trim($ts));
$sop = htmlspecialchars(trim($sop));
$accession = htmlspecialchars(trim($accession));
$suid = htmlspecialchars(trim($suid));
$seriesuid = htmlspecialchars(trim($seriesuid));


$sql1 = "SELECT * FROM ".TABLE." WHERE sop='{$sop}' AND accession='{$accession}' AND suid='{$suid}' AND seriesuid='{$seriesuid}' AND aet='{$sent_from_ae}'";

$res = mysqli_query($link,$sql1);
if($res && mysqli_num_rows($res) >= 1){
  logger("数据库已存在这个文件.");
  exit();
} 

insert($link,$name,$age,$sex,$jcbw,$jcrq,$jcrqstamp,$absdbdir,$sop,$accession,$suid,$seriesuid,$sent_from_ae,$ts);
unset($dicom);
}catch(Nanodicom_Exception $e){
   echo 'File failed. '.$e->getMessage()."<br/>";
}



?>
