<nav class="ACUI">
	<header>
		<?php if($fullHeader){?><span id="goCoreHome"></span><?php }?>
		<div class="appTitles">
		<?php if(isset($headerTitle)){?><h1><?php echo $headerTitle;?></h1><?php }?>
		<?php if(isset($headerTitle)){?><h2><?php echo $headerSubTitle;?></h2><?php }?>
		</div>
		<?php if(isset($headerIncl)){?>
			<?php echo $headerIncl;?>
		<?php }?>
	</header>
	<ul>
		<li class="txt">
			<div id="ajax_msg"></div>
		</li>
		<?php if(User::isCoreUser()){?>
		<?php if(!isset($_POST['controller'])){?>
		<li>
			<select id="default_lang_selector" <?php if(count($languages)<2){?> style="display:none"<?php }?>>
				<?php foreach( $languages as $iso=>$lang ){ ?>
				<option value="<?php echo $iso;?>"><?php echo $lang['name'];?></option>
				<?php }?>
			</select>
		</li>
		<?php }?>
		<?php if(!isset($hideCtrlSelector) && !isset($_POST['controller'])){?>
		<li>
			<select id="default_ctrl_selector" class="list-filter" data-filter="controller" data-init="<?php echo $currentSelectedController;?>">
				<?php if(isset($allow_all_ctrl) && $allow_all_ctrl){?>
				<option value="">Tous</option>
				<?php }?>
				<?php foreach( $controllers as $ctrl ){ ?>
				<option value="<?php echo $ctrl;?>"<?php if($ctrl==$currentSelectedController){?> selected="selected"<?php }?>><?php echo $ctrl;?></option>
				<?php }?>
			</select>
		</li>
		<?php }?>
		<?php foreach( $headerTabs as $key => $value ){ ?>
			<?php  $attr = '';?>
			<?php if(isset($value['attr'])){?>
				<?php foreach( $value['attr'] as $k=>$val ){ ?>
					<?php  $attr .= "$key = $val";?>
				<?php }?>
			<?php }?>
			<li class="btn btn-default process<?php if(isset($value['class'])){?> <?php echo $value['class'];?><?php }?><?php if(isset($value['icon'])){?> <?php echo $value['icon'];?><?php }?><?php if(isset($value['text'])){?> txt<?php }?><?php if(isset($value['smartAccess']) && $value['smartAccess']){?> smartAccess<?php }?>"<?php if(isset($value['id'])){?> id="<?php echo $value['id'];?>"<?php }?>>
			<?php if(isset($value['href'])){?>
				<a href="<?php echo $value['href'];?>"<?php if(isset($value['target'])){?> target="<?php echo $value['target'];?><?php }?>"></a>
			<?php }?>
			<?php if(isset($value['text'])){?>
				<?php echo $value['text'];?>
			<?php }?>
			</li>
		<?php }?>
		<?php }?>
	</ul>
</nav>
<?php if(User::isCoreUser()){?>
<aside id="AcuiUserAccount">
	<h1><?php echo $user->getIdentity();?></h1>
	<h2><?php if($user->getStatus() == 'CoreRoot'){?> Super Administrateur <?php }?><?php if($user->getStatus() == 'CoreUser'){?> Administrateur <?php }?><?php if($user->getStatus() == 'User'){?> Utilisateur Normal<?php }?></h2>
	<section>
		<label>Changer de mot de passe</label>
		<input id="idConnectedUser" type="hidden" value="<?php echo $user->id;?>">
		<input class="password" type="password" id="passwordConnectedUser" value="" autocomplete="off">
		<button class="btn btn-default"><i class="fa fa-check"></i></button>
	</section>
</aside>
<?php }?>