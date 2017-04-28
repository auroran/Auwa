<div id="ewa_toolBar">
<!-- ce menu est lié a uiTools.js -->
<ul class="btn-group">
	<li class="btn btn-default i fa" data-title="<?php echo $transl['bubbles']['htmlMode'];?>"><span class="fa fa-code"></span></li>
	<li class="btn btn-default i fa" style="display:none"><span class="fa fa-pencil-square-o"></span></li>
	<li id="displayLines" class="btn btn-default fa i fa-eye" data-title="<?php echo $transl['bubbles']['hideLines'];?>"></li>
</ul>
<!-- ces menus sont liés a uiActions.js -->
<ul class="btn-group dropdown" id="structureEditor" role="insertElement">
	<li class="btn btn-default fa fa-leaf i" role="insert" data-tag="span" data-title="<?php echo $transl['bubbles']['simpleFormat'];?>"></li>
	<li class="btn btn-default fa fa-pencil i dropdown-toggle" id="menu1" data-toggle="dropdown" data-title="<?php echo $transl['bubbles']['menuElements1'];?>"></li>
	<ul class="dropdown-menu" role="menu"><?php if ($config['BootstrapEnable']==true){ ?>
		<ul class="divider t" data-title="<?php echo $transl['html']['head']['bootstrap'];?>">
			<li role="insert" id="insert_row" data-tag="div" data-class="clearfix row">Ligne</li>
			<li role="insert" id="insert_col" data-tag="div" data-class="clearfix col-md-5">Colonne</li>
		</ul><?php }
		if ($config['BootstrapEnable']==true){ ?>
		<ul class="divider t" data-title="<?php echo $transl['html']['head']['pBootstrap'];?>">
			<li role="insert" id="insert_P" data-tag="p" data-class="col-md-12">Paragraphe / Cellule</li>
		</ul><?php } ?>
		<ul class="divider t" data-title="<?php echo $transl['html']['head']['zone'];?>">
			<li role="insert" data-tag="span">Zone de texte simple</li>
			<li role="insert" data-tag="label">Étiquette</li>
			<li role="insert" data-tag="abbr">Acronyme / Abréviation</li>
			<li role="insert" data-tag="cite">Citation</li>
			<li role="insert" data-tag="dfn">Définition</li>
			<li role="insert" data-tag="code">Code </li>
			<li role="insert" data-tag="var">Variable mathématique</li>
		</ul>
		<ul class="divider t" data-title="<?php echo $transl['html']['head']['list'];?>">
			<li role="insert" id="insert_UL" data-tag="ul">Liste</li>
			<li role="insert" id="insert_OL" data-tag="ol">Liste ordonnée</li>
			<li role="insert" id="insert_LI" data-tag="li">Élement de liste</li>
		</ul>
	</ul>
</ul>
<ul class="btn-group dropdown" id="elementsEditor" role="insertElement">
	<li class="btn btn-default fa i dropdown-toggle" id="menu1" data-toggle="dropdown" data-title="<?php echo $transl['bubbles']['menuElements2'];?>"><span class="fa fa-paragraph "></span></li>
	<ul class="dropdown-menu" role="menu">
		<ul class="divider t" data-title="<?php echo $transl['html']['head']['p'];?>">
			<li role="insert" id="insert_P" data-tag="p"><?php echo $transl['html']['p'];?></li>
		</ul>
		<ul class="divider t" data-title="<?php echo $transl['html']['head']['bloc'];?>">
			<li role="insert" id="insert_div" data-tag="div"><?php echo $transl['html']['div'];?></li>
			<li role="insert" id="insert_article" data-tag="article"><?php echo $transl['html']['article'];?></li>
			<li role="insert" id="insert_section" data-tag="section"><?php echo $transl['html']['section'];?></li>
			<li role="insert" id="insert_TAG" data-tag="aside"><?php echo $transl['html']['aside'];?></li>
			<li role="insert" id="insert_TAG" data-tag="nav"><?php echo $transl['html']['nav'];?></li>
			<li role="insert" id="insert_TAG" data-tag="pre"><?php echo $transl['html']['pre'];?></li>
		</ul>
		<ul class="divider t" data-title="<?php echo $transl['html']['head']['hf'];?>">
			<li role="insert" data-tag="header"><?php echo $transl['html']['header'];?></li>
			<li role="insert" data-tag="footer"><?php echo $transl['html']['footer'];?></li>
		</ul>
		<ul class="divider t" data-title="Titres">
			<li role="insert" id="insert_H1" data-tag="h1"><?php echo $transl['html']['h'];?> 1</li>
			<li role="insert" id="insert_H2" data-tag="h2"><?php echo $transl['html']['h'];?> 2</li>
			<li role="insert" id="insert_H3" data-tag="h3"><?php echo $transl['html']['h'];?> 3</li>
			<li role="insert" id="insert_H4" data-tag="h4"><?php echo $transl['html']['h'];?> 4</li>
			<li role="insert" id="insert_H5" data-tag="h5"><?php echo $transl['html']['h'];?> 5</li>
			<li role="insert" id="insert_H6" data-tag="h6"><?php echo $transl['html']['h'];?> 6</li>
		</ul>
	</ul>
</ul>

<ul class="btn-group">
	<li role="insertObj" data-obj="ul" class="btn btn-default i fa" data-title="<?php echo $transl['bubbles']['listUL'];?>"><span class="fa fa-list-ul"></span></li>
	<li role="insertObj" data-obj="ol" class="btn btn-default i fa" data-title="<?php echo $transl['bubbles']['listOL'];?>"><span class="fa fa-list-ol"></span></li>
</ul>
<!-- ce menu est lié a uiActions.js -->
<ul class="btn-group">
	<li role="insertObj" data-obj='table' class="btn btn-default i fa fa-th" data-title="<?php echo $transl['bubbles']['table'];?>"></li>
	<li role="insertObj" data-obj='link' class="btn btn-default fa i fa-link" data-title="<?php echo $transl['bubbles']['link'];?>"></li>
	<li role="insertObj" data-obj='picture' class="btn btn-default fa i fa-image" data-title="<?php echo $transl['bubbles']['image'];?>"></li>
	<li role="insertObj" data-obj='video' class="btn btn-default fa fa-play-circle" style="color:#aaaaaa" data-title="<?php echo $transl['bubbles']['video'];?>"></li>	

</ul>
<ul class="btn-group dropdown">
	<li class="btn btn-default i fa" id="editor-ui-menuInserObj" data-toggle="dropdown" data-title="<?php echo $transl['bubbles']['modules'];?>"><span class="fa fa-puzzle-piece"></span></li>
	<ul class="dropdown-menu " role="menu" id="editor-ui-addonsInsertion" style="width: 80px; text-align:center">
	</ul>
</ul>
</div>