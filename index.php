<?php
define('INIT_PHPV', true);
require 'func/header.php';
session_start();
?>

<div class="container index-container">
    <h1>更高效的云端笔记本</h1>
    <p>随时随地,快速清晰地记录想法</p>
<?php if(!isset($_SESSION['logged_in_user']) || !isset($_SESSION['logged_in_id'])){ ?>
<a href="/uc/login.php"><button type="button" class="btn btn-primary">登录账户</button></a>
<a href="/uc/register.php" style="margin-left:10px"><button type="button" class="btn btn-secondary">注册账户</button></a>
<?php }else{ ?>
<a href="/log"><button type="button" class="btn btn-primary">开始记录</button></a>
<?php } ?>
</div>

<?php require 'func/footer.php' ?>