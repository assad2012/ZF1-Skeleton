<?php
/* Smarty version 3.1.32, created on 2018-09-07 17:02:56
  from '/www/wwwroot/zf1.yeedev.xyz/views/templates/index_index.html' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_5b923ec019cd14_97607473',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3076b16daa7ff4dfdd4877468d7f7da39a595329' => 
    array (
      0 => '/www/wwwroot/zf1.yeedev.xyz/views/templates/index_index.html',
      1 => 1536310891,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b923ec019cd14_97607473 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $_smarty_tpl->tpl_vars['post']->value['post_title'];?>
</title>
</head>
<body>
<p>
	<?php echo $_smarty_tpl->tpl_vars['hello']->value;?>

</p>
<p>
	<?php echo $_smarty_tpl->tpl_vars['post']->value['post_title'];?>

</p>
</body>
</html><?php }
}
