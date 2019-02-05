<?php
define('INIT_PHPV', true); //调用mysql连接，禁止外部访问

function p($name) { //p函数获取POST
    return $_POST[$name];
}

session_start(); //操作SEESION

if (p('check') == 'OK' && !empty(p('uname')) && !empty(p('uid')) && isset($_SESSION['logged_in_id']) && isset($_SESSION['logged_in_user']) && !empty(p('pid'))) {

    $name = p('uname'); //用户名
    $id = p('uid'); //用户ID
    $pid = p('pid'); //要删除的内容数组key

    require '../func/sql_conn.php'; //引入mysql连接
    $ver = mysqli_query($con,'select COUNT(uid) from log_data where uname = "'.$name.'" limit 1'); //判断用户是否存在
    $ver = mysqli_fetch_array($ver)[0];

    if (!empty($ver)) { //用户存在

        $result_1 = mysqli_query($con,'SELECT log from log_data WHERE uname = "'.$name.'" and uid = "'.$id.'" limit 1'); //获取原内容
        $result_1 = mysqli_fetch_array($result_1,MYSQLI_NUM);
        $old_content = (string)$result_1[0];
        $old_content_array = explode('+-*//*-+',$old_content); //转换原内容为数组

        array_splice($old_content_array, (int)$pid, 1); //删除对应数组key的内容
        $current_content = implode('+-*//*-+',$old_content_array); //转换为字符串
        $current_content = addslashes($current_content);
        
        
        $result = mysqli_query($con,'UPDATE log_data SET log = \''.(string)$current_content.'\' WHERE uname = "'.$name.'" and uid = "'.$id.'"');
        if ($result) { //保存数据库
            $array = array('stat' => 1);
        } else {
            $array = array('stat' => 0);
        }
        
        }else{
            $array = array('stat' => 0);
        }

    echo json_encode($array);


}

?>