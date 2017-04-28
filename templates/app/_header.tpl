<nav class="ACUI">
	<header>
		{if $fullHeader}<span id="goCoreHome"></span>{/if}
		<div class="appTitles">
		{if isset($headerTitle)}<h1>{$headerTitle}</h1>{/if}
		{if isset($headerSubTitle)}<h2>{$headerSubTitle}</h2>{/if}
		</div>
		{if isset($headerIncl)}
			{$headerIncl}
		{/if}
	</header>
	<ul>
		<li class="txt">
			<div id="ajax_msg"></div>
		</li>
		{foreach $headerTabs as $key => $value}
			{fn: $attr = ''}
			{if isset($value.attr)}
				{foreach $value.attr as $k=>$val}
					{fn: $attr.="$key = $val"}
				{/foreach}
			{/if}
			<li class="btn btn-default process{if isset($value.class)} {$value.class}{/if}{if isset($value.icon)} {$value.icon}{/if}{if isset($value.text)} txt{/if}{if isset($value.smartAccess) && $value.smartAccess} smartAccess{/if} >';
			{if isset($value['href'])}
				<a href="{$value.href}"{if isset($value.target)} target="{$value.target}{/if}"></a>';
			{/if}}
			{if isset($value.text)}
				 $value.text;
			{/if}
			</li>
		{/foreach}
		{if $fullApp}
		<li>
			<select id="default_lang_selector" {if count($languages)<2} style="display:none"{/if}>
				{foreach $languages as $iso=>$lang}
				<option value="{$iso}">{$lang.name}</option>
				{/foreach}
			</select>
		</li>
		{/if}
	</ul>
</nav>
