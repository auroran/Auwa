				{if isset($current_panel)}
				{foreach $current_panel as $panel}
				<ul>
				{foreach $panel as $item }
					{if !isset($item.root) || ( isset($item.root) && $item.root && _Auwa_ROOT_CONNECTED_ ) && ( !isset($item.multilang) || (isset($item.multilang) && $item.multilang && count($languages)>1) )}
					<li class="{if isset($item.class)} {$item.class}{/if}{if !isset($item.a)} app{/if}" data-controller="{$item.controller}" data-module="{$item.module}"{if isset($item.action)} data-action="{$item.action}"{/if}{if isset($item.dialog) && $item.dialog} data-dialog{/if}{if isset($item.noctrl)} data-noctrl="{$item.noctrl}"{/if}>
					{if isset($item.a)}<a href="{$item.a}">{/if}
					{if isset($item.icon)}<i{if !isset($item.icontype)} class="{$item.icon}"{/if}>{if isset($item.icontype)}<img src="{url}{if isset($item.path)}{$item.path}{else}core/img/icons/{/if}{$item.icontype}/{$item.icon}.svg">{/if}</i>{/if}
					{if isset($item.text.all)}{$item.text.all}{else}{$item.text[$current_lang]}{/if}
					{if isset($item.a)}</a>{/if}</li>
					{/if}
				{/foreach}
				</ul>
				{/foreach}
				{/if}