<nav class="ACUI">
	<header>
		{if $fullHeader}<span id="goCoreHome"></span>{/if}
		<div class="appTitles">
		{if isset($headerTitle)}<h1>{$headerTitle}</h1>{/if}
		{if isset($headerTitle)}<h2>{$headerSubTitle}</h2>{/if}
		</div>
		{if isset($headerIncl)}
			{$headerIncl}
		{/if}
	</header>
	<ul>
		<li class="txt">
			<div id="ajax_msg"></div>
		</li>
		{if User::isCoreUser()}
		{if !isset($_POST['controller'])}
		<li>
			<select id="default_lang_selector" {if count($languages)<2} style="display:none"{/if}>
				{foreach $languages as $iso=>$lang}
				<option value="{$iso}">{$lang.name}</option>
				{/foreach}
			</select>
		</li>
		{/if}
		{if !isset($hideCtrlSelector) && !isset($_POST['controller'])}
		<li>
			<select id="default_ctrl_selector" class="list-filter" data-filter="controller" data-init="{$currentSelectedController}">
				{if isset($allow_all_ctrl) && $allow_all_ctrl}
				<option value="">Tous</option>
				{/if}
				{foreach $controllers as $ctrl}
				<option value="{$ctrl}"{if $ctrl==$currentSelectedController} selected="selected"{/if}>{$ctrl}</option>
				{/foreach}
			</select>
		</li>
		{/if}
		{foreach $headerTabs as $key => $value}
			{fn: $attr = ''}
			{if isset($value.attr)}
				{foreach $value.attr as $k=>$val}
					{fn: $attr .= "$key = $val"}
				{/foreach}
			{/if}
			<li class="btn btn-default process{if isset($value.class)} {$value.class}{/if}{if isset($value.icon)} {$value.icon}{/if}{if isset($value.text)} txt{/if}{if isset($value.smartAccess) && $value.smartAccess} smartAccess{/if}"{if isset($value.id)} id="{$value.id}"{/if}>
			{if isset($value['href'])}
				<a href="{$value.href}"{if isset($value.target)} target="{$value.target}{/if}"></a>
			{/if}
			{if isset($value.text)}
				{$value.text}
			{/if}
			</li>
		{/foreach}
		{/if}
	</ul>
</nav>
{if User::isCoreUser()}
<aside id="AcuiUserAccount">
	<h1>{$user->getIdentity()}</h1>
	<h2>{if $user->getStatus() == 'CoreRoot'} Super Administrateur {/if}{if $user->getStatus() == 'CoreUser'} Administrateur {/if}{if $user->getStatus() == 'User'} Utilisateur Normal{/if}</h2>
	<section>
		<label>Changer de mot de passe</label>
		<input id="idConnectedUser" type="hidden" value="{$user->id}">
		<input class="password" type="password" id="passwordConnectedUser" value="" autocomplete="off">
		<button class="btn btn-default"><i class="fa fa-check"></i></button>
	</section>
</aside>
{/if}