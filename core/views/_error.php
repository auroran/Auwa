<div style="font-family:arial; text-align:center; padding-top: 50px">
	<div style="border-radius:50%; width: 3em; padding:  .7em 0; border: 1px solid #111111; border-top-width: 28px;font-size:5em; text-align:center; line-height: .5em; margin: .5em auto; box-sizing: border-box">
		&nbsp;x&nbsp;&nbsp;&nbsp;x 
		<br>_
	</div>
	<?php if ( _DEVMODE_) { ?>
	<h1 style="color:#aa3333;text-align:center">Auwa has detected a Fatal Error - (<?php echo $type;?>)</h1>
	
	<pre style="text-align:center;width: 100%"><?php echo "$message - <br>on file $fichier line $ligne";?></pre>
	<?php } else { ?>
	
	<p style="color:#aa3333;text-align:center; font-size:4em">Something is wrong...</p>
	<?php } ?>
	<p style="text-align:center; margin: 2em; color: #224455">Please contact the webmaster to report this error
</div>