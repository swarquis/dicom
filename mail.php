<?php 
mail("receiver@qq.com","subject","content","sender@qq.com\r\n");
$link = ldap_connect($host,$port) or die;
ladp_set_option($link,LADP_OPT_PROTOCOL_VERSION,3);
ldap_start_tls($link);
ldap_bind($link,$name,$pwd);
ldap_unbind($link);

session.save_handler
session_start();
session_destroy();
session_unset();
session_encode();

 ?>