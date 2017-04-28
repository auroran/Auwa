<header>
	{foreach $headerTabs as $tab}
	{if isset($tab.class) && $tab.class=='separator'}&nbsp;&nbsp;&nbsp;
	{else}<button{if isset($tab.smartAccess)} class="smartAccess"{/if}{if isset($tab.attr)}{foreach $tab.attr as $attr=>$value} {$attr}="{$value}"{/foreach}{/if}>{/if}
		<i class="fa fa-{$tab.icon}"></i>
	{if !isset($tab.class) || (isset($tab.class) && $tab.class!='separator')}</button>{/if}
	{/foreach}
</header>
<article id="YamlEditor">
	<pre>
		<code class="yaml">
		</code>
	</pre>
</article>