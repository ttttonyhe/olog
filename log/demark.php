<?php
define('INIT_PHPV', true);

function p($name) {
    return $_POST[$name];
}

session_start();

if (p('check') == 'OK' && !empty(p('uname')) && !empty(p('uid')) && isset($_SESSION['logged_in_id']) && isset($_SESSION['logged_in_user']) && !empty(p('pid'))) {

    $name = p('uname');
    $id = p('uid');
    $pid = p('pid'); //数据条的id

    require '../func/sql_conn.php';
    $ver = mysqli_query($con,'select COUNT(uid) from log_data where uname = "'.$name.'" limit 1');
    $ver = mysqli_fetch_array($ver)[0]; //判断是否存在用户内容

    if (!empty($ver)) {

        $result_1 = mysqli_query($con,'SELECT mark from log_data WHERE uname = "'.$name.'" and uid = "'.$id.'" limit 1'); //获取已标记的id字符串
        $result_1 = mysqli_fetch_array($result_1,MYSQLI_NUM);
        $old_id = (string)$result_1[0];
        $old_id_array = explode(',',$old_id); //分隔id字符串为数组

        $key = array_search($pid, $old_id_array); //获取对应数据条id的在已标记数组里的key
        if ($key !== false){ //如果存在
            array_splice($old_id_array, $key, 1); //删除
            $current_id = implode(',',$old_id_array); //转换为字符串
        
        
        $result = mysqli_query($con,'UPDATE log_data SET mark = "'.$current_id.'" WHERE uname = "'.$name.'" and uid = "'.$id.'"');
        if ($result) {
            $array = array('stat' => 1);
        } else {
            $array = array('stat' => 0);
        }
    }else {
            $array = array('stat' => 0);
        }
        
        }else{
            $array = array('stat' => 0);
        }

    echo json_encode($array);


}

?>