<article>
	<h1>VERSION {$release.tag_name}</h1>
	{if $release.prerelease}<div class="alert alert-warning">Cette version est une version ßéta, des bugs peuvent survenir</div>{/if}
	<section>
	{$release.body}
	</section>

	<button>Installer la mise-à-jour</button>
</article>
