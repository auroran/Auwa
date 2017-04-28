Acui.update = {
	repo: 'https://api.github.com/repos/Auroran/Auwa/releases',
	dlLog : auwa.queryCore+	'releases/update.json'
};

Acui.$checkUpdate = $('#updateAuwa').on('click', function(){
	$.getJSON(Acui.update.repo, function(r){
		if (r.length==0) return Acui.notice('Auwa semble à jour', 'success');
		var release = r[0];
		Acui.callCore('checkUpdate', {'version': release.tag_name}, 'DefaultCore', false, {
			success: function(u){
				Acui.notice( u ? 'Auwa est à jour' : 'Une mise à jour est disponible' , u ? 'success' : 'warning');
				if (u) return;
				Acui.open('AuwaUpgrade', {
					'controller': 'DefaultCore',
					'action': 'upgradeAuwa',
					'release' : release
				},'mergeHeader', function(){
					var $o = this;
					this.setEvents('click', {
						'article button' : function(){
							Acui('AuwaUpgrade', 'article').html('<img src="img/wait.gif"> <h1>Téléchargement de l\'archive</h1>').css({
								'background':'#3d4049',
								'color': '#fff'
							});
							Acui.callCore('downloadUpdate', {'release': release}, 'DefaultCore', false, {
								success: function(){
									var log = setInterval(function(){
										$.getJSON(Acui.update.dlLog, function(r){
											var h = r.action
											if (r.status!==false){
												h += ' : '+r.status+' %';
											}
											Acui('AuwaUpgrade', 'article h1').html(h);
										});
									}, 10);

									Acui.callCore('installUpdate', {'release': release}, 'DefaultCore', false, {
										success: function(){
											clearInterval(log);
											setTimeout(function(){
												Acui('AuwaUpgrade', 'article').html('<section><i class="fa fa-check fa-5x"></i><h1>MAJ effectuée</h1></section>');
												setTimeout(function(){
													location.reload();
												},5000);
											}, 15)
										},
										error: function(r){
											clearInterval(log);
											setTimeout(function(){
												Acui('AuwaUpgrade', 'article').html('<section><i class="fa fa-close fa-5x"></i><h1>Échec de la MAJ</h1><pre>'+r+'</pre></section>');
												setTimeout(function(){
													location.reload();
												},5000);
											}, 15)
										}
									});
								}
							});
						}
					})
				});
			}
		})
	});
});
