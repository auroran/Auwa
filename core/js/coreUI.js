// ========== ACUI FUNCTIONS ==========
if (typeof auwa == 'undefined') auwa={};

var Acui = function(id, selector){
	if (!selector) {
		if (Acui.base[id]==undefined)  Acui.base[id] = new Acui.nodeUI(id, false);
		return Acui.base[id];
	}
	return (typeof Acui.base[id] !=='undefined' && typeof Acui.base[id].$content !== 'undefined') ? Acui.base[id].$content.find(selector) : $('<div>');
}

Acui.debugMode = true;
Acui.language = 'fr';

// create and return a random ID
Acui.createId= function(){
	var r='';
	var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
	for (var i = 0; i < 6; i++)
		r += chars[Math.floor(Math.random() * chars.length)];
	return r;
}

Acui.session = Acui.createId();
Acui.$languageSelector = $('#default_lang_selector').change(function(){ Acui.applyLanguage($(this).val()) });
Acui.$controllerSelector = $('#default_ctrl_selector').change(function(){ Acui.setController($(this).val()) });
Acui.js = auwa.js ? auwa.js : []; 	// map of js script added by ajax
Acui.css = []	// map of css files added by ajax
Acui.contextMenu = {} // collecion of contextual menus
Acui.base = {}; // collection of uiNode

// display a message inside the topbar
Acui.notice = function(msg, type){
	var $p = $('<p class="alert alert-'+type+'">'+msg+'</p>');
	$('#ajax_msg').append($p);
	$p.fadeIn(200).delay(2000).fadeOut(150).queue(function(){
		$p.dequeue().remove();
	});
}

// call the core and execute a callback function after the response
Acui.callCore = function(query, obj, controller, module, callback){
	var data = {
		controller: controller ? controller : null,
		module: module ? module : null,
		data: obj,
		query: query
	}
	if (auwa.appController) data.AuwaController = auwa.appController;
	$.ajax({
		url: auwa.queryCore,
		data: data,
		type: 'POST',
		dataType: 'json',
		success: function(r){
		  if (typeof r=='object' && r.errors!=undefined && r.errors==false){
		     if (callback!=undefined && typeof callback.success=='function') callback.success(r.data);
		  }else {
		    if (r.errors==undefined ){
		    	Acui.notice('Réponse de la requête invalide','error');
		    	if (Acui.debugMode) Acui.debug('Réponse : <hr>'+JSON.stringify(r));
		    } 
		    else {
		    	if (callback!=undefined && typeof callback.error=='function') callback.error(r.errors);
				for(var i in r.errors)
					Acui.notice(r.errors[i], 'error');
		    }
		  }
		},
		error: function(r){
			if(Acui.debugMode){
				return Acui.debug(r.responseText);
			}
			if (callback!=undefined && typeof callback.fail=='function') callback.fail();
			else {
				Acui.notice('Appel impossible','error');
			}
		}
	});
}

// apply all filters to the lists (table or ul)
Acui.applyFilters = function(id_node){
	Acui(id_node, '.customlist.adv tr.item_line, ul.customlist.adv li').hide();
	
	var setSelector = function($o){
		var s = '';
		var value = $o.attr('data-value')?  $o.attr('data-value'):$o.val();
		var filtername = typeof $o.attr('data-filtername')!== 'undefined' ? $o.attr('data-filtername') : $o.attr('data-filter');
		if (value!==''){
			switch( $o.attr('data-filter') ){
				case 'contains':
					s +='[data-'+filtername+'~="'+value+'"]';
					break;
				case 'begins':
					s +='[data-'+filtername+'^="'+value+'"]';
					break;
				default:
					s +='[data-'+filtername+'='+value+']';
			}
		} else
			s +=  '[data-'+filtername+']';
		return s;
	}

	var selector = '';
	Acui(id_node,'.list-filter, button[data-filter].active, input.search').each(function(){
		selector += setSelector( $(this) );
	})
	if (Acui(id_node).useCtrl) {
		selector = selector + setSelector( Acui.$controllerSelector ) + ', '+selector+'[data-controller=none]';
	}
	Acui(id_node,'.customlist.adv tr.item_line'+selector+', ul.customlist.adv li'+selector).show();
}


Acui.setSwitches = function(id_node){
	Acui(id_node, '.btn-switch').each(function(){
		var $s = $(this).next('div.switch');
		if($s.length==0){
			var id = Acui.createId();
			$(this).attr('id',id);
			var c = parseInt( $(this).val() )==1 ? 'on' : 'off';
			$s = $('<div>', {"class": 'switch '+c,"name": id});
			$(this).parent().append( $s );
			$(this).on('change', function(){
				var $t = Acui(id_node, '[name='+id+']');
				if ($(this).val()==1 && !$t.hasClass('on') ) $t.addClass('on').removeClass('off') 
				if ($(this).val()==0 && !$t.hasClass('off') ) $t.addClass('off').removeClass('on');
			})
		}
	});
	Acui(id_node,'.btn-switch+.switch').off('click').on('click', function(){
		$(this).toggleClass('on').toggleClass('off');
		var val = $(this).hasClass('on') ? "1" : "0";
		$('#'+$(this).attr('name')).val(val).trigger('change');
	});
}
Acui.applyLanguage = function(l){
	if (!l) l = Acui.$languageSelector.val();
	$(document).find('.ACUI [data-lang]').hide();
	$(document).find('.ACUI [data-lang='+l+']').show();
}
Acui.setController = function(c){
	Acui.callCore('setCurrentController', c, 'DefaultCore');
}
Acui.insertNode= function(id, title, cl, show){
	var r = new Acui.nodeUI(id, title, cl, show);
	setTimeout(function(){
		Acui(id).focus();
	}, 11);
	return r;
}
Acui.debug = function(r){
	console.log(r);
	idDebug = Acui.createId();
	$node = Acui.insertNode(idDebug,'Debug', 'mergeHeader', true).insertContent(r, 'ACUI_debug');
	Acui.notice('Voir le debug','warning');
}
Acui.lastOpening = false;	// to avoid double click

Acui.loadJs = function(jsArray, $node){
	var js = jsArray[0];
	jsArray.shift();
	var ending = function(){
		if (jsArray.length==0){
    		$node.init();
    		if ($node.$e) $node.focus();
    		return;
    	} else {
    		Acui.loadJs(jsArray, $node);
    	}	
	}
	if ( typeof js!=='undefined' && (!Acui.js || Acui.js.indexOf(js.url)==-1) ){
		if (Acui.debug) console.log('loading js file : '+js.url);
		jsReaded = true;
		jQuery.ajax({
	        type:'GET',
	        url:js.url+'.js?v='+Acui.session,
	        success: function(){
				Acui.js.push(js.url);
	        	ending();	
			},
	        dataType:'script'
	    });
	} else {
		ending();	
	}
}

// create a uiNode and call the Auwa Core to fill it
Acui.open = function(id, data, cl, fn, show){
	if (Acui.lastOpening==id) return false;	// prevent double click
	Acui.lastOpening = id;
	var li = data.li ? data.li : false;
	if (Acui.base[id] && Acui.base[id].active){
		if (li) li.removeClass('loading');
		Acui(id).restore();
		Acui.lastOpening = false;
		return id;
	}
	if (li) delete data.li;
	if (typeof data=='string'){ // it is an url, it must be parsed
		u = data;
		data = {};
		u.replace('?','').split("&").forEach(function(part) { // parse url variables
		    var item = part.split("=");
		    data[item[0]] = decodeURIComponent(item[1]);
		});
	}
	if (!data || (!data.controller && !data.module)){
		Acui.lastOpening = false;
		return Acui.debugMode ? Acui.debug(JSON.stringify(data)) : Acui.notice('Appel éronné', 'error');
	}
	var cl_content = '';
	if (typeof cl=='object'){
		var cls = cl;
		if (cls.node) cl = cls.node; 
		if (cls.content) cl_content = cls.content+' '; 
	}
	if (!cl) cl = '';
	if (!id) id=Acui.createId();
	if (!data.action) data.action = true;
	if (typeof auwa.forceTheme !=='undefined') data.theme = auwa.forceTheme;
	if (auwa.appController) data.AuwaController = auwa.appController;
	$.ajax({
		url: auwa.queryCore,
		data: data,
		dataType: 'json',
		type: 'post',
		success: function(r){
			if (li) li.removeClass('loading');
			if (r.errors!==undefined){
				Acui.notice(r.errors, 'warning');
				Acui.lastOpening = false;
				return;
			}
			r.css.forEach(function(css){
				if (css.media) css.media="all";
				if ( Acui.css.indexOf(css.url)==-1 ){
					$("head").append(
				    	$(document.createElement("link")).attr({rel:"stylesheet", media:css.media, type:"text/css", href:css.url+'.css?v='+Acui.session})
				  	);
				  	Acui.css.push(css.url);
				}
			});
			var jsReaded = false;

			$node = new Acui.nodeUI(id, r['title'], cl, false);
			Acui.loadJs(r.js, $node);
			if (r.noCtrlFilter) $node.useCtrl = false;
			if (typeof fn=='function') $node.init = fn;

			$node.insertContent(r.html, cl_content+'ACUI');
			$node.data = data;
			Acui.applyFilters(id);
			Acui.applyLanguage();
			Acui.setSwitches(id);
			// ready event triggering
			$node.$e.trigger('readyNode');
			if (jsReaded==false) {
				setTimeout(function(){
					$node.init();
					$node.focus();
				},11);				
			}
			Acui.lastOpening = false;
		},
		error: function(r){
			if (li) li.removeClass('loading');
			if (r.responseText=='Disconnected') return window.location.reload();
			if(Acui.debugMode && r.responseText !==''){
				return Acui.debug(r.responseText);
			}
			Acui.notice('Error','error');
		}
	});
	return id;
}
// nodeUI Object
Acui.nodeUI = function(id, title, cl){
	this.id = id;
	this.classContent = '';
	this.data = {};
	this.$e = false;
	this.$content;
	this.$title;
	this.useCtrl = true;
	this.active = false;
	this.init = (Acui.base[id]) ? Acui.base[id].init: function(){};
	this.$nav;
	this.contextMenus={};
	if (title!==false){;
		this.title = title;
		this.class = typeof cl!=='undefined' ? cl : '';
	}
	this.preventClose = false;
	this.insert();
}
	Acui.nodeUI.prototype.get= function(){
		return this.$e;
	}
	// create and insert a nodeUI into the dom and store it the Acui.base with it id
	Acui.nodeUI.prototype.insert = function(){
		this.active = true;
		var $e= $('<div>', {
			'class' : 'uiNode '+this.class,
			'id': this.id
		}).append( 
			$('<div>', {'role':'title'}).append(
					$('<span>', {'role':'title', 'text':this.title}),
					$('<span>', {'role':'closeNode', 'name':this.id}),
					$('<span>', {'role':'maximizeNode', 'name':this.id}),
					$('<span>', {'role':'minimizeNode', 'name':this.id})
				),
			$('<div>', {'role':'content'}),
			$('<nav>', {'class': 'navbar'})
		);
		$e.draggable({ 
			handle: "[role=title], .ACUI>header",
	      animate: true,
	      animateDuration: 200,
		});
		$e.resizable({
	      animate: true,
	      animateDuration: 180,
	      stop: function(){
	      	setTimeout(function(){
	      		$e.removeClass('maximized').trigger('resizeNode');
	      	},220)	
	      }
	    });
	    $e.appendTo('#mainPanel');
	    $('#nodeTabs').append(
	    	$('<li>', {'data-node': this.id, 'text': this.title})
	    );
		this.$e = $e;
		this.$content = this.$e.find('[role=content]');
		this.$nav = this.$e.find('nav.navbar');
		this.$title = this.$e.find('span[role=title]');
		this.$tab = $('#nodeTabs li[data-node='+this.id+']');
		Acui.base[this.id] = this; // insert to collection
		var $o = this;
		return this;
	}
	// insert the content into the nodeUI
	Acui.nodeUI.prototype.insertContent = function(contentNode, cl){
		this.$content.empty().append(contentNode);
		if (cl){
			this.classContent=cl;
			this.$content.addClass(cl);
		}
		if (Acui.$languageSelector.find('option').length>1)
			this.$content.find('input[data-lang],textarea[data-lang]').each(function(){
				$(this).addClass('lang').css('background-image','url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/PnDUgAJNAN9oGvvMQAAAABJRU5ErkJggg==), url(../img/flags/'+$(this).attr('data-lang').toUpperCase()+'.png)')
			});
		return this;
	}
	// change the id of the nodeUI
	Acui.nodeUI.prototype.changeId = function(newId){
		var id = this.id;
		if (this.active){
			this.$e.attr('id', newId);
			this.$tab.attr('data-node', newId);
		}
		this.id = newId;
		Acui.base[newId] = Acui.base[id];
		delete Acui.base[id];
		return Acui.base[newId];
	}
	// resize the nodeUI
	Acui.nodeUI.prototype.resize = function(w, h){
		this.$e.width(w);
		if (typeof h !='undefined') this.$e.height(h);
		this.$e.trigger('resizeNode');
		return this;
	}
	// resize the nodeUI
	Acui.nodeUI.prototype.place = function(left, top){
		this.$e.css({
			'left': left,
			'top': top
		})
		return this;
	}
	Acui.nodeUI.prototype.setTitle = function(title){
		this.title = title;
		this.$title.text(title);
		this.$tab.text(title);
	}
	Acui.nodeUI.prototype.trigger= function(action){
		switch(action){
			case 'closeNode' : this.close(); break;
			case 'minimizeNode' : this.minimize(); break;
			case 'maximizeNode' : this.maximize(); break;
		}
		return this;
	}
	Acui.nodeUI.prototype.ready= function(fn){
		this.init = fn;
	}
	Acui.nodeUI.prototype.setEvents = function(eventType, events){
		for(var selector in events){
			var fn = events[selector];
			$(document).off(eventType, '#'+this.id+' '+selector);
			$(document).on(eventType, '#'+this.id+' '+selector, fn);
		}
	}
	Acui.nodeUI.prototype.close = function(){
		if (typeof this.preventClose=='function'){
			if( !this.preventClose() ) return this;
		}
		var $node = this.$e;
		if (typeof $node=='undefined') return;
		this.active=false;
		this.$tab.remove();
		$node.trigger('closeNode').css({
			left: (parseInt($node.css('left').replace('px','')))
		});
		$node.find('[role=content]').fadeOut(250);
		$node.trigger('closeNode').removeClass('opened');
		setTimeout(function(){
			$node.remove();
		}, 250);
	}
	Acui.nodeUI.prototype.minimize = function(){
		if (this.$e==false || this.$e.hasClass('minimized')) return;
		this.$tab.addClass('minimized').removeClass('focus');
		this.$e.css('left', 80+this.$tab.offset().left-this.$tab.width()/2).addClass('minimized').removeClass('focus');
	}
	Acui.nodeUI.prototype.maximize = function(){
		this.$e.css('left',false).toggleClass('maximized').trigger('resizeNode');
		return this;
	}
	Acui.nodeUI.prototype.restore = function(){
		this.$tab.removeClass('minimized');
		this.$e.removeClass('minimized').css('left','');
		this.focus();
		return this;
	}
	Acui.nodeUI.prototype.toggle = function(){
		this.$e.toggleClass('minimized').toggleClass('focus');
		this.$tab.toggleClass('minimized').toggleClass('focus');
		return this;
	}
	Acui.nodeUI.prototype.focus = function(){
		if ( this.$e.hasClass('focus')) return;
		if ( !this.$e.hasClass('opened')) this.$e.addClass('opened');
		$('.uiNode, #nodeTabs li[data-node]').removeClass('focus');
		this.$e.addClass('focus');
		this.$tab.addClass('focus');
		return this;
	}
	Acui.nodeUI.prototype.refresh=function(fn){
		var $n = this;
		var id = this.id;
		$.ajax({
			url: auwa.queryCore,
			data: this.data,
			dataType: 'json',
			type: 'post',
		}).done(function(r){
			$n.insertContent(r.html);
			$n.init();
			$n.$e.trigger('readyNode');
			$n.setTitle(r.title);
			Acui.applyFilters(id);
			Acui.applyLanguage();
			Acui.setSwitches(id);
			if (typeof fn=='function') fn();
		});
		return this;
	}
	Acui.nodeUI.prototype.contextMenu = function(menu, selector){
		if (!menu) menu = {};
		var $node = this.$e;
		if (selector===true)
			this.contextMenus = menu;
		else
			this.contextMenus[selector] = menu;
		var setMenu = function(selector, node){
			if (!selector) return;
			var $target = $node.find(selector);
			var menu = node.contextMenus[selector];
			$target.off('contextmenu');
			$target.each(function(){
				$(this).on('contextmenu', function(e){
					$('.onContextMenu').removeClass('onContextMenu');
					var $o = $(this).addClass('onContextMenu');
					e.preventDefault();
					e.stopPropagation();
					var pos = {'top': e.pageY, 'left': e.pageX};
					var $m = $('<ul role="contextmenu" id="Cxt" style="position: absolute; width: 200px; left: '+pos.left+'px; top: '+pos.top+'px">');
					for(var i in menu){
						if (menu[i]==false || (typeof menu[i].canDisplay=='function' && !menu[i].canDisplay($o) ) )
							continue;
						var $li = $('<li>', {
										'class': menu[i].class,
										'text' : menu[i].text,
										'name': i
										});
						if (typeof menu[i].vars !== 'undefined') $li.attr('data-vars', menu[i].vars);
						if (typeof menu[i].fn !== 'undefined') 
							$li.on('click', function(e){ 
								e.stopPropagation();
								$o.removeClass('onContextMenu');
								node.contextMenus[selector][$(this).attr('name')].fn($o,e); 
								$('#Cxt').remove();
							} );
						$m.append( $li	);
					}
					if ( typeof $('#Cxt').get(0) != 'undefined'){
						$('#Cxt').remove();
					}
					$('body').append($m.on('contextmenu', function(){return false;}));
					for(var i in menu){
						var func = menu[i].fn;
					}
					return false;
				});
			});
		}

		if (selector===true) {
			for (var i in this.contextMenus)
				setMenu( i, this )
		} else {
			setMenu( selector, this)
		}
	}

// ========== ACUI EVENTS ==========

$(document).on('click','#mainPanel *',function(e){
	$('#Cxt').remove();
	$('.onContextMenu').removeClass('onContextMenu');
}).on('click','#mainPanel a.app',function(e){
	e.preventDefault();
	e.stopPropagation();
}).on('click', '.uiNode > div[role=title] [role][name]', function(e){
	e.stopPropagation();
	Acui( $(this).parents('.uiNode').attr('id') ).trigger( $(this).attr('role') );
}).on('contextmenu', '.uiNode > [role=content]', function(e){
	//e.preventDefault();
	e.stopPropagation();
	//return false;
}).on('click','.uiNode',function(){
	Acui( $(this).attr('id') ).focus();
}).on('click', '#nodeTabs li[data-node]', function(){
	var id =$(this).attr('data-node');
	if ($(this).hasClass('focus')) Acui(id).toggle();
	else {
		Acui(id).restore();
	}
}).on('click','#mainPanel a.app, .customlist li[data-u]', function(e){
	e.stopPropagation();
	var node = $(this).attr('name') ? $(this).attr('name') : Acui.createId();
	var cl = $(this).attr('role') ? $(this).attr('role') : '';
	cl +=  (typeof $(this).attr('data-dialog')!='undefined') ? ' mergeHeader ' : '';
	var u = $(this).attr('data-u') ? $(this).attr('data-u') : $(this).attr('href');
	Acui.open(node, u, cl);
}).on('click', '.icons > ul > li.app',function(){
	if ($(this).hasClass('loading')) return;
	$('#icons').removeClass('full');
	$(this).addClass('loading');
	var c = $(this).attr('data-controller');
	var m =$(this).attr('data-module') ? $(this).attr('data-module') :'';
	var a =$(this).attr('data-action') ? $(this).attr('data-action'):'';
	if (!c && !m) return;
	var data = {
		li: $(this),
	    controller: c,
	    module: m,
	    action: a
	  }
	Acui.open( m+c+a, data, ( (typeof $(this).attr('data-dialog')!='undefined') ? 'mergeHeader' : '' ) + " "+$(this).attr('class') );
}).on('keydown', function(e){
	if (e.keyCode==65 && e.altKey){ // alt key and 'a' key
		$('#icons').toggleClass('full');
	}
	if (e.keyCode==27 ){ // escape key
		$('#icons').removeClass('full');
		$('#Cxt').remove();
		$('.onContextMenu').removeClass('onContextMenu');
	} 
}).on('change', '.list-filter',function(){
	target = $(this).parents('.uiNode');
	if (target.length>0) {
		id = target.attr('id');
		Acui.applyFilters(id);
	} else {
		for (var id in Acui.base){
			Acui.applyFilters(id);
		}
	}
}).on('click', 'button[data-filter]',function(){
	var f = $(this).attr('data-filter');
	var active = $(this).hasClass('active');
	$('button[data-filter='+f+']').removeClass('active');
	if (!active) $(this).addClass('active');
	Acui.applyFilters( $(this).parents('.uiNode').attr('id') );
}).on('keyup', 'input.search', function(e){
	Acui.applyFilters( $(this).parents('.uiNode').attr('id') );
});

$('#desktop').click(function(){
	for(var i in Acui.base) Acui(i).minimize();
});
$('.AcuiImgNav').click(function(){
	Acui.loadDirectory('pictures/', 'picture','Mes Images', $(this));
});
$('.Disconnect').click(function(){
	Acui.callCore('disconnect', true, 'CoreLogin', false, {
		success: function(){
			window.location.reload();
		}
	});
});
$('.UserAccount').click(function(){
	$('#AcuiUserAccount').toggleClass('active');
});
$('#AcuiUserAccount input').keyup(function(e){
	if (e.keyCode==13) $('#AcuiUserAccount button').click();
});
$('#AcuiUserAccount button').click(function(){
	var data = {
		id : $('#idConnectedUser').val(),
		pwd: $('#passwordConnectedUser').val()
	}
	Acui.callCore('chgPasswd', data, 'CoreLogin', false, {
		success: function(){
			Acui.notice('Mot de passe modifié', 'success');
		},
		fail: function(){
			Acui.notice('Erreur pendant la modification', 'error');
		}
	});
	$('.UserAccount').click();
});
$('#goCoreHome').click(function(){
	$('#icons').toggleClass('full');
});
if (Acui.debugMode) console.log('Auwa Core.UI loaded');


// ========== ACUI NAVIGATOR ==========
Acui.clickActions = {
	context : false,
	rename : false
}
Acui.navFn = {
	menu: {},
	file : null,
	source: null,
	action: false
};
Acui.navFn.rename = function($o,e){
	$i = $o.find('input');
	Acui.clickActions.rename = true;
	$i.removeAttr('disabled').focus().on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
	}).on('change', function(){
		$(this).attr('disabled','disabled');
		Acui.navFn.action = 'copy';
		Acui.navFn.file = $(this).attr('name');
		Acui.navFn.action = 'rename';
		Acui.navFn.source = $('#Nav').find('nav[role=path]').attr('data-path');
		Acui.navFn.paste( $o, e, Acui.navFn.source,  $(this).val() );
		Acui.clickActions.rename = false;
	});
}
Acui.navFn.delete = function($o,e){
	if (! confirm ("Supprimer le fichier "+$o.attr('data-path')+" ?")) return;
	file = $o.attr('data-path');
	Acui.callCore('fileRemove', {
			query: 'fileRemove',
			file : file
		}, 'DefaultCore', null, {
			success: function(){
				Acui.notice('Suppression réussie', 'success');
				Acui.loadDirectory( Acui('Nav', 'nav[role=path]').attr('data-path') );
			},
			fail: function(){
				Acui.notice('La suppression à échouée', 'error');
			}
		});
}
Acui.navFn.copy = function($o,e){
	Acui.navFn.action = 'copy';
	Acui.navFn.file = $o.attr('data-path');
	Acui.navFn.source = Acui('Nav').data.path;
	Acui('Nav', 'li').removeClass('cut');
}
Acui.navFn.cut = function($o,e){
	Acui.navFn.action = 'cut';
	Acui.navFn.file = $o.attr('data-path');
	Acui.navFn.source = Acui('Nav').data.path;
	Acui('Nav', 'li').removeClass('cut');
	$o.addClass('cut');
}
Acui.navFn.paste = function($o, e, d, n){
	var destination = typeof d=='undefined' ? ( ( typeof $o.attr('data-item')!=='undefined' && $o.attr('data-item')=='Folders'
					? $o.attr('data-path')+'/' : Acui('Nav').data.path ) ) : d;
	Acui('Nav', 'li').removeClass('cut');
	var file = Acui.navFn.file.replace(Acui.navFn.source,'');
	var data = {
			query: 'fileCopy',
			source: Acui.navFn.source,
			destination : destination,
			file : file,
			newfile: typeof n=='undefined' ? false : n,
			fileaction: Acui.navFn.action
		}
	console.log(data);
	Acui.callCore('fileCopy', data, 'DefaultCore', null, {
			success: function(){
				Acui.navFn.action = false;
				Acui.loadDirectory( Acui('Nav', 'nav[role=path]').attr('data-path') );
			}
		});
}
Acui.createDir= function(parent, name){
	Acui.callCore('createDirectory', {
				'parent' : parent,
				'name' : name
			}, 'DefaultCore', null, {
				success: function(){
					Acui.notice('Répertoire créé', 'success');
					Acui.loadDirectory(parent);
				}
			});
}
Acui.navmenu = {	
	'section[role=filelist] ul li': {
		'copy' : {
			class: 'fa fa-copy',
			text: 'Copier',
			fn : Acui.navFn.copy
		},
		'cut' : {
			class: 'fa fa-cut',
			text: 'Couper',
			fn : Acui.navFn.cut
		},
		'paste' : {
			class: 'fa fa-paste',
			text: 'Coller',
			fn : Acui.navFn.paste,
			canDisplay: function(){
				return Acui.navFn.action !== false;
			}
		},
		'rename' : {
			class: 'fa fa-edit',
			text: 'Renommer',
			fn : Acui.navFn.rename
		},
		'delete' : {
			class: 'fa fa-trash',
			text: 'Supprimer',
			fn : Acui.navFn.delete
		}
	},
	'[role=content]': {
		'paste' : {
			class: 'fa fa-paste',
			text: 'Coller',
			fn : Acui.navFn.paste,
			canDisplay: function(){
				return Acui.navFn.action !== false;
			}
		}
	}
}
Acui.loadDirectory= function(path, type, title, $o){
	if ( typeof $('#Nav').get(0) == 'undefined'){
		title = typeof title=='undefined' ? 'Navigateur' : title;
	} else {
		Acui('Nav').data.path = path;
		Acui('Nav').data.fileaction= Acui.navFn.action;
		Acui('Nav').data.filetarget= Acui.navFn.file;
		Acui('Nav').refresh();
		if (typeof $('#Nav').attr('data-type')!== 'undefined') type = $('#Nav').attr('data-type');
	}
	data = {
		filetype: type,
		path: path,
		li: $o,
		title: title,
		controller: 'DefaultCore',
		action: 'navigator'
	}
	Acui.open('Nav', data, 'navigator maximized', function(){
		$('#Nav').attr('data-type', type);
		this.contextMenu(Acui.navmenu, true);
		Acui('Nav','li[data-item=Folders]').on('click', function(){
			Acui.loadDirectory($(this).attr('data-path')+'/', type, title, false);
		})
		Acui('Nav','[role=dirup]').on('click', function(){
			var p = Acui('Nav').data.path.split('/');
			p.pop();p.pop();
			Acui.loadDirectory(p.join('/')+'/', type, title, false);
		});
		Acui('Nav','[role=upload]').on('click', function(){
			Acui.imgUpload(Acui('Nav').data.path);
		})
		Acui('Nav','[role=addfolder]').on('click', function(){
			Acui('Nav', '[role=newFolderName]').toggle(150);
		})
		Acui('Nav','[role=newFolderName]').on('click', function(e){
			e.stopPropagation();
			return false;
		})
		Acui('Nav','[role=createDir]').on('click', function(e){
			// create the directory
			parent = Acui('Nav').data.path;
			name = $(this).parent().find('input').val();
			if (name=='') Acui.notice('Aucun nom de répertoire défini', 'danger');
			else {
				Acui.createDir(parent, name);
			};
		});
		$('#Nav').trigger('list');
	});
}


Acui.checkFileType= function(file) {
    if (file) {
        var extension = file.name.split('.').pop().toLowerCase();  //file extension from input file
        return {'name': file.name, 'extension': extension, 'result': ( auwa.fileTypesAllowed.indexOf( extension) > -1 )};  //is extension in acceptable types
    }
    return {'extension': 'unknown', 'result':false};
}

Acui.imgUpload = function(directory, name, ext){
	$node = new Acui.nodeUI('Upload', 'Importer une image', 'mergeHeader');

	$filePath = $('<input type="hidden" name="path">').val(directory);
	$fileImg = $('<input type="hidden" name="img">').val('');
	$fileInput = $('<input type="file" id="up_img">');
	$fileName = $('<input type="'+(name ? 'hidden':'text')+'" name="filename">');
	$exploreBtn = $('<button>', {"class":'file btn btn-default fa fa-folder fa-2x', type: "button"});
	$imgPreview = $('<figure style="width: 100%; height: 60%">');
	$content = $('<article>').append( $('<form>', {class:'uiUpload', 'enctype': 'multipart/form-data', 'method': 'post'}).append(
		$filePath, $fileImg, $fileInput, $exploreBtn,$fileName,
		$('<button>', {"class":' btn btn-default fa fa-download fa-2x'}),
		$imgPreview
	));
	$node.insertContent($content);
	$node.ready(function(){
		$node.focus();
		Acui('Upload', '.fa-folder').on('click', function(){
			Acui('Upload', '#up_img').click();
		})
		setTimeout(function(){
			Acui('Upload', '.fa-folder').click();
		},150);
	})
	$node.init();
	$fileInput.on('change', function(e){
		$node.focus();
		var file = e.target.files[0]; 
		var c = Acui.checkFileType(file);
		if (!name) $fileName.val(c.name);
		if (c.result){
			var fr = new FileReader();
			fr.readAsDataURL(file);		
			fr.onload = function(e){ 
				$fileImg.val(e.target.result);
				$imgPreview.css('background-image', 'url('+e.target.result+')'); 
			};   
		} else {
			$('form.uiUpload').get(0).reset();
			$imgPreview.css('background-image','');
			Acui.notice("Ce type de fichier n'est pas supporté : "+c.extension, "error");
		} 
	});
	$content.on("submit", function(e){
		e.preventDefault(); //On empêche de submit le form
		if (!name && $fileName.val()=='') {
			Acui.notice('Vous devez spécifier un nom de fichier', 'warning');
			return;
		}
		var form = $(this);
		data = {
			dataURL : $fileImg.val(),
			ext: ext ? '.'+ext : '',
			file : directory + (name ? name : $fileName.val()),
		}
		Acui.callCore('writeDataURL', data, 'DefaultCore', null,{
			success: function(r){
				$('.wait').animate({'opacity':0}, 300);
   				Acui('Upload').$e.trigger('uploadDone', data.file);
   				Acui('Upload').close();
   				if (Acui('Nav').active) Acui('Nav').refresh().focus();
	   		},
	   		fail: function(r){
				switch(r.result){
   					case 1:
   					case 2:
   						msg = 'Fichier trop gros';
   						break;
   					case 3:
   						msg = 'Fichier que partiellement téléchargé';
   						break;
   					case 4:
   						msg = 'Aucun fichier téléchargé';
   						break;
   					case 7:
   						msg = 'Le fichier ne peut pas être copié dans '+directory;
   						break;
   					case 6:
   						msg = 'Il manque le dossier de réception temporaire';
   						break;
   					case 42:
   						msg = 'Type de fichier non autorise : '+r.minetype;
   						break;
   					default:
   						msg = 'Erreur';
   						break;
	   			}
	   			Acui.notice(msg, 'error');
	   		}
		})
		return false;
	});

	Acui.cropTools = function(img, ratio, directory){
		var image = new Image();
		filepath = '../data/'+directory+img +'.jpg?v='+Acui.createId();
		image.src = filepath;
		var imgProp = {
			x:0,
			y:0,
			w:0,
			h:0,
			scale: 1
		}
		$node = new Acui.nodeUI('thumbCrop', 'Recadrez votre image', 'mergeHeader', true);
		content = '<header><button role="cropThumb" class="fa fa-save"></button></header><article><img src="'+filepath+'" height="400px" name="cropTarget"><input type="hidden" id="finalImg"></article>';
		$node.insertContent(content, 'ACUI');
		$node.focus();
		Acui('thumbCrop', '[name=cropTarget]').Jcrop({
		        //onRelease: Acui.jscrop.releaseCheck
	      },function(){
	        jcrop_api = this; 
		      jcrop_api.setOptions({ 
		      	allowSelect: false,
		      	allowMove: true,
		      	allowResize: true,
		      	aspectRatio: ratio,
		      	bgOpacity: .35
		      });
		      $img = Acui('thumbCrop', '[name=cropTarget]');
		      h =$img.width()/ratio;
		      Acui('thumbCrop').resize($img.width()+10, $img.height()+40);
		   
	        jcrop_api.animateTo([0,($img.height()-h)/2,$img.width(),($img.height()-h)/2+h]);
	      });
		Acui('thumbCrop', '[role=cropThumb]').on('click', function(){
			$target = $('.jcrop-holder > div');
			$('.adminContent').append( $('<div>',{class:'wait'}) ) ;

			image.onload = cutImageUp;
			image.src = filepath;
			function cutImageUp() {
				var scale = $img.width() / image.width;
				imgProp = {
					x: parseInt( $target.css('left').replace('px','') )/scale,
					y: parseInt( $target.css('top').replace('px','') )/scale,
					w: $target.width()/scale,
					h: $target.height()/scale
				}
				var canvas = document.createElement('canvas');
				var ratio = imgProp.w / imgProp.h;
	            canvas.width = imgProp.w<=1500 ? imgProp.w : 1500; // to avoid too largest image
	            canvas.height = canvas.width / ratio;
	            var context = canvas.getContext('2d');
	            context.drawImage(image, imgProp.x, imgProp.y, imgProp.w, imgProp.h, 0, 0, canvas.width, canvas.height);

	            data = {
            		'file': directory+img,
            		'dataURL': false
	            }
	            data.dataURL = canvas.toDataURL("images/jpg");

	            Acui.callCore('writeDataURL', data, 'DefaultCore', null,{
					success: function(r){
						Acui('thumbCrop').$e.trigger('cropDone', [filepath]);
			   			Acui('thumbCrop').close();
			   		},
			   		fail: function(r){
			   			Acui.notice('Impossible de sauvegarder cette image', 'warning');
			   		}
			   	});
			}
		})
	}
} 
// use the list of Smartik from https://github.com/SMK-Toolkit/SMK-Font-Awesome-PHP-JSON
Acui.displayFaIcons = function($target){
	$target.each(function(){
		var $o = $(this);
		var $fa = $('<div>', {role: 'faList'});
		for(var icon in Acui.fontawesome){
			$s = $('<span>', {class: 'fa '+icon, 'data-fa': icon}).on('click',function(){
					$o.val('fa '+$(this).attr('data-fa')).trigger('change');
					$fa.find('span').removeClass('on');
					$(this).addClass('on');
					$fa.toggle();
				});
			if ($o.val()=='fa '+icon) $s.addClass('on');
			$fa.append( $s );
		}
		$btn = $('<button class="btn">').html('<i class="fa fa-font-awesome">').on('click', function(){
			$fa.toggle();
		})
		$o.hide().after($btn,$fa);
	})
}

Acui.pxToNum = function(px){
	return parseInt(px.replace('px',''));
}