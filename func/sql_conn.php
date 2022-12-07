<?php
!defined('INIT_PHPV') && die('非法操作！请与管理员联系!');

$con = mysqli_init();
$con->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
$con->real_connect(getenv("HOST"), getenv("USERNAME"), getenv("PASSWORD"), getenv("DATABASE"));


?>
