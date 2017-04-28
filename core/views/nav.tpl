<header>
	<button><i class="btn fa fa-arrow-up" role="dirup" data-path="{$shortPath}"></i></button>
	<button><i class="btn fa fa-plus" role="addfolder" data-path="{$shortPath}"></i></button>
	<div role="newFolderName">
		<input type="text" placeholder="Nouveau Dossier" style="color:black">
		<button class="btn fa fa-save" role="createDir"></button>
	</div>
	<button><i class="btn fa fa-download" role="upload" data-path="{$shortPath}"></i></button>
	<nav role="path" data-path="{$shortPath}">
		<span class="fa fa-home"></span>
		{foreach $path_e as $part}
			{if !empty($part)} <div></div> {$part}{/if}
		{/foreach}
	</nav>
</header>
<section role="filelist">
	{foreach $typeList as $type=>$list}
		{if !empty($list)}
		<h1 class="headerList">{$type}</h1>';
		<ul data-type="{$filetype}">
		{foreach $list as $item}
			<li data-path="{$item.path}" data-item="{$type}"{if $cutfile==$item['path']} class="cut"{/if}>
			{if isset($item.url)}<img src="{$item.url}">{/if}
			<input name="{$item.name}" disabled="disabled" value="{$item.name}">
			</li>
		{/foreach}
		</ul>
		{/if}
	{/foreach}
</section>
