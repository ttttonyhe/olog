<?php
define('INIT_PHPV', true);

function p($name) {
    return $_POST[$name];
}

session_start();

if (p('check') == 'OK' && !empty(p('uname')) && !empty(p('uid')) && isset($_SESSION['logged_in_id']) && isset($_SESSION['logged_in_user'])) {

    $name = p('uname');
    $id = p('uid');
    
    
    function getsuffix($file) {
        $suffixarray = explode('.',$file);
        $suffixarray = array_reverse($suffixarray);
        return $suffixarray [0];
    }

    /* 处理图片上传 */
    $file_type = $_FILES["file"]["type"];
    $file_error = $_FILES["file"]["error"];
    $file_name = $_FILES["file"]["name"];
    $file_suff = getsuffix($file_name);
    $file_name = time().rand(0,20).'.'.$file_suff;
    $file_size = $_FILES["file"]["size"];

    if ((($file_type == "image/png") || ($file_type == "image/gif") || ($file_type == "image/jpeg") || ($file_type == "image/pjpeg")) && ($file_size < 2097152)) {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if ($_FILES["file"]["error"] > 0) {
            $msg = '上传出现意想不到的错误';
            $status = false;
        } else {
            if (file_exists($root."/upload/".$file_name)) {
                $msg = '图片已存在,请重新选择上传';
                $status = false;
            } else {
                $upload = move_uploaded_file($_FILES["file"]["tmp_name"],$root."/upload/".$file_name);
                if ($upload) {
                    $file_url = 'https://log.ouorz.com/upload/'.$file_name;
                    $status = true;
                } else {
                    $status = false;
                }
            }
        }
    } else {
        $msg = '图片类型不支持或图片超过2MB';
        $status = false;
    }
    
    
    
    if($status){

    require '../func/sql_conn.php';
    $ver = mysqli_query($con,'select COUNT(uid) from log_data where uname = "'.$name.'" limit 1');
    $ver = mysqli_fetch_array($ver)[0]; //判断是否提交过内容

    if (!empty($ver)) { //提交过

        $result_1 = mysqli_query($con,'SELECT log from log_data WHERE uname = "'.$name.'" and uid = "'.$id.'" limit 1');
        $result_1 = mysqli_fetch_array($result_1,MYSQLI_NUM);
        $old_content = (string)$result_1[0];
        $current_content = addslashes($old_content).'+-*//*-+'.'<img src="'.$file_url.'">'; //拼接内容字符串上img标签

        $result = mysqli_query($con,'UPDATE log_data SET log = \''.(string)$current_content.'\' WHERE uname = "'.$name.'" and uid = "'.$id.'"');
        if ($result) { //注意内容需使用转移符，因为图片存在双引号
            $array = array('stat' => 1,'current_content' => 1,'img_src'=>$file_url); //返回图片链接与更新符
        } else {
            $array = array('stat' => 0,'msg'=>3);
        }

    } else { //图片为第一段内容

        $result = mysqli_query($con,'INSERT into log_data (log,view,uid,uname) values(\'<img src="'.$file_url.'">\',"1","'.(int)$id.'","'.(string)$name.'")');
        if ($result) {
            $array = array('stat' => 1,'img_src'=>$file_url);
        } else {
            $array = array('stat' => 0,'msg'=>1);
        }

    }
    }else{
        $array = array('stat' => 0,'msg'=>2);
    }

    echo json_encode($array);


}

?>