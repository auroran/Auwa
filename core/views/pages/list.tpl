<header>
	
	<button data-filter="type" data-value="1" title="Page d'accueil">
		<i class="fa fa-home"></i>
	</button>
	<button data-filter="type" data-value="0" title="Page normale">
		<i class="fa fa-file"></i>
	</button>
	<button data-filter="type" data-value="2" title="Élément de gabari">
		<i class="fa fa-file-o"></i>
	</button>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<button><i class="fa fa-search"></i></button><input type="text" class="search" data-filter="begins" data-filtername="rewrite">

	<button id="createNewPage">
		<i class="fa fa-plus"></i>
	</button>
	<select id="page_type_selector">
		<option value="0">Page simpe</option>
		<option value="1">Page accueil</option>
		<option value="2">Élément de gabaris</option>
	</select>
</header>
<article>
	<ul class="customlist adv">
		<?php foreach ($list_item as $item) {?>
			<li class="item_line" data-id="{$item['id_page']}" data-type="{$item['id_type']}" data-controller="{$item['contents'][$current_lang]['controller']}" data-enable="{$item['enable']}" data-published="{$item['enable']}" data-rewrite="{$item['contents'][$current_lang]['rewrite']}" title="{$item['contents'][$current_lang]['title']}">
				<i class="fa fa-{if $item['id_type']==1}home{else}{if $item['id_type']==0}file{else}file-o{/if}{/if} fa-4x"></i>
				<span>
						
						{$item['contents'][$current_lang]['rewrite']}
				</span>
				</li>
			</li>
		<?php } ?>
	</ul>
	<section>

		
	</section>	
</article>