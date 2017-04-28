var redirect = auwa.queryCore;
auwa.queryCore += '?coreController=CoreLogin';
$('#login').click(function(){
	console.log('Log in...');
	data = {
		user: $('input[name=user]').val(),
		passwd: $('input[name=passwd]').val()
	}
	Acui.callCore('coreConnexion',data, 'CoreLogin',false, {
		success:function(r){
			window.location.replace(redirect);
		},
		fail:function(r){
			Acui.notice(r,'error');
		}
	});
})
$('#loginPanel input').on('keyup', function(e){
	if (e.keyCode==13) $('#login').trigger('click');
})
