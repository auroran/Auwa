<html>
<head>
<title><?php echo $headtitle; ?> : En maintenance</title>
</head>
<body style="font-family:arial;text-align:center;">
	<h1 style="color:#444; font-size: 3em; padding: 1.8em 0 0;"><?php echo $title; ?></h1>
	<div style="padding-top: 50px">
		<div style="border-radius:50%; width: 3em; padding:  .7em 0; border: 1px solid #111111; border-top-width: 28px;font-size:5em; text-align:center; line-height: .5em; margin: .5em auto; box-sizing: border-box; position: relative">
			&nbsp;~&nbsp;&nbsp;&nbsp;~
			<br>_
			<div style="position: absolute; top: 30px; right: -65px; font-size:.7em;  transform: rotate(-27deg); text-shadow: 3px -2px 1px #fff">
				ZzZ
			</div> 
		</div>
		<?php if (isset($is_core) && $is_core){ ?>
		<h2 style="color:#aa3333;text-align:center">Auwa est en maintenance</h2>
		<div style="color:#555">
		Le mode <cite>«Hard Maintenance»</cite> est actif.<br>
		Vous ne pouvez pas accéder au coeur via ce mode, il vous faudra modifier le fichier de configuration via le FTP.
		</div>
		<?php } else { ?>
		<h1 style="color:#aa3333;text-align:center">Votre site se refait une beauté et revient bientôt</h1>
		<?php } ?>
	</div>
</body>