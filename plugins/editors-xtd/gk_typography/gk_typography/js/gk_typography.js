function gkclick(tag){	
	jInsertEditorText(tag, $GKEditor);
	$('sbox-btn-close').fireEvent('click');	
}

function gkhideTab(val) {	
	if($$('#sbox-window .gkTypoTable')) {
		$$('#sbox-window .gkTypoTable').each(function(el, i){		
			if(i==val){	
				el.setStyle('display', 'block');	
			} else {	
				el.setStyle('display', 'none');	
			}	
		});	
	
		$$('#sbox-window .gkTypoMenu li').setProperty('class', '');
		if($$('#sbox-window .gkTypoMenu li')[val])$$('#sbox-window .gkTypoMenu li')[val].setProperty('class', 'active');
	}
}

window.addEvent('load', function() {
	$('editor-xtd-buttons').getElement('.gk_typography').getElement('a').addEvent('click', function() {
		(function() {
			gkhideTab(0); 
			$('sbox-window').setStyle('padding', '0px');
			$$('#sbox-window .gkTypoMenu li')[0].setProperty('class', 'active');
			$$('#sbox-window .gkTypoContent')[0].setStyle('height', $$('#sbox-window .gkTypoMenu')[0].getSize().y + 'px');
		}).delay(500);
	});
});