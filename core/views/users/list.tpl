<header>
	
	<button data-filter="type" data-value="CoreRoot" title="Super Admin">
		<i class="fa fa-user-circle"></i>
	</button>
	<button data-filter="type" data-value="CoreUser" title="Admin">
		<i class="fa fa-user-circle-o"></i>
	</button>
	<button data-filter="type" data-value="Standart" title="Standart">
		<i class="fa fa-user"></i>
	</button>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<button><i class="fa fa-search"></i></button><input type="text" class="search" data-filter="begins" data-filtername="login">

	<button role="createNewUser">
		<i class="fa fa-plus"></i>
	</button>
</header>
<article>
	<ul class="customlist adv noctrl">
		{foreach $list as $item}
		<li class="item_line" data-controller="none" data-id="{$item->id}" data-type="{$item->getStatus()}" data-enable="{$item->isEnable()}" data-login="{$item->login}">
			<i class="fa fa-{if $item->getStatus()=='CoreRoot'}user-circle{else}{if $item->getStatus()=='CoreUser'}user-circle-o{else}fa-user{/if}{/if} fa-4x"></i>
			<span>
					
					{$item->login}
			</span>
			</li>
		</li>
		{/foreach}
	</ul>
</article>