<article>
	<ul>
		<li data-section="core" class="active">Auwa</li>
		<li data-section="modules">Modules</li>
	</ul>
	<section role="core">
	{if $release}
		<h1>VERSION {$release.tag_name} (actuelle : {$AuwaVersion})</h1>
		{if $release.prerelease}<div class="alert alert-warning">Cette version est une version ßéta, des bugs peuvent survenir</div>{/if}
		<div>
		{$release.body}
		</div>
		<button role="update" data-target="Auwa" name="{$release.tag_name}">Installer la mise-à-jour</button>
	{else}
		<br><div class="alert alert-success">Auwa est à jour ({$AuwaVersion})</div>
	{/if}
	</section>
	<section role="modules">
		<ul>
		{if count($m_releases>0)}
		{foreach $m_releases as $m=>$r}
			<li{if $release.prerelease} class="prerelease"{/if}>
				<button role="update" data-target="AuwaCoreModule-{$m}" name="{$r.version}">Mettre à jour</button>
				<h1>{$m}</h1><code>{$r.version}</code>
			</li>
		{/foreach}
		{else}
		<br><div class="alert alert-success">Tous les modules sont à jour</div>
		{/if}
		</ul>
	</section>
</article>
