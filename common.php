<?php 
//获得文件后缀
error_reporting(E_ALL^E_NOTICE^E_WARNING);
function getext($file){
    $tmp = pathinfo($file);
    $ext = $tmp['extension'];
    $ext = strtolower($ext);
    return $ext;
}
//获取文件夹文件数目
function getfilecount($dirname){
    $dirname = trim($dirname);
    if(!file_exists($dirname) && !is_dir($dirname)){
        return false;
    }
    $dp = opendir($dirname);
    $count = 0;
    while(($file = readdir($dp)) !== false){
        if($file != '.' && $file != '..'){
            $count++;
        }
    }
    closedir($dp);
    return $count;
}
//创建储存dcm文件夹
function makedir($dcmdir){
    $dirname = trim($dirname);
if(!file_exists($dcmdir)){
    //echo "文件夹不存在，创建文件夹...<br/>";
    //$dcmdir = iconv("utf-8","gbk",$dcmdir);
    if(mkdir($dcmdir,0777,true)){
        //echo "文件夹创建成功.<br/>";

    }else{
    	echo "文件夹创建失败.<br/>";
    	return false;
    }
}
}

//重命名文件夹文件名
function rn($dcmdir,$dcmfile){
    $filenum = (int)(getfilecount($dcmdir));
    $dirname = trim($dirname);
    $dcmfile = $dcmfile.'-'.($filenum+1).'.dcm';
    $dcmfile = str_replace(' ','',$dcmfile);
    $dcmPath = $dcmdir.'/'.$dcmfile;
    $dcmPath = str_replace(' ','',$dcmPath);
    return $dcmPath;
}
//重命名数据库文件名
function rn1($dcmdir,$dcmfile1){
    $dirname = trim($dirname1);
    $filenum = (int)(getfilecount($dcmdir));
    $dcmdir1 = iconv("gb18030","utf-8",$dcmdir);
    $dcmfile1 = $dcmfile1.'-'.$filenum.'.dcm'; 
    $dcmfile1 = str_replace(' ','',$dcmfile1);
    $dcmPath1 = $dcmdir1.'/'.$dcmfile1;
    $dcmPath1 = str_replace(' ','',$dcmPath1);
    return $dcmPath1;
}
//保存文件到目录
function savefile($uploadedfile,$dcmfile,$dcmdir){
    $dirname = trim($dirname);
	if(is_uploaded_file($uploadedfile)){
    if(!file_exists($dcmfile)){
        
        if(move_uploaded_file($uploadedfile,$dcmfile)){
           
        echo '文件拷贝成功...<br/>';
    }else{
        echo '文件拷贝失败';die;
        
    }
    }else{
        echo '文件已存在<br/>';
        
    }        
    
}
}


//转换日期格式
function strtodateformate($date){
    $yyyy = substr($date,0,4);
    $mm = substr($date,4,2);
    $dd = substr($date,6,2);
    $res = $yyyy.'-'.$mm.'-'.$dd;
    return $res;
}




 ?>