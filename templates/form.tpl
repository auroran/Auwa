
<section style="padding: .5em">
	{if isset($sendStatus)}
		{if $sendStatus==true}
		<p class="alert alert-success">Votre message a bien été envoyé</p>
		{else}
		<p class="alert alert-danger">Une erreur est survenue durant l'envoi de votre message</p>
		{/if}
	{else}
	{if isset($form_errors) }
		{$form_errors}
	{/if}	<form id="contactForm" method="post" action="{url}query.php" enctype="multipart/form-data">
		<input type="hidden" name="callRequested" value="true">
		<input type="hidden" name="cu" value="{$current_page}">
		<fieldset name="identity">
			<label class="required">
				Identité
			</label>
			<input type="text" name="form_identity" required=""{submitted:form_identity} value="{formValue:form_identity}"{/submitted}>
		</fieldset>
		<fieldset name="pseudo">
			<div> Si vous voyez ce champs ne le modifiez pas, Turin vous prendrait pour un être mécanique !</div>
			<label>
				Pseudo
			</label>
			<input type="text" name="form_username" value="">
		</fieldset>
		<fieldset name="site">
			<label>
				Site/Entreprise
			</label>
			<input type="text" name="form_site"{submitted:form_siteZ} value="{formValue:form_site}"{/submitted}>
		</fieldset>
		<fieldset name="mail">
			<label class="required">
				E-mail
			</label>
			<input type="text" name="form_mail"{submitted:form_mail} value="{formValue:form_mail}"{/submitted}>
		</fieldset>
		<fieldset name="subject">
			<label class="required">
				Sujet
			</label>
			<select name="form_subject">
				<?php foreach($mail_subjects as $s){ ?>
					<option value="{$s['value']}">{$s['text'][$current_lang]}</option>
				<?php } ?>
			</select>
		</fieldset>
		<fieldset name="mail">
			<label>
				Pièce jointe<br>(<i>PDF, jpg, png)</i><br>Taille max. : 4Mo
			</label>
			<input type="file" name="form_file">
		</fieldset>
		<fieldset name="message">
			<label class="required">
				Message
			</label>
			<textarea name="form_msg" placeholder="Votre message ici">{submitted:form_msg}{formValue:form_msg}{/submitted}</textarea>
		</fieldset>
		<fieldset>
			<label>
			</label>
			<button type="submit"><span class="fa fa-envelope"> </span> Envoyer</button>
		</fieldset>
	</form>
	{/if}
</section>