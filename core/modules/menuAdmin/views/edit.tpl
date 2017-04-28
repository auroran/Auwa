<header>	
	<button class="fa fa-save" role="saveMenu" name="{$menuName}"> 	
	<button class="fa fa-plus" role="addItem"> 	
	</button>
</header>
<input type="hidden" name="iso_lang" value="{$iso_lang}">
{if isset($res_errors)}
	{fn:$res_errors->displayErrors()}
{/if}
<article>
	<section class="menuContainer">
		<section class="customlist">
		{template: listitem, menu=$menu}
		</section>
	</section>

	<div class="menu_item_line sample">
		<i class="fa  handle"></i>
		<span class=""></span> <span role="text">{$itemSample.text[$iso_lang]}</span>
		<button class="fa fa-remove"></button>
		<button class="fa fa-edit"></button>
		<section class="customlist">
		</section>
		<code>{fn: echo json_encode($itemSample);}</code>
	</div>
</article>