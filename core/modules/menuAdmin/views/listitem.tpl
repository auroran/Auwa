{foreach $menu as $k=>$item}

	<div class="menu_item_line">
		<i class="fa  handle"></i>
		<span class="{$item.icon}"></span> <span role="text">{$item.text[$iso_lang]}</span>
		<button class="fa fa-remove"></button>
		<button class="fa fa-edit"></button>
		<section class="customlist">
		{if !empty($item.menu)} 
		{template:listitem, menu=$item.menu, i=$i+1}
		{/if}
		</section>
		<code>{fn: echo json_encode($item);}</code>
	</div>
{/foreach}