(function(){
	if (Acui.debugMode) console.log('User Manager loading');
	var _module = false;
	var _ctrl = 'user';

	// edit window actions
	var editUser = function(idNode){
		Acui(idNode).setEvents('click', {
			'[role=SaveUser]':function(){
				var user = {
					id : $(this).attr('data-id')
				};
				Acui(idNode, 'fieldset > div').each(function(){
					$(this).find('input, select').each(function(){
						user[ $(this).attr('name')] = $(this).val();
					})
				})
				Acui.callCore('setUser', user, _ctrl, false, {
					success:function(r){
						Acui.notice('Modifications enregistrées', 'success');
						Acui('user').refresh();
						Acui(idNode).close();
					}
				})
			}
		})
	}

	// list windows actions
	Acui('user').ready(function(){
		this.setEvents('click', {
			'.item_line': function(){
				var idNode = 'editUser_'+$(this).attr('data-login');
				var data = {
					controller: _ctrl,
					action: 'edit',
					id_user: $(this).attr('data-id')
				}
				Acui.open(idNode, data, 'mergeHeader', function(){
					editUser(idNode);
				});
			},
			'[role=createNewUser]': function(){
				var data = {
					controller: _ctrl,
					action: 'edit',
					id_user: false
				}
				var idNode = 'createUser';
				Acui.open(idNode, data, 'mergeHeader', function(){
					editUser(idNode);
				});
			}
		})
	})


})()