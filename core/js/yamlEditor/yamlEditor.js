if (Acui.debugMode) console.log('Yaml Editor (CodeMirror) loaded');

Acui('yamlEditor').ready(function(){
	var file = null;
	var $this = this;
	Acui.codeEditor.init( Acui(this.id, 'pre code') );
	this.setEvents('click', {
		'.smartAccess' : function(){
			file = $(this).attr('data-config')+'.yml';
			Acui.callCore('getYamlFile', {file: file}, 'yamlEditor', false,{
				success:function(yamlContent){
					if (yamlContent==null) {
						return;
					}
					Acui.codeEditor.setContent(yamlContent);
				}
			});
		},
		'[role=saveFile]': function(){
			var data = {
				file : file,
				yamlContent : Acui.codeEditor.getContent()
			}
			Acui.callCore('setYamlFile', data, 'yamlEditor', false, {
				success: function(){
					if (file == 'config/yamlEditor.yml'){
						$this.refresh();
						setTimeout(function(){
							Acui('yamlEditor', '[role=yamlFileSettings]').click();
						}, 50);
					}
					Acui.notice('Sauvegarde réussie', 'success');
				},
				fail: function(){
					Acui.notice('Erreur lors de la Sauvegarde', 'error');
				}
			});
		}
	});
});