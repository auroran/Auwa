<!DOCTYPE html>
<html lang="{$current_iso_code}">
	<head>
		<meta charset="utf-8">
		<base href="{$base_url}">

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="generator" content="Auwa">
		<meta name="publisher" content="{$site_publisher}">
	    {if isset($metaDescription)}<meta name="description" content="{$metaDescription}">{/if}
		
		<meta property="og:title" content="{$metaTitle}">
		<meta property="og:url" content="{url}{$current_page}">
	    {if isset($metaDescription)}<meta property="og:description" content="{$metaDescription}">{/if}
	    
	    {if isset($metaImage)}<meta property="og:image" content="{$metaImage}">{/if}

		{if isset($canonical_url)}<link rel="canonical" href="{$canonical_url}">{/if}
		
		{if isset($includeFeed)}<link rel="alternate" type="application/rss+xml"  href="{url}{$includeFeed}" title="{$metaTitle}">{/if}

		{if isset($alternate_url) && is_array($alternate_url)}
		<?php foreach($alternate_url as $iso_code=>$url){?>
		<link rel="alternate" hreflang="{$iso_code}" href="{$url}">
		<?php } ?>
		{/if}
		<link rel="icon" type="image/png" href="img/fav/{$current_ctrl}.png">
	    {$tpl_head_content}
		<title>{$metaTitle}</title>
	</head>
	<body class="{if isset($page_type)}type_{$page_type}{/if}">
	{$tpl_body_content}
		<script type="text/javascript">
			{if $_auwaJsVars}var auwa = {$_auwaJsVars};{/if}
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
	{$tpl_footer_content}
	</body>
</html>

