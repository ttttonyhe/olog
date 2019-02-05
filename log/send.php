<?php
define('INIT_PHPV', true);

function p($name) {
    return $_POST[$name];
}

    session_start();
    
    if(p('check') == 'OK' && !empty(p('uname')) && !empty(p('uid')) && isset($_SESSION['logged_in_id']) && isset($_SESSION['logged_in_user']) && !empty(p('content'))){
        
        $name = p('uname');
        $id = p('uid');
        $content = p('content');
        $content = addslashes($content);
        
        require '../func/sql_conn.php';
        $ver = mysqli_query($con,'select COUNT(uid) from log_data where uname = "'.$name.'" limit 1');
        $ver = mysqli_fetch_array($ver)[0]; //判断用户是否已提交过内容
        
        if(!empty($ver)){ //提交过
            
            $result_1 = mysqli_query($con,'SELECT log from log_data WHERE uname = "'.$name.'" and uid = "'.$id.'" limit 1');
            $result_1 = mysqli_fetch_array($result_1,MYSQLI_NUM); //获取原内容
            $old_content = (string)$result_1[0];
            $current_content = addslashes($old_content).'+-*//*-+'.(string)$content; //拼接字符串
            
            $result = mysqli_query($con,'UPDATE log_data SET log = \''.(string)$current_content.'\' WHERE uname = "'.$name.'" and uid = "'.$id.'"');
            if($result){ //更新内容，可能存在图片src=""，使用了双引号，故log =''，'需要使用转义符
                $array = array('stat'=>1,'current_content'=>1);
            }else{
                $array = array('stat'=>0);
            }

        }else{ //未提交过
            
            $result = mysqli_query($con,'INSERT into log_data (log,view,uid,uname) values("'.(string)$content.'","1","'.(int)$id.'","'.(string)$name.'")');
            if($result){ //插入内容
                $array = array('stat'=>1);
            }else{
                $array = array('stat'=>0);
            }
            
        }
        
        echo json_encode($array);


}

?>