<?php 
!defined('INIT_PHPV') && die('非法操作！请与管理员联系!');
session_start();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title>OLog | 高效云端笔记本</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit">
        <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="https://static.ouorz.com/jquery.min.js"></script>
        <link rel="shortcut icon" href="https://static.ouorz.com/bitbug_favicon%20%281%29.ico">
        <link rel="stylesheet" href="/func/style.css">
    </head>
    <body>
<nav class="navbar navbar-expand-lg navbar-light header-div">
  <a class="navbar-brand logo" href="https://log.ouorz.com">OLog</a>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item <?php if(!strstr($_SERVER["REQUEST_URI"],'log')) echo 'active'; ?>">
        <a class="nav-link" href="https://log.ouorz.com">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-ite <?php if(strstr($_SERVER["REQUEST_URI"],'log')) echo 'active'; ?>">
        <a class="nav-link" href="https://log.ouorz.com/log">Logs</a>
      </li>
    </ul>
    <?php if(!isset($_SESSION['logged_in_user']) || !isset($_SESSION['logged_in_id'])){ ?>
        <?php 
            if(strstr($_SERVER["REQUEST_URI"],'log') && !empty($_GET['view'])){
                require '../func/sql_conn.php'; //连接数据库
                $opens = mysqli_query($con,'select uopen,uname from user_data where uid = "'.(int)$_GET['view'].'" limit 1');
                $opens = mysqli_fetch_array($opens,MYSQLI_NUM);
                $open = (int)$opens[0]; //获取开放状态
                $author_name = (string)$opens[1]; //获取用户名
            }
        ?>
        
        
        <a class="nav-link" href="/uc/login.php">登录</a>
        <a class="nav-link" href="/uc/register.php" style="color: #ed412d;">快速注册</a>
        
        
    <?php }else{ ?>
    
    <?php 
        if(strstr($_SERVER["REQUEST_URI"],'log') && isset($_SESSION['logged_in_user']) && isset($_SESSION['logged_in_id'])){
            require '../func/sql_conn.php'; //连接数据库
            $opens = mysqli_query($con,'select uopen from user_data where uname = "'.$_SESSION['logged_in_user'].'" and uid = "'.$_SESSION['logged_in_id'].'" limit 1');
            $open = (int)mysqli_fetch_array($opens,MYSQLI_NUM)[0]; //获取开放状态
            
            if($open){
                $view_btn = 'success';
                $onclick_func = 'set_view(0);';
                $view_text_1 = '公开访问';
                $view_text_2 = '私有内容';
            }else{
                $view_btn = 'info';
                $onclick_func = 'set_view(1);';
                $view_text_2 = '公开访问';
                $view_text_1 = '私有内容';
            }
    ?>
    <?php if($open){ ?>
        <a style="margin-right: 15px;color: #999;text-decoration: none;letter-spacing: .2px;" href="https://log.ouorz.com/log?view=<?php echo $_SESSION['logged_in_id']; ?>">https://log.ouorz.com/log?view=<?php echo $_SESSION['logged_in_id']; ?></a>
    <?php } ?>
    <div class="btn-group">
    <button type="button" class="btn btn-<?php echo $view_btn; ?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px 12px !important;"><?php echo $view_text_1; ?></button>
    <div class="dropdown-menu">
        <a class="dropdown-item" onclick="<?php echo $onclick_func; ?>" href="#"><?php echo $view_text_2; ?></a>
    </div>
    </div>
    <?php } ?>
    
        <a class="nav-link" href="/uc" style="font-size: 1.4rem;"><button type="button" class="btn btn-primary" style="padding: 3px 12px !important;text-transform: uppercase;"><?php echo $_SESSION['logged_in_user']; ?></button></a>
        <a class="nav-link" href="/uc/login.php?action=logout" style="padding: 0px;color: #999;letter-spacing: -.2px;">登出</a>
    <?php } ?>
  </div>
</nav>