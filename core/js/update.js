Acui.update = {
	repo: 'https://api.github.com/repos/Auroran/Auwa/releases',
	dlLog : auwa.queryCore+	'releases/update.json'
};

//add the css file
$("head").append(
	$(document.createElement("link")).attr({rel:"stylesheet", type:"text/css", href:'css/update.css?v='+Acui.session})
);
Acui.css.push('css/update.css?v='+Acui.session);

Acui.$checkUpdate = $('#updateAuwa').on('click', function(){
	if ($('#waitingUpdate').length==0 && $('#AuwaUpgrade').length==0)
		$(this).append(
			$('<i class="fa fa-spin fa-refresh" id="waitingUpdate">')
		);
	Acui.open('AuwaUpgrade', {
		'controller': 'DefaultCore',
		'action': 'checkUpdate',
	},'mergeHeader', function(){
		var $o = this;
		var $c = 'auwa';
		$('#waitingUpdate').remove();
		this.setEvents('click', {
			'article>ul>li': function(){
				$c = $(this).attr('data-section');
				Acui('AuwaUpgrade', 'article>ul>li').toggleClass('active');
				Acui('AuwaUpgrade', 'article>section').hide();
				Acui('AuwaUpgrade', 'article>section[role='+$c+']').show();
			},
			'article button[role=update]' : function(){
				var html = Acui('AuwaUpgrade', 'article>section[role='+$c+']').html();
				var target = $(this).attr('data-target');
				var release = $(this).attr('name');
				Acui('AuwaUpgrade', 'article>section[role='+$c+']').html('<img src="img/wait.gif"><h1></h1>').addClass('updating');
				var log = setInterval(function(){
					$.getJSON(Acui.update.dlLog, function(r){
						var h = r.action
						if (r.status!==false){
							h += ' : '+r.status+' %';
						}
						Acui('AuwaUpgrade', 'article>section[role='+$c+'] h1').html(h);
					});
				}, 10);
				Acui.callCore('installUpdate', {'release': release, 'target': target}, 'DefaultCore', false, {
					success: function(){
						clearInterval(log);
						setTimeout(function(){
							if (target=="Auwa"){
								Acui('AuwaUpgrade', 'article').html('<section class="uinfo"><i class="fa fa-check fa-5x"></i><h1>MAJ effectuée</h1></section>');
								setTimeout(function(){
									location.reload();
								},5000);
							} else {
								Acui('AuwaUpgrade', 'article>section[role='+$c+']').removeClass('updating').html(html);
								Acui('AuwaUpgrade', 'article>section[role='+$c+'] button[name="'+release+'"]').removeAttr('role').html('<i class="fa fa-check"></i>');
							}
						}, 15)
					},
					error: function(r){
						clearInterval(log);
						if (target=="Auwa"){
							setTimeout(function(){
								Acui('AuwaUpgrade', 'article>section[role='+$c+']').html('<section class="uinfo"><i class="fa fa-close fa-5x"></i><h1>Échec de la MAJ</h1><pre>'+r+'</pre></section>');
							}, 15)
						} else {
							Acui('AuwaUpgrade', 'article>section[role='+$c+']').removeClass('updating').html(html);
							Acui('AuwaUpgrade', 'article>section[role='+$c+'] button[name="'+release+'"]').removeAttr('role').html('<i class="fa fa-close"></i>').css('background', '#dd0000');
						}
					}
				});
			}
		})
	});
});
