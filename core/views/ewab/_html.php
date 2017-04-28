<?php  header("Pragma: no-cache"); $transl = $config['translations'];?>
<html>
	<head>
		<meta content="text/html; charset=UTF-8" http-equiv="content-type">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
		<!-- Bootstrap-Elusive-FontAwesome W3C Valids Frameworks -->
	    <link href="frameworks/beff/css/beff.min.css" rel="stylesheet">
	 	 <!-- ContextMenu -->
	    <link href="frameworks/ContextMenu/css/jquery.contextMenu.min.css" rel="stylesheet">
	    <!-- CodeMirror -->
	    <link href="frameworks/CodeMirror/lib/codemirror.css" rel="stylesheet">
	    <link href="frameworks/CodeMirror/theme/base16-light.css" rel="stylesheet">

		<title> E.W.A.</title>
	    <!-- Default -->
	    <link href="css/editor.css" rel="stylesheet">
	    <link href="css/navigator.css" rel="stylesheet">
	    <link href="css/cssEditor.css" rel="stylesheet">
	</head>
	<body>
		<?php include('toolsbar.php');?>
		<section id="editor-ui-main" role="container nobootstrap">
				<?php include('cssEditor.php');?>
				<p id="WAIT" style="position:absolute; top: 80px; left:0px; right:0px; text-align:center; display:none; opacity:0.2">
					<img src="img/attente.gif" style="position:relative; width:300px;margin:auto" alt="Please Wait">
				</p>
	            <textarea name="editor-ui-htmlCode" id="editor-ui-htmlCode"></textarea>
				<div class="bodyContainer col-xs-9<?php if ($config['BootstrapEnable']==true) echo " bootstrapOn" ?>">
					<div class="displayLines" id="body">
					</div>
				</div>
		</section>
		<section id="editor-ui-navigator" class="editor-ui-popup uiNode">
			<?php echo $transl['navigator']['head'];?>
			<div class="glyphicon glyphicon-remove" role="closeNode"></div>
			<div role="content" id="editor-ui-navigatorContents">	
				<nav class="navbar navbar-default col-xs-1" id="editor-ui-navigatorMenuBar">
					<ul class="btn-group" style="margin:0; padding:0">
						<li class="btn btn-default glyphicon glyphicon-arrow-up" data-dir=""></li>
						<li class="btn btn-default glyphicon glyphicon-plus">
							<div id="editor-ui-newFolderName" >
								<input type="text" value="<?php echo $transl['navigator']['newFolder'];?>" style="color:black">
								<button class="btn btn-default glyphicon glyphicon-floppy-disk" ></button>
							</div>
						</li>
					</ul>
				</nav>
				<h1><span class="glyphicons glyphicons-home"></span><div></div></h1>
				<p id="editor-ui-folders" class="col-xs-11"></p>
			</div>
			<textarea name="editor-ui-additionalHtmlCode" id="editor-ui-additionalHtmlCode"  rows="10" style="vertical-align:top;width:98.5%;display:none; ">
			</textarea>
			<nav class="navbar col-xs-12" id="btnNav">
				<ul style="margin:0; padding:0 glyphicon">
					<li>
						<input type="text" value="" name="editor-ui-nameFile" data-input="nameFile">
					</li>
					<li class="btn btn-default glyphicon glyphicon-floppy-disk btn-nav-action" id="editor-ui-validateSave">
					</li>
					<li class="btn btn-default glyphicon glyphicon-folder-open btn-nav-action" id="editor-ui-validateOpen">
					</li>
					<li class="btn btn-default glyphicon glyphicon-link btn-nav-action" id="editor-ui-validateLinkInsert">
					</li>
					<li class="btn btn-default glyphicon glyphicon-picture btn-nav-action" id="editor-ui-validateImgInsert">
					</li>
				</ul>
			</nav>
			<input type="hidden" value='' name="editor-ui-dirToWork" id="editor-ui-dirToWork">
	    </section>
	    <article id="readme">
	    	<img src="img/EWA.png" width="110" style="float:left; margin:0 2em">
			<p><?php echo $transl['readme'];?></p>
			<?php if ($config['BootstrapEnable']==true){ ?>
			<p><span style="font-size:1.1em">Cette version à été conçue pour pouvoir fonctionner avec Bootstrap : </span><br>
			un frameworks permettant de mettre en style les éléments de votre page plus facilement, 
			suivant un système de grille et de colonnage.<br>
			Cependant, à condition que vous n'utilisez que les outils de cet éditeur, votre page concervera son aspect 
			si votre site utilise la feuille de style <a href="css/bootstrap-emulator.css" target="_blank"><i>bootstrap-emulator.css</i></a>.
			</p>
			<?php } ?>
	    </article>
<?php
	echo '
	<script type="text/javascript">
	'.$json.'
	window.ewaModules = '.$js_modules.'
	window.ewaRewrite = '.$js_rewrite_rules.'
	</script>
	<input type="hidden" value="http://'.$_SERVER['HTTP_HOST'].'" name="editorURL" id="editor-ui-editorURL">
	';
?>
	</body>
<script src="js/jquery-ui.min.js"></script>
<script src="frameworks/ContextMenu/js/jquery.contextMenu.min.js"></script>
<script src="frameworks/ContextMenu/js/jquery.ui.position.min.js"></script>
<script src="frameworks/CodeMirror/lib/codemirror.js"></script>
<script src="frameworks/CodeMirror/mode/xml/xml.js"></script>
<script src="frameworks/CodeMirror/mode/javascript/javascript.js"></script>
<script src="frameworks/CodeMirror/mode/css/css.js"></script>
<script src="frameworks/CodeMirror/mode/htmlmixed/htmlmixed.js"></script>

<script src="js/ewa.js"></script>
<script src="js/htmlElements.js"></script>
<script src="js/cssEditor.js"></script>
<script src="js/uiTools.js"></script>
<script src="js/navigator.js"></script>
<script src="js/uiActions.js"></script>
<script src="frameworks/beff/js/bootstrap.min.js"></script>
</html>
