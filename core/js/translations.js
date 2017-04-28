(function(){
	var lastTab = false;
	var ctrl = Acui.$controllerSelector.val();
	Acui('DefaultCoretranslations').setEvents('click',{
		'li[data-primary]': function(){
			if ($(this).attr('data-primary')==lastTab) return;
			Acui('DefaultCoretranslations', 'section[data-primary]').hide();
			Acui('DefaultCoretranslations', 'section[data-primary='+$(this).attr('data-primary')+']').show();
			lastTab = $(this).attr('data-primary');
		},
		'[role=saveTranslations]': function(){
			var translations = {};
			Acui('DefaultCoretranslations', 'section[data-primary]').each(function(){
				var primary = $(this).attr('data-primary')
				translations[ primary ] = {
					controller: $(this).attr('data-controller')!=='' ? $(this).attr('data-controller') : false,
					contents: {}
				};

				$(this).find('input[data-lang]').each(function(){
					var v = $(this).attr('name');
					if ( !translations[primary].contents[v] ) translations[primary].contents[v] = {};
					translations[ primary ].contents[v][ $(this).attr('data-lang') ] = $(this).val();
				});
			});
			console.log(translations);
			Acui.callCore('saveTranslations', {translations:translations}, 'DefaultCore', false, {
				success(r){
					Acui.notice(r, 'success');
				}
			});
		}
	});

	Acui.$controllerSelector.on('change', function(){
		Acui('DefaultCoretranslations', 'section[data-controller='+ctrl+']').hide();
		ctrl = $(this).val();
	})
})()