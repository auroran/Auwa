<?php 
$transl = $config['translations'];

if (! empty($list['Folders'])) { ?>
<div class="navigator-item_header"><?php echo $transl['navigator']['folders'];?></div>
<?php } 
foreach($list['Folders'] as $d){ ?>
	<div data-dir="<?php echo $path.$d; ?>" data-item="directory" class="navigator-item">
		<span class="glyphicon glyphicon-folder-open"></span> <br> <?php echo $d; ?>
	</div>
<?php }
if (! empty($list['Documents'])) { ?>
<div class="navigator-item_header"><?php echo $transl['navigator']['files'];?></div>
<?php } 
foreach($list['Documents'] as $f){ ?>
	
	<div data-file="<?php echo $f; ?>" data-item="document" class="navigator-item <?php if($preselected==$f) echo 'selected';?>">
		<span class="glyphicon glyphicon-file"></span> <br> <?php echo $f; ?>
	</div>
<?php }
if (! empty($list['Pictures'])) { ?>
<div class="navigator-item_header"><?php echo $transl['navigator']['images'];?></div>
<?php } 
foreach($list['Pictures'] as $i){ ?>
	<div data-file="<?php echo $i; ?>" data-item="picture" class="navigator-item <?php if($preselected==$i) echo 'selected';?>">
		<span class="glyphicon glyphicon-picture"></span> <br> <?php echo $i; ?>
	</div>
<?php }
if (! empty($list['Movies'])) { ?>
<div class="navigator-item_header"><?php echo $transl['navigator']['videos'];?></div>
<?php } 
foreach($list['Movies'] as $m){ ?>
	<div data-file="<?php echo $m; ?>" data-item="movie" class="navigator-item <?php if($preselected==$m) echo 'selected';?>">
		<span class="glyphicon glyphicon-movie"></span> <br> <?php echo $m; ?>
	</div>
<?php } ?>