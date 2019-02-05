
<?php
    define('INIT_PHPV', true);

function p($name) {
    return $_POST[$name];
}

function input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
    $error = '';
    $pass = '';
    $ver_code = rand(1000,2555);
    session_start();

if(p('check') == 'OK' && !empty(p('user_name')) && !empty(p('user_passwd')) && !isset($_SESSION['logged_in_id'])){
        
        if((int)p('ver_code') == (int)p('code')){
        
        $name = input(p('user_name'));
        $passwd = input((string)p('user_passwd'));
        $passwd = md5(md5($passwd).md5($passwd)); //双重md5加密

        if(strlen($passwd) < 6){
            $error .= '密码长度必须大于6个字符<br/>';
        }

        
        if($error == ''){
            
        require '../func/sql_conn.php';
        $uver = mysqli_query($con,'select COUNT(uid) from user_data where uname = "'.$name.'" and upasswd = "'.$passwd.'" limit 1');
        $uver = mysqli_fetch_array($uver)[0]; //判断用户是否存在
        
        if(!empty($uver)){ //存在用户
            
            $uid = mysqli_query($con,'select uid from user_data where uname = "'.$name.'" and upasswd = "'.$passwd.'" limit 1');
            $uid = mysqli_fetch_array($uid,MYSQLI_NUM); //获取用户名
            
            session_start(); //设置用户SEESION登录
            $_SESSION['logged_in_user'] = $name;
            $_SESSION['logged_in_id'] = (int)$uid[0];
            
            $pass = '欢迎登录OLog<script>location.href="https://log.ouorz.com/log"</script>';

        }else{ //用户不存在
            
            $error .= '信息错误，请检查用户名或密码';
            
        }

}
}else{
    $error .= '验证码输入错误';
}

}

if($_GET['action'] == 'logout' && p('check') !== 'OK'){ //未提交表单，直接访问logout页面
    
    if(isset($_SESSION['logged_in_id'])){ //删除SESSION
        unset($_SESSION['logged_in_id']);
        $pass = '用户'.$_SESSION['logged_in_user'].'已登出';
        unset($_SESSION['logged_in_user']);
    }else{
        echo '<script>location.href="https://log.ouorz.com/uc/login.php";</script>'; //未登录
    }
    
}
    
    
    require '../func/header.php';
?>

<?php if(!isset($_SESSION['logged_in_user']) || !isset($_SESSION['logged_in_id'])){ //未登录 ?>
        <div class="container">
        <form class="input-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>">
            
            <?php if ($error !== '') { ?>
            <div class="alert alert-danger" role="alert">
                <p style="margin:0px"><?php echo $error; ?></p>
            </div>
        <?php } ?>
        <?php if ($pass !== '') { ?>
            <div class="alert alert-success" role="alert">
                <p style="margin:0px"><?php echo $pass; ?></p>
            </div>
        <?php } ?>
            
  <div class="form-group row">
    <label for="inputName3" class="col-sm-2 col-form-label">用户名</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="inputName" name="user_name" placeholder="Name">
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword3" class="col-sm-2 col-form-label">密码</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="inputPassword" name="user_passwd" placeholder="Password">
    </div>
  </div>
  
    <div class="form-group row">
            <label for="inputPassword3" class="col-sm-2 col-form-label">验证码</label>
            <div class="col-sm-10">
                <input type="password" id="inputVerify" class="form-control" name="ver_code" placeholder="0000">
                <small class="text-muted">
                    请输入验证码 : <?php echo $ver_code;?>
                </small>
            </div>
        </div>

  <div class="form-group row">
    <div class="col-sm-10" style="margin:20px auto;text-align:center">
        <input type="hidden" name="check" value="OK">
        <input type="hidden" name="code" value="<?php echo $ver_code; ?>"> <!-- 真验证码提交 -->
        <button type="submit" class="btn btn-primary" style="padding: 5px 50px;">登入账户</button>
        <a href="/uc/register.php"><button type="button" class="btn btn-secondary" style="padding: 5px 50px;">注册</button></a>
    </div>
  </div>
</form>
</div>
        
        
<?php require '../func/footer.php'; }else{ //已登录 ?>
<script>location.href="https://log.ouorz.com/log"</script>
<?php } ?>