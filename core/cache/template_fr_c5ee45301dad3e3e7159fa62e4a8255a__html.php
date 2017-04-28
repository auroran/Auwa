<html>
	<head>
	<base href="<?php Auwa::display_url();?>core/">
	<meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php echo $tpl_head_content;?>
	
	<title> <?php echo $app_title;?> </title>
	</head>
	<body class="core">
		<div id="bg"></div>
		<?php echo $displayHeader;?>
		<input type="hidden" id="corepath" value="<?php echo Auwa::url().'core/';?>">
		
		<?php if(!$modExec || Check::isError($modExec)){?>
		<?php } else {?>
			 <?php if(Check::isError($modExec)){?> <?php echo $modExec->displayErrors();?><?php }?>
		<?php }?>
		<?php echo $main_content;?>
		
		<footer>
			<nav>
				<ul id="nodeTabs">
					<li class="notab">© <?php echo date(trim(' Y'));?> - Auwa, <a href="http://gregory-gaudin.com/auwa" target="_blank">AuroraN</a></li>
					<?php if(!User::isCoreUser()){?>
					<li class="notab" id="lostPassword"><i class="fa fa-qestion"></i> Mot de passe oublié</li>
					<?php }?>
					<li class="notab" id="desktop"><i class="fa fa-home"></i></li>
				</ul>
			</nav>
		</footer>
		<?php echo $tpl_footer_content;?>
	</body>
</html>
