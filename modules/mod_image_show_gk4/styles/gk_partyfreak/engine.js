window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_partyfreak").each(function(el){
		if(!el.hasClass('activated')) {
			el.addClass('activated');
			var elID = el.getProperty("id");
			var wrapper = document.id(elID);
			var $G = $Gavick[elID];
			var opacity = 0.75;
			var thumbs_array = wrapper.getElements('div.gkIsThumb');
			var slides = [];
			var contents = [];
			var links = [];
			var play = false;
			var $blank = false;
			var loadedImages = false;
			var fxscr = new Fx.Scroll(el.getElement('.gkIsThumbs'), {duration: 350, transition: Fx.Transitions.Expo.easeOut});
	
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
	
					if(wrapper.getElement(".gkIsText")){
						wrapper.getElements(".gkIsTextItem").each(function(elmt,i){ contents[i] = elmt.innerHTML; });
					}
	
					$G['actual_slide'] = 0;
	
					if(wrapper.getElement(".gkIsText")) wrapper.getElement(".gkIsText").innerHTML = contents[0];
	
					if($G['autoanim']){
						play = true;
						$G['actual_animation'] = (function(){
							if(play && $blank == false){
								gk_is_partyfreak_anim(wrapper, contents, slides, thumbs_array, $G['actual_slide']+1, $G);
								if(thumbs_array.length) fxscr.toElement(thumbs_array[$G['actual_slide']]);
							}else $blank = false;
						}).periodical($G['anim_interval']+$G['anim_speed']);
					}
	
					thumbs_array.each(function(thumb, i){
						thumb.addEvent("click", function(){
							gk_is_partyfreak_anim(wrapper, contents, slides, thumbs_array, i, $G);
							fxscr.toElement(thumbs_array[$G['actual_slide']]);
							$blank = true;
						});
					});
	
					if(thumbs_array[0]){
						$pos = 0;
						$slide = 0;	
						$slider = wrapper.getElement('.gkIsThumbsSlider1');
						$scroller = new Fx.Scroll($slider,{wait:true,duration:300});
						$total = $slider.getSize().y;
						$h = ($total+$G['thumbs_space']) / $G['thumbs_amount'];								
	
						wrapper.getElement('.gkIsBtnUp').addEvent("click", function(){
							if($slide > 0){
								$pos -= $h;
								$slide--;
								$scroller.start(0, $pos);
							}
						});
	
						wrapper.getElement('.gkIsBtnDown').addEvent("click", function(){
							if($slide < thumbs_array.length - $G['thumbs_amount']){
								$pos += $h;
								$slide++;
								$scroller.start(0, $pos);
							}	
						});
					}
				}
			}).periodical(1000);
		}
	});
});

function gk_is_partyfreak_text_anim(wrapper, contents, which, $G){
	var txt = wrapper.getElement(".gkIsText");
	new Fx.Tween(txt,{property:'opacity',duration: $G['anim_speed']/2}).start(1,0);
	(function(){
		new Fx.Tween(txt,{property:'opacity',duration: $G['anim_speed']/2}).start(0,1);
		txt.innerHTML = contents[which];
	}).delay($G['anim_speed']);
}

function gk_is_partyfreak_anim(wrapper, contents, slides, thumbs_array, which, $G){
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
		
		if(wrapper.getElement(".gkIsText")) gk_is_partyfreak_text_anim(wrapper, contents, which, $G);	

		switch($G['anim_type']){
			case 'opacity': break;
			case 'top': new Fx.Tween(slides[which],{property:'margin-top',duration: $G['anim_speed']}).start((-1)*slides[which].getSize().y,0);break;
			case 'left': new Fx.Tween(slides[which],{property:'margin-left',duration: $G['anim_speed']}).start((-1)*slides[which].getSize().x,0);break;
			case 'bottom': new Fx.Tween(slides[which],{property:'margin-top',duration: $G['anim_speed']}).start(slides[which].getSize().y,0);break;
			case 'right': new Fx.Tween(slides[which],{property:'margin-left',duration: $G['anim_speed']}).start(slides[which].getSize().x,0);break;
		}

		if(thumbs_array.length){
			thumbs_array[actual].setProperty('class','gkIsThumb');	
			thumbs_array[which].setProperty('class','gkIsThumb active');
		}
		(function(){slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);}).delay($G['anim_speed']);
	}
}