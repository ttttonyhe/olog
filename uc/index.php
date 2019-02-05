<?php
define('INIT_PHPV', true);
require '../func/header.php';
session_start();
?>

<div class="container">
<div class="card text-center" style="width: 50%;margin:10vh auto;">
  <div class="card-header" style="text-transform: uppercase;">
    欢迎，<?php echo $_SESSION['logged_in_user']; ?>
  </div>
  <div class="card-body" style="padding: 50px 0;">
    <h5 class="card-title" style="font-size: 2rem;">今天想写些什么呢？</h5>
    <p class="card-text" style="color: #777;">使用OLog，你可以在任何时间任何地点快速创作，记录所思<br>简单快捷，快速响应，不错过每一个新的想法</p>
    <a href="https://log.ouorz.com/log" class="btn btn-primary" style="padding: 5px 20px;">开始书写</a>
  </div>
  <div class="card-footer text-muted">
    © 2019 OLog | Made with <svg width="18" height="18" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: -3px;"> <path fill="none" stroke="#ee561d" stroke-width="1.03" d="M10,4 C10,4 8.1,2 5.74,2 C3.38,2 1,3.55 1,6.73 C1,8.84 2.67,10.44 2.67,10.44 L10,18 L17.33,10.44 C17.33,10.44 19,8.84 19,6.73 C19,3.55 16.62,2 14.26,2 C11.9,2 10,4 10,4 L10,4 Z"></path></svg> by <a href="https://www.ouorz.com" target="_blank" style="color:#7c7c7c">TonyHe</a>
  </div>
</div>
</div>
<?php
require '../func/footer.php';
?>