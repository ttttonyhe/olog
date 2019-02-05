<?php
define('INIT_PHPV', true);
require '../func/header.php';
session_start();
?>
<style>
body{
    background-color: #f7f7f9;
}

html{
    background-color: #f7f7f9;
}
</style>
    <p style="text-align: center;color: #888;font-size: 1.4rem;letter-spacing: .5px;margin: 20px 0;">你正在浏览由 <?php echo $author_name; ?> 创建的公开内容</p>
    <div class="container log-container" style="padding-bottom: 10vh;margin-bottom: 20px;">
        
        <div id="content_stream">
        <?php
            $name = mysqli_query($con,'select uname from user_data where uid = "'.$_GET['view'].'" limit 1');
            $name = mysqli_fetch_array($name,MYSQLI_NUM)[0]; //获取内容
            $id = $_GET['view'];
            
            $log = mysqli_query($con,'select log from log_data where uname = "'.$name.'" and uid = "'.$id.'" limit 1');
            $logs = mysqli_fetch_array($log,MYSQLI_NUM); //获取内容
            if(!empty($logs[0])){ //内容不为空
                
                
                
                $mark = mysqli_query($con,'SELECT mark from log_data WHERE uname = "'.$name.'" and uid = "'.$id.'" limit 1');
                $mark = mysqli_fetch_array($mark,MYSQLI_NUM); //获取已标记的id字符串
                $mark = (string)$mark[0];
                $mark_array = explode(',',$mark); //转换已标记id字符串为数组
                
                
                $content_array = explode('+-*//*-+',$logs[0]); //分隔内容字符串为数组
                
                if(count($content_array) !== 0){ //内容大于1条
                    for($i=0;$i<count($content_array);$i++){ //循环输出内容段
                        if($i == 0){
                            echo '<p id="'.$i.'">'.$content_array[$i].'</p>'; //第一段内容不可进行任何操作
                        }elseif(in_array($i,$mark_array) && $i !== 0){ //如果当前段被标记
                            echo '<p id="'.$i.'" onclick="demark('.$i.');" style="background-color:#fdbd00">'.$content_array[$i].'</p>'; //输出带背景色内容段
                        }else{
                            echo '<p id="'.$i.'" onclick="mark('.$i.');">'.$content_array[$i].'</p>'; //未被标记输出带标记onclick的内容段
                        }
                    }
                }else{ //内容只有1条
                    echo $logs[0]; //直接输出
                }
                
            }else{
                echo '<p>Nothing Here</p><p style="color:#888">';
            }
        ?>
        </div>
        </div>
        <p style="text-align: center;color: #999;font-size: 1.4rem;font-weight: 300;margin-bottom: 5vh;letter-spacing: 5px;">自豪地使用 OLog 创作</p>
<?php
require '../func/footer.php';
?>