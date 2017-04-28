
<header>
	<button role="savePage" class="fa fa-save"></button>
	<a href="?controller=pages&action=infos&id_page={$page['id_page']}" name="info_page_{$page['id_page']}" class="el el-info-circle" role="pageInfos" data-dialog ></a>

</header>
<article>
		<?php foreach ($languages as $iso=>$lang) { ?>

			<div data-lang="{$iso}" class="editor_container">
				<textarea class="ajaxeditor" data-content="{$contents[$iso]['id_content']}" name="page_{$page['id_page']}_content_{$iso}">{$contents[$iso]['html']}</textarea>
			</div>
		<?php } ?>
		

</article>