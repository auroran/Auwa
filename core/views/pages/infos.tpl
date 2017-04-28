<header>
	<button role="savePageInfos" class="fa fa-save"></button>
</header>
<article>

	<section>
		<input type="hidden" name="controller" value="{$currentSelectedController}">
		<input type="hidden" name="id_page" value="{$page['id_page']}">
		<fieldset>
			<label>
				Titre
			</label>
			<div>
				<?php foreach ($languages as $iso=>$lang) { ?>

				<input type="text" name="title_{$iso}" role="title" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso]['title']}{/if}" style="display:none">
				<?php } ?>

			</div>
		</fieldset>
		<fieldset>
			<?php foreach ($languages as $iso=>$lang) { ?>
			<input type="hidden" name="init_rewrite_{$iso}" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso]['rewrite']}{/if}">
			<?php } ?>
			<label>
				Nom "web" simple
			</label>
			<div>
				<?php foreach ($languages as $iso=>$lang) { ?>

				<input type="text" name="rewrite_{$iso}" data-lang="{$iso}" value="{if isset($contents[$iso])}{$contents[$iso]['rewrite']}{/if}" style="display:none">
				<?php } ?>
			</div>
		</fieldset>
		<fieldset>
			<label>
				Description
			</label>
			<div>
				<?php foreach ($languages as $iso=>$lang) { ?>

				<textarea name="description_{$iso}" data-lang="{$iso}" style="height:100px;max-height:100px;display:none">{if isset($contents[$iso])}{$contents[$iso]['description']}{/if}</textarea>
				<?php } ?>

			</div>
		</fieldset>
		<fieldset>
			<label>CSS Additionnel
			</label>
			<div>
				<input type="text" name="css" value="{$page['css']}">
			</div>
		</fieldset>
		<fieldset>
			<label>Script Js Additionnel
			</label>
			<div>
				<input type="text" name="js" value="{$page['js']}">
			</div>
		</fieldset>
		<fieldset>
			<label>Type de page
			</label>
			<div>
				<select name="id_type">
					<option value="0"{if $page['id_type']==0} selected="selected"{/if}>Page simple</option>
					<option value="1"{if $page['id_type']==1} selected="selected"{/if}>Page d'accueil</option>
					<option value="2"{if $page['id_type']==2} selected="selected"{/if}>Élement de gabari</option>
				</select>
			</div>
		</fieldset>
		<fieldset>
			<label>Contrôleur
			</label>
			<div>
				<select name="controller">
				<?php foreach ($controllers as $ctrl) { ?>

					<option value="{$ctrl}"{if $ctrl==$controller} selected="selected"{/if}>{$ctrl}</option>
				<?php } 
				?>
				</select>
			</div>
		</fieldset>
	</section>
</article>