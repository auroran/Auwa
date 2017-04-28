<header>
	<button role="saveTranslations"><i class="fa fa-save"></i></button>
</header>
<article>
	<ul class="customlist adv">
		{foreach $config as $category => $value}
		<li data-controller="{if $value['controller']}{$value['controller']}{else}none{/if}" data-primary="{$category}">
			<i>
				<img src="img/icons/apps/eog.svg">
			</i>
			<span>{if !$value['controller']}<i>{/if}{fn: echo str_replace('Z_'.$value['controller'],'',$category)}{if !$value['controller']}</i>{/if}</span>
		</li>
		{/foreach}
	</ul>
	{foreach $config as $nameC=>$category}
	<section data-primary="{$nameC}" data-controller="{$category['controller']}">
		{foreach $category['contents'] as $var=>$values}
		<fieldset>
			<label>{$values[$iso_ref]}</label>
			<div>
				{foreach $languages as $iso=>$l}
				<input name="{$var}" type="text" data-lang="{$iso}" value="{if isset($values[$iso])}{$values[$iso]}{/if}">
				{/foreach}
			</div>
		</fieldset>
		{/foreach}
	</section>
	{/foreach}
</article>