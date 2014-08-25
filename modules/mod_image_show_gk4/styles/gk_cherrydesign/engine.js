window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_cherrydesign").each(function(el){
		if(!el.hasClass('activated')) {
			el.addClass('activated');		
			var elID = el.getProperty("id");
			var wrapper = $(elID);
			var $G = $Gavick[elID];
			var slides = [];
			var contents = [];
			var links = [];
			var play = false;
			var $blank = false;
			var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
			var overlay_anim = false;
			
			if(!loadedImages){
				var imagesToLoad = [];
				
				wrapper.getElements('.gkIsSlide').each(function(el,i){
					links.push(el.getFirst().getProperty('href'));
					var newImg = new Element('img',{
						"title":el.getProperty('title'),
						"class":el.getProperty('class'),
						"style":el.getProperty('style')
					});
					
					newImg.setProperty('alt',el.getChildren()[1].getProperty('href'));
					el.getChildren()[1].destroy();
					newImg.setProperty("src",el.getChildren()[0].getProperty('href'));
					el.getChildren()[0].destroy();
					imagesToLoad.push(newImg);
					newImg.injectAfter(el);
					el.destroy();
				});
				
				var time = (function(){
					var process = 0;				
					imagesToLoad.each(function(el,i){ if(el.complete) process++; });
	 				
					if(process == imagesToLoad.length){
						$clear(time);
						loadedImages = process;
						(function(){
							wrapper.getElement('.gkIsPreloader').fade('out');
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
							elmt.addEvent("click", function(){window.location = elmt.getProperty('alt');});
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
					
					if($G['autoanim']){
						play = true;
						$G['actual_animation'] = (function(){
							if(play && $blank == false){
								gk_is_gk_cherrydesign_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
							}else $blank = false;
						}).periodical($G['anim_interval']+$G['anim_speed']);
					}
					
					if(wrapper.getElement('.gkIsPagination li')){
	    				wrapper.getElement('.gkIsPagination li').setProperty('class','active');
	    				
	    				wrapper.getElements('.gkIsPagination li').each(function(el,i){
	    				   el.addEvent('click', function(e){
	    				        new Event(e).stop();
	    				        gk_is_gk_cherrydesign_anim(wrapper, contents, slides, i, $G);
	    				        wrapper.getElements('.gkIsPagination li').setProperty('class','');
	    				        el.setProperty('class','active');
	    				        $blank = true;
	    				   }); 
	    				});
					}
					
					if(wrapper.getElement('.gkIsTextData')) {
					    overlay_anim = new Fx.Tween(wrapper.getElement('.gkIsOverlay'), {duration:300, property: 'opacity', wait:false }).set(0);
					    
	                    wrapper.getElement('.gkIsImage').addEvent('mouseenter', function(e){
	                        new Event(e).stop();
	                        wrapper.getElement('.gkIsOverlay').setStyle('display','block');
	                        overlay_anim.start(0,1);
	                        wrapper.getElement('.gkIsOverlay').innerHTML = '<div class="gkIsTextover">' + wrapper.getElements('.gkIsTextData .gkIsTextItem')[$G['actual_slide']].innerHTML + '</div>';
	                        play = false;
	                    });
	                    
	                    wrapper.getElement('.gkIsImage').addEvent('mouseleave', function(e){
	                        new Event(e).stop();
	                        overlay_anim.start(0);
	                        (function(){ wrapper.getElement('.gkIsOverlay').setStyle('display','none'); }).delay(400);
	                        play = true;
	                        $blank = true;
					    });
					}
				}
			}).periodical(250);
		}
	});
});

function gk_is_gk_cherrydesign_text_anim(wrapper, contents, which, $G){
	var txt = wrapper.getElement(".gkIsText");
	new Fx.Tween(txt, {duration: $G['anim_speed']/2, property: 'opacity'}).start(1,0);
	(function(){
		new Fx.Tween(txt, {duration: $G['anim_speed']/2, property: 'opacity'}).start(0,1);
		txt.innerHTML = contents[which];
	}).delay($G['anim_speed']);
}

function gk_is_gk_cherrydesign_anim(wrapper, contents, slides, which, $G){
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
		
		if(wrapper.getElement(".gkIsText")) gk_is_gk_cherrydesign_text_anim(wrapper, contents, which, $G);	
			
		switch($G['anim_type']){
			case 'opacity': break;
			case 'top': new Fx.Tween(slides[which],{duration: $G['anim_speed'], property: 'margin-top'}).start((-1)*slides[which].getSize().size.y,0);break;
			case 'left': new Fx.Tween(slides[which],{duration: $G['anim_speed'], property: 'margin-left'}).start((-1)*slides[which].getSize().size.x,0);break;
			case 'bottom': new Fx.Tween(slides[which],{duration: $G['anim_speed'], property: 'margin-top'}).start(slides[which].getSize().size.y,0);break;
			case 'right': new Fx.Tween(slides[which],{duration: $G['anim_speed'], property: 'margin-left'}).start(slides[which].getSize().size.x,0);break;
		}
				
		(function(){slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);}).delay($G['anim_speed']);
		
		if(wrapper.getElement('.gkIsPagination li')) {
			wrapper.getElements('.gkIsPagination li').setProperty('class','');
			wrapper.getElements('.gkIsPagination li')[which].setProperty('class','active');
		}
	}
}