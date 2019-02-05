<?php
define('INIT_PHPV', true);

function p($name) {
    return $_POST[$name];
}

session_start();

if (p('check') == 'OK' && !empty(p('uname')) && !empty(p('uid')) && isset($_SESSION['logged_in_id']) && isset($_SESSION['logged_in_user'])) {

    $name = p('uname');
    $id = p('uid');
    $st = (int)p('status');

    require '../func/sql_conn.php';
    $ver = mysqli_query($con,'select COUNT(uid) from user_data where uname = "'.$name.'" limit 1');
    $ver = mysqli_fetch_array($ver)[0]; //判断是否存在用户内容

    if (!empty($ver)) {

        $result_1 = mysqli_query($con,'SELECT uopen from user_data WHERE uname = "'.$name.'" and uid = "'.$id.'" limit 1');
        $result_1 = mysqli_fetch_array($result_1,MYSQLI_NUM); //获取已标记id字符串
        $old_st = (int)$result_1[0];
        
        if ($st !== $old_st){ //如果要标记的id不存在在已标记数组

            $result = mysqli_query($con,'UPDATE user_data SET uopen = "'.$st.'" WHERE uname = "'.$name.'" and uid = "'.$id.'"');
            if ($result) { //更新已标记id字符串
                $array = array('stat' => 1);
            } else {
                $array = array('stat' => 0,'msg'=>1);
            }
        } else {
            $array = array('stat' => 0,'msg'=>2);
        }

    } else {

        $array = array('stat' => 0,'msg'=>3);

    }

    echo json_encode($array);


}

?>