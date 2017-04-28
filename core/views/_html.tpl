<html>
	<head>
	<base href="{url}core/">
	<meta content="text/html; charset=UTF-8" http-equiv="content-type">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {$tpl_head_content}
	
	<title> {$app_title} </title>
	</head>
	<body class="core">
		<div id="bg"></div>
		{$displayHeader}
		<input type="hidden" id="corepath" value="{fn:echo Auwa::url().'core/'}">
		
		{if !$modExec || Check::isError($modExec)}
		{else}
			 {if Check::isError($modExec)} {$modExec->displayErrors()}{/if}
		{/if}
		{$main_content}
		
		<footer>
			<nav>
				<ul id="nodeTabs">
					<li class="notab">© {date: Y} - Auwa, <a href="http://gregory-gaudin.com/auwa" target="_blank">AuroraN</a></li>
					{if !User::isCoreUser()}
					<li class="notab" id="lostPassword"><i class="fa fa-qestion"></i> Mot de passe oublié</li>
					{/if}
					<li class="notab" id="desktop"><i class="fa fa-home"></i></li>
				</ul>
			</nav>
		</footer>
		{$tpl_footer_content}
	</body>
</html>
