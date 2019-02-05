<?php
define('INIT_PHPV', true);
require '../func/header.php';
session_start();
if(!isset($_SESSION['logged_in_user']) || !isset($_SESSION['logged_in_id'])){
    if($open){
        require 'view.php';
    }else{
?>

<script>location.href='https://log.ouorz.com/uc/login.php';</script>

<?php }}else{ ?>
<style>
body{
    background-color: #f7f7f9;
}

html{
    background-color: #f7f7f9;
}
</style>
<script>
function tobottom(){
    $('#bottom')[0].scrollIntoView();
    keydown(); //监听新增加的内容
}
</script>
    <div class="container log-container">
        
        <div class="guide-fixed">
            <h4>使用指南</h4>
            <ul style="padding: 0px;color: #666;font-size: 1rem;">
                <li>在文本框内可使用回车录入内容</li>
                <li>内容输入支持HTML(除块级元素)</li>
                <li>可使用回车录入内容</li>
                <li>右键内容段可将内容删除</li>
                <li>左键内容段可将内容标记高亮</li>
                <li>再次左键内容段可移除标记高亮</li>
                <li>左键图片内容可将图片放大查看</li>
                <li>图片上传大小不可超过2MB</li>
                <li>顶栏右上角可设置内容对外可见度</li>
            </ul>
        </div>
        
        <div id="content_stream">
        <?php
            $log = mysqli_query($con,'select log from log_data where uname = "'.$_SESSION['logged_in_user'].'" and uid = "'.$_SESSION['logged_in_id'].'" limit 1');
            $logs = mysqli_fetch_array($log,MYSQLI_NUM); //获取内容
            if(!empty($logs[0])){ //内容不为空
                
                
                
                $mark = mysqli_query($con,'SELECT mark from log_data WHERE uname = "'.$_SESSION['logged_in_user'].'" and uid = "'.$_SESSION['logged_in_id'].'" limit 1');
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
                echo '<p>Nothing Here</p><p style="color:#888"><b>注意:</b>&nbsp;你将不能对你发送的第一条内容进行任何操作,如删除或标记等</p>'; //内容为空输出占位
            }
        ?>
        </div>
        <div id="bottom"></div> <!-- 去到底部的id -->
    
    <script>
    
    var last_id = <?php echo count($content_array) - 1; ?>; //获取最后一个内容段的id
var send = function(){
    
    var uid = <?php echo $_SESSION['logged_in_id']; ?>;
    var uname = '<?php echo $_SESSION['logged_in_user']; ?>';
    var content = document.getElementById('send_content').value; //获取要发送的内容
    content = content.toString(); //转换为字符串
    
    if(content.indexOf("+-*/") >= 0 || content.indexOf("<p") >= 0 || content.indexOf("<h") >= 0 || content.indexOf("<div") >= 0){ //包含分隔内容段的特殊字符，输出非法操作
        
        $('#error_illegal').modal('show'); //显示非法操作的modal
        
    }else{
        
    if(content !== ''){ //如果要发送的内容不为空
    
    var formdata = new FormData(); //建立表单
        formdata.append('uname',uname);
        formdata.append('uid',uid);
        formdata.append('content',content);
        formdata.append('check','OK');
        
    $.ajax({
        url: 'send.php',
        type: "POST",
        data: formdata,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.stat == 1) { //发送成功
                if(data.current_content == 1){ //若已存在内容
                    $('#content_stream').html($('#content_stream').html() + '<p id="' + (last_id + 1) + '" onclick="mark(' + (last_id + 1) + ');">'+ content + '</p>'); //在现有内容后添加发送的内容
                    last_id = last_id + 1; //更新最后一段内容的id
                }else{ //原不存在内容
                    $('#content_stream').html('<p id="' + (last_id + 1) + '" onclick="mark(' + (last_id + 1) + ');">'+ content + '</p>'); //直接更新成发送的内容
                    last_id = last_id + 1; //更新最后一段内容的id
                }
                $('#send_content').val(''); //清空输入框
                tobottom(); //滑到底部
            }else{
               $('#error').modal('show'); //发送失败展示modal
               tobottom(); //滑到底部
            }
        },
        error: function(data){
            $('#error').modal('show');
            tobottom();
        }
    });
    
}else{
    $('#error_empty').modal('show');
}
}
}

var mark = function(pid){
    var uid = <?php echo $_SESSION['logged_in_id']; ?>;
    var uname = '<?php echo $_SESSION['logged_in_user']; ?>';
    var pid = pid;
    
    if(pid == 0){ //第一条内容不可操作
        $('#no_first_modal').modal('show');
    }else{
    
    var formdata = new FormData();
        formdata.append('uname',uname);
        formdata.append('uid',uid);
        formdata.append('pid',pid);
        formdata.append('check','OK');
        
    $.ajax({
        url: 'mark.php',
        type: "POST",
        data: formdata,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.stat == 1) { //标记成功
                $('#'+pid).attr({'style':'background-color:#fdbd00','onclick':'demark('+ pid +');'}); //更新标记的内容段的样式与onclick
            }else{
               $('#error_mark').modal('show');
            }
        },
        error: function(data){
            $('#error_mark').modal('show');
        }
    });
    }
}

var demark = function(pid){
    var uid = <?php echo $_SESSION['logged_in_id']; ?>;
    var uname = '<?php echo $_SESSION['logged_in_user']; ?>';
    var pid = pid;
    
    if(pid == 0){ //第一段内容不可操作
        $('#no_first_modal').modal('show');
    }else{
    
    var formdata = new FormData();
        formdata.append('uname',uname);
        formdata.append('uid',uid);
        formdata.append('pid',pid);
        formdata.append('check','OK');
        
    $.ajax({
        url: 'demark.php',
        type: "POST",
        data: formdata,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            if (data.stat == 1) {
                $('#'+pid).attr({'style':'','onclick':'mark('+ pid +');'}); //删除不标记的内容段的样式与改变onclick
            }else{
               $('#error_demark').modal('show');
            }
        },
        error: function(data){
            $('#error_demark').modal('show');
        }
    });
    }
}

var delete_do = function(pid){
    var uid = <?php echo $_SESSION['logged_in_id']; ?>;
    var uname = '<?php echo $_SESSION['logged_in_user']; ?>';
    var pid = pid;
    
    if(pid == 0 || pid == '' || pid == undefined){ //第一段内容不可操作
        $('#de_notice_modal').modal('hide');
        $('#no_first_modal').modal('show');
    }else{
    
    var formdata = new FormData();
        formdata.append('uname',uname);
        formdata.append('uid',uid);
        formdata.append('pid',pid);
        formdata.append('check','OK');
        
    $.ajax({
        url: 'delete.php',
        type: "POST",
        data: formdata,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            $('#de_notice_modal').modal('hide');
            if (data.stat == 1) { //删除成功
                $('#'+pid).attr('style','display:none'); //停止展示删除的内容段
            }else{
               $('#error_delete').modal('show'); //删除失败
            }
        },
        error: function(data){
            $('#de_notice_modal').modal('hide');
            $('#error_delete').modal('show');
        }
    });
    }
}


var upload = function(){
    $('#upload_modal').modal('show');
}

var upload_do = function(){
    var uid = <?php echo $_SESSION['logged_in_id']; ?>;
    var uname = '<?php echo $_SESSION['logged_in_user']; ?>';
    
    if($("#upload_img")[0].files[0] !== undefined){
        
    var formdata = new FormData();
        formdata.append('uname',uname);
        formdata.append('uid',uid);
        formdata.append('file', $("#upload_img")[0].files[0]); //在表单中加入选择的图片文件
        formdata.append('check','OK');
        
    $.ajax({
        url: 'upload.php',
        type: "POST",
        data: formdata,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            $('#upload_modal').modal('hide');
            if (data.stat == 1 && data.img_src !== '') { //上传成功
                if(data.current_content == 1){ //已存在其他内容
                    $('#content_stream').html($('#content_stream').html() + '<p id="' + (last_id + 1) + '" onclick="mark(' + (last_id + 1) + ');"><img src="' + data.img_src + '"></p>');
                    last_id = last_id + 1;
                }else{ //图片作为第一条内容
                    $('#content_stream').html('<p id="' + (last_id + 1) + '" onclick="mark(' + (last_id + 1) + ');"><img src="' + data.img_src + '"></p>');
                    last_id = last_id + 1;
                }
                $("#upload_img")[0].files[0] = undefined; //清空选择的文件
                tobottom();
            }else{ //上传失败
               $('#error_upload').modal('show');
               $("#upload_img")[0].files[0] = undefined;
               tobottom();
            }
        },
        error: function(data){
            $('#error_upload').modal('show');
            $("#upload_img")[0].files[0] = undefined;
            tobottom();
        }
    });
}else{
    $('#error_upload_empty').modal('show');
}
}


var set_view = function(status){
    var uid = <?php echo $_SESSION['logged_in_id']; ?>;
    var uname = '<?php echo $_SESSION['logged_in_user']; ?>';
    
    if(status == <?php echo $open; ?>){
        
        $('#error_set_view').modal('show');
        
    }else{
    
    var formdata = new FormData();
        formdata.append('uname',uname);
        formdata.append('uid',uid);
        formdata.append('status',status);
        formdata.append('check','OK');
        
    $.ajax({
        url: 'set_view.php',
        type: "POST",
        data: formdata,
        cache: false,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            console.log('success');
            if (data.stat == 1) {
                $('#set_refresh').modal('show');
                setTimeout('location.reload();',1000);
            }else{
               $('#error_set_view').modal('show');
            }
        },
        error: function(data){
            $('#error_set_view').modal('show');
        }
    });
    }
}



    </script>
    
    
    
    
    
    <div class="input-group mb-3 log-send" id="log-send-div">
        <input type="text" class="form-control log-send-input" placeholder="可使用回车录入内容,支持HTML(除块级元素)" id="send_content">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" onclick="upload();">图片</button>
            <button class="btn btn-outline-primary log-send-btn" type="button" onclick="send();" id="btn-text">录入</button>
        </div>
    </div>
        
        
        
        
        
    </div>  
</div>

<div class="modal fade" id="error" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">出现错误</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>发送失败，请重试或稍后再试</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_delete" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">出现错误</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>删除失败，请重试或稍后再试</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="error_mark" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">出现错误</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>标记失败，请重试或稍后再试</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_demark" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">出现错误</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>清除标记失败，请重试或稍后再试</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_empty" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">无内容</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>内容不可为空，请尝试输入一些文字</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_upload_empty" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">无图片内容</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>图片不可为空，请尝试选择一张图片</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_upload" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">上传出错</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>上传失败，请重试或稍后再试</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="upload_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">选择上传图片</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <input type="file" class="form-control-file" id="upload_img" name="upload_img" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" onclick="upload_do();">上传</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="de_notice_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">删除字段确认</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>你确定要删除此字段吗？</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
        <button type="button" class="btn btn-primary" id="de_btn">确认</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_illegal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">内容错误</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>你输入的内容包含非法词汇如「 +-*/ 」或「 ' 」或HTML块级元素(如:div、h1~h6、address、blockquote、center、dir、dl、dt、dd、fieldset、form、hr、isindex、menu、noframes、noscript、ol、p、pre、table、ul ...)，请删除后重试</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="no_first_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">操作错误</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>不可对第一段内容进行任何操作</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="error_set_view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">操作错误</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>内容可见度设置失败，请重试或稍后再试</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">知道了</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="set_refresh" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">正在设置中</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p>正在为你设置内容可见度，请稍候...</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade bd-example-modal-lg" id="view_img" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <img src="" style="width:100%;height:auto" id="img_view">
    </div>
  </div>
</div>

    <script>
    
    
    //输入框监听回车发送
    $("#send_content").bind("keydown", function(e) {
        // 兼容FF和IE和Opera    
        var theEvent = e || window.event;
        var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
        if (code == 13){
            send();
        }
    });
    
    function keydown(){
    $("p").mousedown(function(e) {
        if (3 == e.which) {
            $('#de_btn').attr('onclick','delete_do('+ $(this).eq(0).attr('id') +');');
            $('#de_notice_modal').modal('show');
        }
    })
    $("img").click(function(e) {
        $('#img_view').attr('src',$(this).eq(0).attr('src'));
        $('#view_img').modal('show');
    });
    }
    
    keydown();
    
    $("#send_content").focus(function(){
        tobottom();
    });
    
    $("#btn-text").click(function(){
        $("#send_content").focus();
　　});
    </script>

<?php
require '../func/footer.php';
}
?>