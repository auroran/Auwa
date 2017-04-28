<header>
	<button class="fa fa-save" role="saveItem"></button>
</header>
<article>
	<section role="itemEditor">
		<fieldset>
			<label data="text" data-type="lang">
				Texte
			</label>
			<div>
				<?php foreach($langs as $key=>$iso){ ?>
				<input type="text"  data-lang="{$key}" name="label" value="{if isset($item['text'][$key])}{$item['text'][$key]}{/if}" style="display:none">
				<?php } ?>
			</div>
		</fieldset>
		<fieldset>
			<label data="type">
				Type de lien
			</label>
			<div>
				<select name="linkType">
				<?php foreach ($item_type_available as $k => $i){ ?>
					<option value="{$k}"{if $k==$item['type']} selected="selected"{/if}>{$i['name']}</option>
				<?php } ?>
				</select>
			</div>
		</fieldset>
		<?php foreach ($item_type_available as $k => $i){ ?>
		<?php foreach ($i['inputs'] as $key => $input){ ?>
		<fieldset class="{$k}Link linkType" data-type="{$k}">
			<label data="{$input['data']}">
				{$input['label']}
			</label>
			<div>
				<?php switch($input['type']){
					case 'input': ?>
					<input type="text" name="{$input['name']}" placeholder="{$input['placeholder']}" value="{if isset($item[$input['data']])}{$item[$input['data']]}{/if}">
					<?php
						break;
					case 'select': ?>
						<select name="{$input['name']}">
						<?php switch($input['values']){
							case 'controllers': 
								foreach($controllers as $ctrl){ ?>
								<option value="{$ctrl}"{if (isset($item['controller']) && $item['controller']==$ctrl) || (!isset($item['controller']) && $currentSelectedController == $ctrl)} selected="selected"{/if}>{$ctrl}</option>
								<?php } 
								break;
							default:
								foreach($input['values'] as $kvalue => $optvalue){ ?>
								<option value="{$kvalue}">{$optvalue}</option>
								<?php } 
								break;
						} ?>
						</select>
					<?php
					break;
				} ?>
			</div>
		</fieldset>
		<?php } }?>
	</section>
</article>