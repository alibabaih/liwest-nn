window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_financial_business").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var slides = [];
		var contents = [];
		var links = [];
		var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
		var $blank = false;
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
				
				if(wrapper.getElement('.gkIsTextInterface span')) {
					wrapper.getElement('.gkIsTextInterface span').setProperty('class', 'active');
					
					wrapper.getElements('.gkIsTextInterface span').each(function(elm, i){
						elm.addEvent('click', function() {
							gk_is_financial_business_anim(wrapper, contents, slides, progressFx, i, $G);
							progressFx.options.duration = progressFx.options.duration * 1.5; 
							$blank = true;
						});
					});
				}
				
				if($G['autoanim']){
					progressFx.start(0, 100);
					
					$G['actual_animation'] = (function(){
						if(!$blank) {
							gk_is_financial_business_anim(wrapper, contents, slides, progressFx, $G['actual_slide']+1, $G);
						} else {
							$blank = false;
						}
					}).periodical($G['anim_interval']+$G['anim_speed']);
				}
			}
		}).periodical(250);
	});
});

function gk_is_financial_business_anim(wrapper, contents, slides, progressFx, which, $G){
	if(which != $G['actual_slide']){
		var progressOpacity = new Fx.Tween(wrapper.getElement('.gkIsProgress'), { property: 'opacity', duration: $G['anim_speed'] - 50, wait:false }).start(0);
		
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
		
		if(wrapper.getElement('.gkIsTextInterface span')) {
			wrapper.getElements('.gkIsTextInterface span').setProperty('class', '');
			wrapper.getElements('.gkIsTextInterface span')[which].setProperty('class', 'active');
		}
			
		(function(){
			progressOpacity.start(0, 1);	
			slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);
			progressFx.stop();
			progressFx.start(0, 100);	
		}).delay($G['anim_speed']);
	}
}