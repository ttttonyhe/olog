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

if (p('check') == 'OK' && !empty(p('user_name')) && !empty(p('user_email')) && !empty(p('user_passwd'))) {
        
        if((int)p('ver_code') == (int)p('code')){ //判断验证码
        
        $name = input(htmlspecialchars((string)p('user_name')));
        $email = input(p('user_email'));
        $passwd = input(p('user_passwd'));
        
        require '../func/sql_conn.php';
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error .= '邮箱地址不正确<br/>';
        }
        if(strlen($passwd) < 6){
            $error .= '密码长度必须大于6个字符<br/>';
        }
        $check_query = mysqli_query($con,"select count(uid) from user_data where uname='".$name."' limit 1");
        if(!empty((int)mysqli_fetch_array($check_query)[0])){ //判断用户名是否存在
            $error .= '用户名 '.$name.' 已存在<br/>';
        }
        
        if($error == ''){
            
        $uid = mysqli_query($con,'select COUNT(uid) from user_data');
        $uid = mysqli_fetch_array($uid)[0]; //获取已存在用户数量
        $uid = (int)$uid + 1; //当前id为总数量加1
        
        //写入数据
        $passwd = md5(md5($passwd).md5($passwd)); //双重md5加密
        $regdate = time();
        $sql = "INSERT INTO user_data (uid,uname,upasswd,uemail,uregdate) VALUES ('$uid','$name','$passwd','$email',
    $regdate)"; //插入数据
        if (mysqli_query($con,$sql)){
            $pass = '欢迎加入OLog | 点击此处 <a href="/uc/login.php">登录</a>';
        } else {
            $error .= '注册失败，系统错误<br />';
        }

}
}else{
    $error .= '验证码输入错误';
}

}




require '../func/header.php';
?>


<div class="container">
    <form class="input-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);
        ?>">
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
            <label for="inputEmail3" class="col-sm-2 col-form-label">用户名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="inputName" name="user_name" placeholder="Name">
                <small class="text-muted">
                    将用于登录
                </small>
            </div>
        </div>
        
        <div class="form-group row">
            <label for="inputEmail3" class="col-sm-2 col-form-label">邮箱</label>
            <div class="col-sm-10">
                <input type="email" class="form-control" id="inputEmail" name="user_email" placeholder="Email">
            </div>
        </div>

        <div class="form-group row">
            <label for="inputPassword3" class="col-sm-2 col-form-label">密码</label>
            <div class="col-sm-10">
                <input type="password" class="form-control" id="inputPassword" name="user_passwd" placeholder="Password">
                <small class="text-muted">
                    请输入不少于6位字符
                </small>
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
                <input type="hidden" name="code" value="<?php echo $ver_code; ?>">
                <button type="submit" class="btn btn-primary" style="padding: 5px 50px;">注册账户</button>
                <a href="/uc/login.php"><button type="button" class="btn btn-secondary" style="padding: 5px 50px;">登录</button></a>
            </div>
        </div>

    </form>
</div>


<?php require '../func/footer.php' ?>