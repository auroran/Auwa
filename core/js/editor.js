$(document).ready(function(){
  if (Acui.debugMode) console.log('TinyMCE Editor Manager loaded');

  cssFiles= (typeof cssFiles!='undefined') ? cssFiles : [];
  var css = ['css/editor.css'];
  for(var i in cssFiles){
    css.push( cssFiles[i].url+'.css');
  }
auwa.launchEditor = function(selector, idNode){
  $(typeof selector=='undefined' ? '.editor' : selector).each(function(){
    if ( $(this).val()!==null ) $(this).val(  $(this).val().replace('%code(','{').replace(')%','}') ) ;
  });
  selector = typeof selector=='undefined' ? '.editor' : selector
  var $uiNode = Acui(idNode).get().addClass('uiEditor mergeHeader');
  $uiNode.on('resizeNode', function(){
    var $o = $(this);
    console.log('resize editor');
    var h = $o.find('div.ACUI article').height()-9;
    $o.find('.mce-menubar, .mce-toolbar-grp, .mce-statusbar').each(function(){
        h -= $(this).height();
    });
    $o.find('iframe').css('height',h);   
  }).on('closeNode', function(){
    a = tinymce.remove(selector);
  });
  tinymce.init({
    selector: selector,
    verify_html:false,
    extended_valid_elements : "em[class|name|id],i[class|style],span[class|style|id],a[class|style|href|target]",
    height: 500,
    language: 'fr_FR',
    
    plugins: [
      'advlist autolink lists link picture charmap print preview hr anchor truncate',
      'searchreplace wordcount visualblocks visualchars code fullscreen',
      'media nonbreaking table contextmenu directionality',
      'emoticons template paste textcolor colorpicker textpattern'
    ],
    toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | forecolor backcolor | bullist numlist outdent indent | link pictureManager media |',
    image_advtab: true,
    content_css: css,
    style_formats: [
      
      { title: 'Blocks', items: [
          { title: 'Paragraphe', block: 'p' },
          { title: 'Citation', block: 'blockquote' },
          { title: 'Suppr. flottement', block: 'div', classes: 'clearf' },
        ]
      },
      { title: 'En-ligne', items: [
        ]
      },
      { title: 'Conteneurs', items: [
          { title: 'Générique', block: 'div', wrapper: true },
          { title: 'Aside', block: 'aside', wrapper: true },
          { title: 'Article', block: 'article', wrapper: true },
          { title: 'Section', block: 'section', wrapper: true }
        ]
      },
      { title: 'Headers', items: [
          { title: 'Header 1', block: 'h1' },
          { title: 'Header 2', block: 'h2' },
          { title: 'Header 3', block: 'h3' },
          { title: 'Header 4', block: 'h4' },
          { title: 'Header 5', block: 'h5' },
          { title: 'Header 6', block: 'h6' }
        ]
      },
      { title: 'Message', items: [
          { title: 'Warning', block: 'p', classes: 'alert alert-warning' },
          { title: 'Info', block: 'p', classes: 'alert alert-info' },
        ]
      },
          
    ],
   // external_filemanager_path:"frameworks/tinymce/plugins/responsivefilemanager/",
   // filemanager_title:"Navigateur" ,

    external_plugins: { 
      //"filemanager" : "plugins/responsivefilemanager/plugin.min.js",
    },
    setup: function (ed) {
        ed.on('init', function(args) { 
           Acui(idNode, 'iframe').ready(function(){
              $uiNode.trigger('resizeNode');
              Acui.applyLanguage();
            });
       
          
        });

    }
   });
}

});