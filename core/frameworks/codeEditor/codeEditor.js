Acui.codeEditor = {
	file: false,
	path: '',
	$target: false,
	init: function($target){
		if ($target) this.$target = $target;
		this.$target.attr('contenteditable', true).each(function(i, block) {
		    hljs.highlightBlock(block);
		});
		this.$target.off('keydown').on('keydown', function(e){
			switch(e.which){
				case 18: // alt
				case 17: // ctrl
				case 16: // shift
				case 37: // left
				case 38: // top
				case 39: // right
				case 40: // bottom
				case 18: // capslock
					return true;
					break;
				case 9: // tab
					// and if is there a selection ?
					document.execCommand('insertHtml', false, '\t');
					e.preventDefault();	
					return false;
				case 13: 
					document.execCommand('insertHtml', false, auwa.EOL);
					e.preventDefault();	
					return false;
					break;
				default:
					// actualize editor
					break;
			}
		})
	},
	setContent: function(code){
		this.$target.empty().text(code);
		this.init();
	},
	getContent: function(){
		return this.$target.text();
	}
}