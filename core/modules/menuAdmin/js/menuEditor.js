(function(){
	if (Acui.debugMode) console.log('Menu Manager loaded');
	var _controller = 'menus';
	var _module = 'menuAdmin';

	// get all items of the menu
    var getItems = function(idNode, $target){
    	var items = {};
    	if (!$target) $target = Acui(idNode, '.menuContainer');
    	var i = 0;
    	$target.find('>.customlist > div').each(function(){
      		var item = JSON.parse( $(this).find('>code').text() );
      		item.menu = getItems( idNode, $(this) );
    		items[i]=item;
    		i++;
    	});
    	return items;
    }

	// save the menu
	var saveMenu = function(idNode, menuName){
		var mode = typeof menu=='undefined' ? 0 : 1;
		var data= {
			menu : getItems(idNode),
			name: menuName,
			create: mode
		}
		Acui.callCore('saveChanges', data, _controller, _module, {
			success: function(){
				Acui.notice('Mis-à-jour réussie', 'success');
			},
			fail: function(){
				Acui.notice('Échec de la mise à jour','danger');
			}
		});
	}

	// edit an item from a menu
	var editItem = function(idNode, menuName, $o){
		data= {
			module: _module,
			controller:  _controller,
			action: 'editItem',
			item: JSON.parse( $o.find('>code').text() ),
		}
		var editorIdNode = 'editor'+idNode+$o.attr('data-key');
		Acui.open(editorIdNode, data, 'menuItemEditor mergeHeader', function(){
			// when this uiNode is ready
			Acui(editorIdNode).setEvents('click', {
				'[role=saveItem]': function(){
					var item = {};
					Acui(editorIdNode, 'fieldset').each(function(){
						if (typeof $(this).attr('data-type')!='undefined' && $(this).attr('data-type')!= Acui(editorIdNode, 'select[name=linkType]').val())
							return;
						var label = $(this).find('label');
						item[label.attr('data')] = {};
						type = typeof label.attr('data-type')!='undefined' ? label.attr('data-type') : '';
						$(this).find('div input, div select').each(function(){
							if (type=="lang") 
								item[label.attr('data')][$(this).attr('data-lang')] = $(this).val();
							else{
								item[label.attr('data')] = $(this).val();
							}
						})
					});
					$o.find('>code').text( JSON.stringify(item) );
					$o.find('span[role=text]').text(item.text[Acui.language]);
					Acui(editorIdNode).close();
				}
			});

			Acui(editorIdNode).setEvents('change',{
				'select[name=linkType]': function(){
					var linkType=$(this).val();
					Acui(editorIdNode, 'fieldset[data-type]').hide();
					Acui(editorIdNode, 'fieldset[data-type='+linkType+']').show();
				}
			});
			Acui(editorIdNode, 'select[name=linkType]').trigger('change');
		});
	}

	// When one menu editor is loading
    $(document).on('readyNode', '.menuEditor', function(){
    	var idNode = $(this).attr('id');
    	Acui(idNode).ready(function(){
    		var $o = false;
    		var move = false;
    		var click = false;
    		Acui(idNode,'section.customlist').disableSelection().sortable({
				placeholder: "sortingColumn",
				items: ">div.menu_item_line",
  				forceHelperSize: true,
  				helper: "clone",
  				axis: 'y',
  				handle: '.handle',
				contectWith: 'section.customlist',
				sort: function(){
					Acui(idNode,'.ui-sortable-helper').height(
						Acui(idNode,'.customlist div.menu_item_line:hidden').height()
					);
				}
			});
			var click= true;
			var $o = false;
			var left = 0;
			Acui(idNode).setEvents('mousedown',{
				'div.menu_item_line': function(e){
					e.stopPropagation();
					$o = $(this);
					var init = $o.offset();
					var eInit = {
						x: e.clientX,
						y: e.clientY
					};
					var left = 0;
					$(document).mousemove(function(eM){
						var move = eM.clientX-eInit.x;
						left = Acui.pxToNum($o.css('left')) + move;
						if ( left > 0 && $o.prev().find('section.customlist').first().length==0 
							|| left< 0 && $o.parent().parent().hasClass('menuContainer') ) return left = 0;
						if (left>40)  {
							var $t = $o.prev().find('section.customlist').first();
							if ($t.length>0){
								$t.append( $o.detach() );
								left = 0;
							}
						}
						if (left<-40)  {
							var $t = $o.parent().parent();
							if (!$t.hasClass('menuContainer')){
								$t.after( $o.detach() );
								left = 0;
							}
						}
						$o.css( 'left', left);
						eInit.x = eM.clientX;
					});
				}
			});
			$(document).mouseup(function(){
				click = false;
				if ($o) {
					$o.off();
					$o.css('left', '');
				}
				$(this).off('mousemove');
			})
			Acui(idNode).setEvents('click',{
				'[role=saveMenu]': function(){
					var menu = saveMenu(idNode, $(this).attr('name'));
				},
				'[role=addItem]': function(){
					var item = Acui(idNode, 'div.menu_item_line.sample').clone().removeClass('sample');
					Acui(idNode, '.menuContainer>section').append(item);
				},
				'section.customlist .fa-remove': function(){
					$t = $(this).parent().toggle(200);
					setTimeout(function(){
						$t.remove();
					}, 202);
				},
				'section.customlist .fa-edit': function(){
					editItem(idNode, Acui(idNode, '[role=saveMenu]').attr('name'), $(this).parent() );
				}
			})
   		});
   	});
})();