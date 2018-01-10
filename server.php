
<?php  

header("content-type:text/html;charset=utf-8");
require_once './common.php';
require_once './conn.php';
require_once './nanodicom.php';



$input = $_FILES['dcm'];

//var_dump($input['type']);die;
$count = 0;
foreach($input['error'] as $err){
if($err == 0){
    $count++;
    //echo '文件上传成功.<br/>';
    //echo '上传了'.$count.'个文件...<br/>';
}else if($err == 1 || $err == 2){
    echo '文件太大.';
}else if($err == 3){
    echo '文件未完整上传.';
}else if ($err == 4 || $err == 5){
    continue;
}
}
echo "总共上传".$count."个文件<br/>";

foreach($input['tmp_name'] as $k=>$uploadedfile){

try
    {
        $tmpfile = $input['name'][$k];
        $ext = getext($tmpfile);
        if(empty($uploadedfile)){continue;}
        if($input['type'][$k] !== "application/octet-stream" || $ext !== 'dcm'){
            echo "第".($k+1)."个文件名为 ".$tmpfile." 的文件格式错误，跳过...<br/>";
            continue;
            }
        $dicom = Nanodicom::factory($uploadedfile, 'dumper');
        
        $dicom->parse();
        $tmp = $dicom->value(0x0010, 0x0010);
        $name = trim(iconv("gbk", "utf-8", $tmp));
        $tmp1 = $dicom->value(0x0010, 0x1010);
        $age = empty($tmp1)?'tbd':$tmp1;
        $tmp2 = $dicom->value(0x0010, 0x0040);
        $sex = empty($tmp2)?'tbd':trim($tmp2);
        //检查部位
        $tmp3 = $dicom->value(0x0008, 0x1030);

        $jcbw = empty($tmp3)?'tbd':trim(iconv("gbk","utf-8",$tmp3));
        /*$tmp3 = $dicom->value(0x0008, 0x0015);
        $jcbw = empty($tmp3)?'tbd':$tmp3;*/
        //检查日期
        $tmp4 = $dicom->value(0x0008, 0x0020);

        $jcrq = empty($tmp4)?'tbd':$tmp4;
        $jcrqstamp = 0;
        if($jcrq !== 'tbd'){
            $jcrq = strtodateformate($jcrq);
            $jcrqstamp = strtotime($jcrq);
        }
        $date = date("Y-m-d",time());
        $dcmdir = 'D:/'.$date.'/'.$tmp;
        $dcmdir = str_replace(" ",'',$dcmdir);
        $dcmdir1 = 'D:/'.$date.'/'.$name;
        $dcmdir1 = str_replace(" ",'',$dcmdir1);
        /*$dcmfile = $dcmdir.'/'.$tmp.'-'.$k.'.dcm';
        $dcmfile1 = $dcmdir1.'/'.$name.'-'.$k.'.dcm';*/
        $dcmfile = $tmp;
        $dcmfile1 = $name;
        makedir($dcmdir);
        $dcmPath = rn($dcmdir,$dcmfile);
        savefile($uploadedfile,$dcmPath,$dcmdir);

        $link = getCon();
        $dcmPath1 = rn1($dcmdir,$dcmfile1);
        insert($link,$name,$age,$sex,$jcbw,$jcrq,$jcrqstamp,$dcmPath1);
        
        unset($dicom);
       
        
    }
    catch (Nanodicom_Exception $e)
    {
        echo 'File failed. '.$e->getMessage()."<br/>";
    }

}




?>  
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <link rel="stylesheet" href="">
</head>
<body>
    <div>
        <!-- <content><?php //echo str_replace("\n","<br/>",$content);?></content> -->
    </div>
    
    <br/>
    <br/>
    <a href="http://localhost/dicom/upload.php">返回上传页继续上传</a>
</body>
</html>