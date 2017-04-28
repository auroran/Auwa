<html>
	<head>
		<meta charset="utf-8">
		<base href="{$base_url}">

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="generator" content="Auwa">
		<meta name="publisher" content="{$site_publisher}">
	    {if isset($metaDescription)}<meta name="description" content="{$metaDescription}">{/if}
		
		<meta property="og:title" content="{$metaTitle}">
		<meta property="og:url" content="{url}">
	    {if isset($metaDescription)}<meta property="og:description" content="{$metaDescription}">{/if}
	    
	    {if isset($metaImage)}<meta property="og:image" content="{$metaImage}">{/if}

		{if isset($canonical_url)}<link rel="canonical" href="{$canonical_url}">{/if}
		
		{if isset($alternate_url) && is_array($alternate_url)}
		<?php foreach($alternate_url as $iso_code=>$url){?>
		<link rel="alternate" hreflang="{$iso_code}" href="{$url}">
		<?php } ?>
		{/if}
		<link rel="icon" type="image/png" href="img/fav/{$current_ctrl}.png">
	    {$tpl_head_content}
		<title>{$metaTitle}</title>
	</head>
	<body class="{$ctrlName}"{if !$fullApp} id="mainPanel"{/if}>
		{if !isset($tpl_body_content)}
		<div id="bg"></div>
		{$displayHeader}
		<input type="hidden" id="corepath" value="{fn:echo Auwa::url()}">
		<section{if $fullApp} id="mainPanel"{/if}>
			<nav class="icons">
				{template:_panel, %_CORE_DIR_%views/}
			</nav>
			{$main_content}
		</section>
		<nav id="icons" class="icons">
			{$displayPanel}
		</nav>
		<footer>
			<nav>
				<ul id="nodeTabs">
					<li class="notab"><a href="http://gregory-gaudin.com/auwa" target="_bank">Acui {$AuwaVersion}</a></li>
					<li class="notab" id="desktop"><i class="fa fa-home"></i></li>
				</ul>
			</nav>
		</footer>
		{else}
		{$tpl_body_content}
		{/if}
		{$tpl_footer_content}
		<script type="text/javascript">
			{if $_auwaJsVars}var auwa = {$_auwaJsVars};{/if}
			auwa.js = [];
			for(var i in auwa.jsFiles)
				auwa.js.push( auwa.jsFiles[i].url );
			auwa.deferLoading = function(){
				if (auwa.jsFiles.length==0) return;
				var c = function(){
					auwa.jsFiles.shift();
					return (auwa.jsFiles.length>0) ? auwa.deferLoading() : true;
				};
				var o = document.createElement("script");
				o.src = auwa.jsFiles[0].url+'.js';
				o.addEventListener('load', function (e) { c(null, e); }, false);
				document.body.appendChild(o);
			};
			if (window.addEventListener)
				window.addEventListener("load", auwa.deferLoading, false);
			else if (window.attachEvent)
				window.attachEvent("onload", auwa.deferLoading);
			else window.onload = auwa.deferLoading;
		</script>
	</body>
</html>