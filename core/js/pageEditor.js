(function(){
	if (Acui.debugMode) console.log('Pages Manager loaded');

	var _pages_contextmenu = {
		'.customlist li':{
			'infos':{
				class: 'fa fa-info',
				text: 'Éditer les infos',
				fn: function(o,e){
					id_page = o.attr('data-id');
					var data = {
						controller: 'pages',
						auwaController: Acui.$controllerSelector.val(),
						id_page: id_page,
						id_type: $(this).attr('data-type'),
						action: 'infos'
					};
					Acui.open('info_page_'+id_page, data, {node:'pageInfos mergeHeader'} );
				}
			},
			'edit':{
				class: 'fa fa-pencil',
				text: 'Éditer la page',
				fn: function(o,e){
					o.click();
				}
			},
			'delete' : {
				class: 'fa fa-remove',
				text: 'supprimer',
				fn : function(o,e){
					id_page = o.attr('data-id');
					if (!confirm('Voulez-vous vraiment supprimer cette page ? ( id: '+id_page+')')) return;
					var callback={
						success: function(){
							Acui.notice('Page supprimée', 'success');
							Acui('pages').refresh();
						}
					};
					Acui.callCore('deletePage', {id_page: id_page}, 'pages', null, callback );
				}
			}
		}
	};
	Acui('pages').ready( function(){
		console.log('page ready');
		Acui('pages').contextMenu(_pages_contextmenu, true);
		Acui('pages', '#createNewPage').on('click', function(){
			var data = {
				controller: 'pages',
				auwaController: Acui.$controllerSelector.val(),
				id_type: parseInt($('#page_type_selector').val()),
				action: 'infos'
			};
			Acui.open('create_new_page_'+Acui.createId(), data, 'pageInfos mergeHeader');
		})
		Acui('pages', 'ul.customlist li').on('click',function(e){
			e.stopPropagation();
			var data = {
				controller: 'pages',
				auwaController: Acui.$controllerSelector.val(),
				id_page: $(this).attr('data-id'),
				id_type: $(this).attr('data-type'),
				action: 'edit'
			};
			var id_editor = 'edit_page_'+$(this).attr('data-id');
			Acui.open(id_editor, data, 'pageEditor maximized', function(){
				auwa.launchEditor("#"+id_editor+' .ajaxeditor', id_editor);
				Acui(id_editor, '[role=savePage]').on('click', function(){
					item = {};
					$('.pageEditor article').find('textarea').each(function(){
						var iso_lang = $(this).parent().attr('data-lang');
						item[iso_lang] = {
							html: tinyMCE.get( $(this).attr('id') ).getContent(),
							id_content: $(this).attr('data-content')
						};
					})
					var callback = {
						success: function(r){
							Acui.notice('Contenu enregistré','success');
						}
					}
					Acui.callCore('setHtmlContent', item, 'pages', null, callback);
				})
			});
			})
	});

	$(document).on('click', '.pageInfos [role=savePageInfos]', function(e){
		e.stopPropagation();
		var $form = $(this).parents('section');
		var current_lang =  Acui.$languageSelector.val();
		var id_node = $(this).parents('.pageInfos.uiNode').attr('id');
		var id_page =  $form.find('input[name=id_page]').val();
		var id_type =  $form.find('select[name=id_type]').val();
		var infos = {
			id_page: id_page,
			id_type: id_type,
			title: {},
			rewrite: {},
			description: {},
			controller: $form.find('input[name=controller]').val(),
			css : $form.find('input[name=css]').val(),
			js : $form.find('input[name=js]').val()
		};
		$form.find('input[data-lang], textarea[data-lang]').each(function(){
			infos[$(this).attr('name')]= $(this).val();
		});
		var callback = {
			success: function(r){
				create = id_page ? false : true;
				var msg = create ? 'Page créée':'Infos enregistrées' ;
				Acui.notice(msg,'success');
				Acui(id_node).close();
				Acui('pages').refresh();
			},
			fail: function(r){
				Acui.notice('Appel impossible', 'danger');
			}
		}
		Acui.callCore('setPageInfos', infos, 'pages', null, callback);
	});
})()