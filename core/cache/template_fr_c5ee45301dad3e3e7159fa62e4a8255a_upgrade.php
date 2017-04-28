<article>
	<h1>VERSION <?php echo $release['tag_name'];?></h1>
	<?php if($release['prerelease']){?><div class="alert alert-warning">Cette version est une version ßéta, des bugs peuvent survenir</div><?php }?>
	<section>
	<?php echo $release['body'];?>
	</section>

	<button>Installer la mise-à-jour</button>
</article>
