window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_game_magazine").each(function(el){
		if(!el.hasClass('activated')) {
			el.addClass('activated');
			var elID = el.getProperty("id");
			var wrapper = document.id(elID);
			var $G = $Gavick[elID];
			var opacity = 0.75;
			var links_array = wrapper.getElements('.gkIsListItem');
			var slides = [];
			var contents = [];
			var links = [];
			var play = false;
			var $blank = false;
			var loadedImages = false;
			var fxscr = new Fx.Scroll(el.getElement('.gkIsListSlider'), {duration: 350, transition: Fx.Transitions.Expo.easeOut});
			var scrollData = {};
			
			var textBlockEnabled = false;
			$G['baseBottom'] = 0;
			$G['baseLeft'] = 0;
	
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
					newImg.inject(el,'after');
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
	
					if(wrapper.getElements(".gkIsTextItem")) {
						textBlockEnabled = true;
						contents = wrapper.getElements(".gkIsTextItem");
						$G['baseBottom'] = contents[0].getStyle('bottom').toInt();
						$G['baseLeft'] = contents[0].getStyle('bottom').toInt();
						contents.each(function(el, i) {
							if(i != 0) {
								if($G["anim_text_type"] == 'bottom') {
									el.setStyle('bottom', "-200px");
								}
								
								if($G["anim_text_type"] == 'left') {
									el.setStyle('left', "-600px");
								}
								
								el.setStyle('opacity', 0);
							}
						});
					}
	
					$G['actual_slide'] = 0;
					links_array[0].setProperty('class','gkIsListItem active');
					fxscr.set(0, 0);
					
					scrollData.itemH = links_array[0].getSize().y;
					scrollData.scrollH = wrapper.getElement('.gkIsListContent').getSize().y;
					scrollData.wrapH = wrapper.getElement('.gkIsListSlider').getSize().y;
					
					if($G['autoanim']){
						play = true;
						$G['actual_animation'] = (function(){
							if(play && $blank == false){
								gk_is_game_magazine_anim(wrapper, contents, slides, links_array, $G['actual_slide']+1, $G);
								fxscr.start(0, (((scrollData.itemH * $G['actual_slide']) - scrollData.wrapH) + (scrollData.itemH)));
							}else $blank = false;
						}).periodical($G['anim_interval']+$G['anim_speed']);
					}
	
					links_array.each(function(thumb, i) {
						thumb.addEvent("click", function(e){
							e.stop();
							gk_is_game_magazine_anim(wrapper, contents, slides, links_array, i, $G);
							fxscr.start(0, (((scrollData.itemH * $G['actual_slide']) - scrollData.wrapH) + (scrollData.itemH)));
							$blank = true;
						});
					});
								
					wrapper.getElement('.gkIsBtnDown').addEvent("click", function(){
						if($G['actual_slide'] < links_array.length - 1) { 
							which = $G['actual_slide']+1;
							
							gk_is_game_magazine_anim(wrapper, contents, slides, links_array, which, $G);
							fxscr.start(0, (((scrollData.itemH * which) - scrollData.wrapH) + (scrollData.itemH)));
							
							$blank = true;
						}
					});

					wrapper.getElement('.gkIsBtnUp').addEvent("click", function(){
						if($G['actual_slide'] > 0) { 
							which = $G['actual_slide']-1;
							
							gk_is_game_magazine_anim(wrapper, contents, slides, links_array, which, $G);
							fxscr.start(0, (((scrollData.itemH * which) - scrollData.wrapH) + (scrollData.itemH)));
							$blank = true;
						}
					});
				}
			}).periodical(1000);
		}
	});
});
//
function gk_is_game_magazine_text_anim(wrapper, contents, actual, which, $G){
	if($G["anim_text_type"] == 'bottom') {
		new Fx.Tween(contents[actual],{
			property:'bottom',
			duration: $G['anim_speed']
		}).start(-200);
	}
	
	if($G["anim_text_type"] == 'left') {
		new Fx.Tween(contents[actual],{
			property:'left',
			duration: $G['anim_speed']
		}).start(-600);
	}
	
	new Fx.Tween(contents[actual],{
		property:'opacity',
		duration: $G['anim_speed']/2,
		transition: Fx.Transitions.Expo.easeOut
	}).start(0);
	
	(function(){
		if($G["anim_text_type"] == 'bottom') {
			new Fx.Tween(contents[which],{
				property:'bottom',
				duration: $G['anim_speed']/2
			}).start($G['baseBottom']);
		}
		
		if($G["anim_text_type"] == 'left') {
			new Fx.Tween(contents[which],{
				property:'left',
				duration: $G['anim_speed']/2
			}).start($G['baseLeft']);
		}
		
		new Fx.Tween(contents[which],{
			property:'opacity',
			duration: $G['anim_speed']/2,
			transition: Fx.Transitions.Expo.easeIn
		}).start(1);
	}).delay($G['anim_speed']/2);
}
//
function gk_is_game_magazine_anim(wrapper, contents, slides, links_array, which, $G){
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
		
		if(wrapper.getElement(".gkIsTextItem")) {
			gk_is_game_magazine_text_anim(wrapper, contents, actual, which, $G);	
		}

		switch($G['anim_type']){
			case 'opacity': break;
			case 'top': new Fx.Tween(slides[which],{property:'margin-top',duration: $G['anim_speed']}).start((-1)*slides[which].getSize().y,0);break;
			case 'left': new Fx.Tween(slides[which],{property:'margin-left',duration: $G['anim_speed']}).start((-1)*slides[which].getSize().x,0);break;
			case 'bottom': new Fx.Tween(slides[which],{property:'margin-top',duration: $G['anim_speed']}).start(slides[which].getSize().y,0);break;
			case 'right': new Fx.Tween(slides[which],{property:'margin-left',duration: $G['anim_speed']}).start(slides[which].getSize().x,0);break;
		}

		if(links_array.length){
			links_array[actual].setProperty('class','gkIsListItem');	
			links_array[which].setProperty('class','gkIsListItem active');
		}
		
		(function(){
			slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);
		}).delay($G['anim_speed']);
	}
}