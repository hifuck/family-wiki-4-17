<?php
/* Smarty version 3.1.31, created on 2018-04-03 14:51:57
  from "/home/jiang/projects/family-wiki/App/Views/Index/index.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac3248d362638_33524795',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2d9c3920517342ec3c64cc357c7638265e7be881' => 
    array (
      0 => '/home/jiang/projects/family-wiki/App/Views/Index/index.html',
      1 => 1522738287,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../Include/header.html' => 1,
    'file:../Include/footer.html' => 1,
  ),
),false)) {
function content_5ac3248d362638_33524795 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender('file:../Include/header.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="main-wrapper">
    <div class="layout">
        <div style="padding: 24px 48px;">
            <h1>:)</h1>
            <p>欢迎使用
                <b> <?php echo $_smarty_tpl->tpl_vars['user']->value;?>
</b>
            </p>
            <br/>
        </div>
        <div style="padding: 24px 48px;">
            <h1>:)</h1>
            <p>欢迎使用
                <b> <?php echo $_smarty_tpl->tpl_vars['user']->value;?>
</b>
            </p>
            <br/>
        </div>
    </div>
</div>
<?php $_smarty_tpl->_subTemplateRender('file:../Include/footer.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
