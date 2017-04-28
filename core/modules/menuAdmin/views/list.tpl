<header>	
	<button class="fa fa-plus" role="addLink"> 	
	</button>
</header>
<article>
	<ul id="menu_items" class="customlist">
		<?php foreach ($list_item as $name=>$item) {?>
			<li data-u="{$baseLink}&action=edit&menu={$name}" data-dialog role="menuEditor" name="edit_menu_{$name}">
				<i class="fa fa-reorder fa-4x"></i><br>
				<div>
					{$name}
				</div>
			</li>
		<?php } ?>
	</ul>
</article>
