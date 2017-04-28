				<?php if(isset($current_panel)){?>
				<?php foreach( $current_panel as $panel ){ ?>
				<ul>
				<?php foreach( $panel as $item  ){ ?>
					<?php if(!isset($item['root']) || ( isset($item['root']) && $item['root'] && _Auwa_ROOT_CONNECTED_ ) && ( !isset($item['multilang']) || (isset($item['multilang']) && $item['multilang'] && count($languages)>1) )){?>
					<li class="<?php if(isset($item['class'])){?> <?php echo $item['class'];?><?php }?><?php if(!isset($item['a'])){?> app<?php }?>" data-controller="<?php echo $item['controller'];?>" data-module="<?php echo $item['module'];?>"<?php if(isset($item['action'])){?> data-action="<?php echo $item['action'];?>"<?php }?><?php if(isset($item['dialog']) && $item['dialog']){?> data-dialog<?php }?><?php if(isset($item['noctrl'])){?> data-noctrl="<?php echo $item['noctrl'];?>"<?php }?>>
					<?php if(isset($item['a'])){?><a href="<?php echo $item['a'];?>"><?php }?>
					<?php if(isset($item['icon'])){?><i<?php if(!isset($item['icontype'])){?> class="<?php echo $item['icon'];?>"<?php }?>><?php if(isset($item['icontype'])){?><img src="<?php Auwa::display_url();?><?php if(isset($item['path'])){?><?php echo $item['path'];?><?php } else {?>core/img/icons/<?php }?><?php echo $item['icontype'];?>/<?php echo $item['icon'];?>.svg"><?php }?></i><?php }?>
					<?php if(isset($item['text']['all'])){?><?php echo $item['text']['all'];?><?php } else {?><?php echo $item['text'][$current_lang];?><?php }?>
					<?php if(isset($item['a'])){?></a><?php }?></li>
					<?php }?>
				<?php }?>
				</ul>
				<?php }?>
				<?php }?>