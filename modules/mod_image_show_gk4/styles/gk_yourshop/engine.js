window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_yourshop").each(function(el){
		if(!el.hasClass('activated')) {
			el.addClass('activated');
			var elID = el.getProperty("id");
			var wrapper = document.id(elID);
			var $G = $Gavick[elID];
			var slides = [];
			var contents = [];
			var links = [];
			var play = false;
			var $blank = false;
			var loadedImages = false;
			var preloader = el.getElement('.gkIsPreloader');
	
			if(!loadedImages){
				var imagesToLoad = [];
				wrapper.getElements('.gkIsSlide').each(function(el,i){
					links.push(el.getElement('a').getProperty('href'));
					var newImg = new Element('img',{
						"title":el.getProperty('title'),
						"class":el.getProperty('class'),
						"style":el.getProperty('style')
					});
					
					newImg.setProperty('alt',el.getElements('a')[1].getProperty('href'));
					el.getElements('a')[1].destroy();
					newImg.setProperty("src",el.getElements('a')[0].getProperty('href'));
					el.getElements('a')[0].destroy();
					imagesToLoad.push(newImg);
					newImg.inject(el, 'after');
					el.destroy();
				});
				
				var time = (function(){
					var process = 0;				
					imagesToLoad.each(function(el,i){ if(el.complete) process++; });
	 				
					if(process == imagesToLoad.length){
						$clear(time);
						loadedImages = process;
						(function(){ 
							preloader.fade('out'); 
						}).delay(400);
					}
				}).periodical(200);
			}
			
			var time_main = (function(){
				if(loadedImages){
					$clear(time_main);
					
					wrapper.getElements(".gkIsSlide").each(function(elmt,i){
						slides[i] = elmt;
						if($G['slide_links']){
							elmt.addEvent("click", function(){window.location = wrapper.getElements(".gkIsSlide")[$G['actual_slide']].getProperty('alt');});
							elmt.setStyle("cursor", "pointer");
						}
					});
					
					slides.each(function(el,i){ if(i != 0) el.setOpacity(0); });
					
					if(wrapper.getElement(".gkIsText")){
						var text_block = wrapper.getElement(".gkIsTextBg");
						wrapper.getElements(".gkIsTextItem").each(function(elmt,i){ contents[i] = elmt.innerHTML; });
					}
					
					$G['actual_slide'] = 0;
					if(wrapper.getElements(".gkIsText")[0]) wrapper.getElements(".gkIsText")[0].innerHTML = contents[0];
					
					wrapper.getElement('.gkIsPrev span').addEvent('click', function() {
						$blank = true;
						gk_is_yourshop_anim(wrapper, contents, slides, $G['actual_slide']-1, $G);
					});
					
					wrapper.getElement('.gkIsNext span').addEvent('click', function() {
						$blank = true;
						gk_is_yourshop_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
					});
					
					if($G['autoanim']){
						play = true;
						$G['actual_animation'] = (function(){
							if(play && $blank == false){
								gk_is_yourshop_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
							}else $blank = false;
						}).periodical($G['anim_interval']+$G['anim_speed']);
					}
				}
			}).periodical(250);
		}
	});
});

function gk_is_yourshop_text_anim(wrapper, contents, which, $G){
	var txt = wrapper.getElement(".gkIsText");
	new Fx.Tween(txt,{property:'opacity',duration: $G['anim_speed']/2}).start(1,0);
	(function(){
		new Fx.Tween(txt,{property:'opacity',duration: $G['anim_speed']/2}).start(0,1);
		txt.innerHTML = contents[which];
	}).delay($G['anim_speed']);
}

function gk_is_yourshop_anim(wrapper, contents, slides, which, $G){
	if(which != $G['actual_slide']){
		var max = slides.length-1;
		if(which > max) which = 0;
		if(which < 0) which = max;
		var actual = $G['actual_slide'];
		
		$G['actual_slide'] = which;
		slides[$G['actual_slide']].setStyle("z-index",max+1);
		
		slides[actual].set('tween', {duration: $G['anim_speed']});
		slides[which].set('tween', {duration: $G['anim_speed']});
		
		slides[actual].fade('out');
		slides[which].fade('in');
		
		if(wrapper.getElement(".gkIsText")) gk_is_yourshop_text_anim(wrapper, contents, which, $G);	
			
		switch($G['anim_type']){
			case 'opacity': break;
			case 'top': new Fx.Tween(slides[which],{property:'margin-top', duration: $G['anim_speed']}).start((-1)*slides[which].getSize().y,0);break;
			case 'left': new Fx.Tween(slides[which],{property:'margin-left', duration: $G['anim_speed']}).start((-1)*slides[which].getSize().x,0);break;
			case 'bottom': new Fx.Tween(slides[which],{property:'margin-top', duration: $G['anim_speed']}).start(slides[which].getSize().y,0);break;
			case 'right': new Fx.Tween(slides[which],{property:'margin-left', duration: $G['anim_speed']}).start(slides[which].getSize().x,0);break;
		}
				
		(function(){slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);}).delay($G['anim_speed']);
	}
}