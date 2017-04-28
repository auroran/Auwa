		<!-- HEADER -->
		<header class="{$containerType}">
			<nav>
				<ul class="lang">
					<li class="lang_FR"><a href="{url}fr/{if _CURRENT_RULE_}{fn:echo _CURRENT_RULE_}/{/if}{$current_page}"><img src="{url}/img/flags/FR.png" alt="Français"></a></li>
					<li class="lang_EN"><a href="{url}en/{if _CURRENT_RULE_}{fn:echo _CURRENT_RULE_}/{/if}{$current_page}"><img src="{url}/img/flags/EN.png" alt="English"</a></li>
				</ul>
			</nav>
			<h1 id="SiteTitle">
				<a href="http://<?php echo _BASE_URL_;?>">{$site_title}</a>
			</h1>
			{if !isset($metaTitle) || empty($metaTitle)}{page:header}{else}<h2>{$metaTitle}</h2>{/if}
			<nav>
					<ul>
						{@menu = Menu::getMenu('Main')}
						{foreach $menu as $key => $t1}
							{if !empty( trim($t1.text[_CURRENT_LANG_]) )}
			                <li>
							{if isset( $t1.link)} <a href="http://{%_BASE_URL_%}{$t1.link}">{/if}
							{$t1.text[_CURRENT_LANG_]}
							{if isset( $t1.link)}</a>{/if}
							</li>
							{/if}
						{/foreach}
					</ul>
			</nav>	
		</header>
		{if isset($tpl_errors) }  {fn:$tpl_errors->displayErrors()}{/if}
		<!-- CONTENEUR PRINCIPAL -->
		<div id="main" class="{$containerType} level1">

			<!-- ZONE PRINCIPALE -->
			{$tpl_main_content}

		</div>
					
		<!-- FOOTER -->
		{Hook:displayBeforeFooter}
		<footer class="<?php echo $containerType;?>">
			{page:footer}
			{Hook:displayFooter}
		</footer>
