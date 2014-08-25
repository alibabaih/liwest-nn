window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_boutique").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var slides = [];
		var contents = [];
		var links = [];
		var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
		var $blank = false;
		var current = 0;
		var progressFx = new Fx.Tween(wrapper.getElement('.gkIsProgress'), { property: 'width', duration: $G['anim_interval'], unit: '%', wait:false, transition: Fx.Transitions.linear });
		
		if(!loadedImages){
			var imagesToLoad = [];
			
			wrapper.getElements('.gkIsSlide').each(function(el,i){
				links.push(el.getElement('a').getProperty('href'));
				var newImg = new Element('img',{
					"title":el.getProperty('title'),
					"class":el.getProperty('class'),
					"style":el.getProperty('style')
				});
				
				newImg.setProperty('alt',el.getChildren()[0].getProperty('href'));
				el.getElement('a').destroy();
				newImg.setProperty("src",el.innerHTML);
				imagesToLoad.push(newImg);
				newImg.inject(el, 'after');
				el.destroy();
			});
			
			var time = (function(){
				var process = 0;				
				imagesToLoad.each(function(el,i){
					if(el.complete) process++;
 				});
 				
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
				
				slides.each(function(el,i){
					if(i != 0) el.setStyle('opacity', 0);
				});
				
				if(wrapper.getElement(".gkIsTextTitle")){
					wrapper.getElements(".gkIsTextItem").each(function(elmt,i){
						contents[i] = elmt.innerHTML;
					});
				}
				
				$G['actual_slide'] = 0;
				
				if(wrapper.getElement(".gkIsTextTitle")) {
					wrapper.getElement(".gkIsTextTitle").innerHTML = contents[0];
				}
				
				if(wrapper.getElement('.gkIsInterface')) {
					wrapper.getElement('.gkIsInterface .gkIsPrev').addEvent('click', function() {
						if(current == 0) current = slides.length - 1;
						else current -= 1;
						
						gk_is_boutique_anim(wrapper, contents, slides, progressFx, current, $G);
						progressFx.options.duration = progressFx.options.duration * 1.5; 
						$blank = true;
					});
					
					wrapper.getElement('.gkIsInterface .gkIsNext').addEvent('click', function() {
						current += 1;
						if(current == slides.length) current = 0;
						
						gk_is_boutique_anim(wrapper, contents, slides, progressFx, current, $G);
						progressFx.options.duration = progressFx.options.duration * 1.5; 
						$blank = true;
					});
				}
				
				if($G['autoanim']){
					progressFx.start(0, 100);
					
					$G['actual_animation'] = (function(){
						if(!$blank) {
							gk_is_boutique_anim(wrapper, contents, slides, progressFx, $G['actual_slide']+1, $G);
							current += 1;
							if(current == slides.length) {
								current = 0;
							}
						} else {
							$blank = false;
						}
					}).periodical($G['anim_interval']+$G['anim_speed']);
				}
			}
		}).periodical(250);
	});
});

function gk_is_boutique_anim(wrapper, contents, slides, progressFx, which, $G){
	if(which != $G['actual_slide']){
		var progressOpacity = false;
		
		if($G['autoanim']){
			progressOpacity = new Fx.Tween(wrapper.getElement('.gkIsProgress'), { property: 'opacity', duration: $G['anim_speed'] - 50, wait:false }).start(0);
		}
		
		progressFx.options.duration = $G['anim_interval'];
		
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
			
		switch($G['anim_type']){
			case 'opacity': break;
			case 'top': new Fx.Tween(slides[which],{duration: $G['anim_speed']}).start('margin-top',(-1)*slides[which].getSize().size.y,0);break;
			case 'left': new Fx.Style(slides[which],{duration: $G['anim_speed']}).start('margin-left',(-1)*slides[which].getSize().size.x,0);break;
			case 'bottom': new Fx.Style(slides[which],{duration: $G['anim_speed']}).start('margin-top',slides[which].getSize().size.y,0);break;
			case 'right': new Fx.Style(slides[which],{duration: $G['anim_speed']}).start('margin-left',slides[which].getSize().size.x,0);break;
		}
		
		var txt = wrapper.getElement(".gkIsTextTitle");
		if(txt) txt.innerHTML = contents[which];
			
		(function(){	
			slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);
			if($G['autoanim']){
				progressOpacity.start(0, 1);
				progressFx.stop();
				progressFx.start(0, 100);	
			}
		}).delay($G['anim_speed']);
	}
}