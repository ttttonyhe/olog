<?php
define('INIT_PHPV', true);

function p($name) {
    return $_POST[$name];
}

session_start();

if (p('check') == 'OK' && !empty(p('uname')) && !empty(p('uid')) && isset($_SESSION['logged_in_id']) && isset($_SESSION['logged_in_user']) && !empty(p('pid'))) {

    $name = p('uname');
    $id = p('uid');
    $pid = p('pid');

    require '../func/sql_conn.php';
    $ver = mysqli_query($con,'select COUNT(uid) from log_data where uname = "'.$name.'" limit 1');
    $ver = mysqli_fetch_array($ver)[0]; //判断是否存在用户内容

    if (!empty($ver)) {

        $result_1 = mysqli_query($con,'SELECT mark from log_data WHERE uname = "'.$name.'" and uid = "'.$id.'" limit 1');
        $result_1 = mysqli_fetch_array($result_1,MYSQLI_NUM); //获取已标记id字符串
        $old_id = (string)$result_1[0];
        $old_id_array = explode(',',$old_id); //转换为数组
        
        if (!in_array($pid,$old_id_array)) { //如果要标记的id不存在在已标记数组

            if ($old_id !== '') { //若不为第一次标记
                $current_id = $old_id.','.(int)$pid; //拼接成当前已标记字符串
            } else {
                $current_id = $pid; //第一次标记，直接更新内容
            }

            $result = mysqli_query($con,'UPDATE log_data SET mark = "'.$current_id.'" WHERE uname = "'.$name.'" and uid = "'.$id.'"');
            if ($result) { //更新已标记id字符串
                $array = array('stat' => 1);
            } else {
                $array = array('stat' => 0);
            }
        } else {
            $array = array('stat' => 0);
        }

    } else {

        $array = array('stat' => 0);

    }

    echo json_encode($array);


}

?>