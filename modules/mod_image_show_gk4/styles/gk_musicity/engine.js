window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_musicity").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var slides = [];
		var contents = [];
		var dates = [];
		var links = [];
		var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
		var $blank = false;
		
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
					if(i != 0) el.setOpacity(0);
				});
				
				if(wrapper.getElement(".gkIsTextTitle")){
					wrapper.getElements(".gkIsTextItem").each(function(elmt,i){
						contents[i] = elmt.innerHTML;
					});
				}
				
				if(wrapper.getElement(".gkIsDate")){
					wrapper.getElements(".gkIsDateItem").each(function(elmt,i){
						dates[i] = elmt.innerHTML;
					});
				}
				
				$G['actual_slide'] = 0;
				
				if(wrapper.getElement(".gkIsTextTitle")) {
					wrapper.getElement(".gkIsTextTitle").innerHTML = contents[0];
				}
				
				if(wrapper.getElement(".gkIsDate")) {
					wrapper.getElement(".gkIsDate").innerHTML = dates[0];
				}
				
				if(wrapper.getElement('.gkIsTextInterface span')) {
					wrapper.getElement('.gkIsTextInterface span').setProperty('class', 'active');
					
					wrapper.getElements('.gkIsTextInterface span').each(function(elm, i){
						elm.addEvent('click', function() {
							gk_is_musicity_anim(wrapper, contents, dates, slides, i, $G);
							$blank = true;
						});
					});
				}
				
				if($G['autoanim']){
					$G['actual_animation'] = (function(){
						if(!$blank) {
							gk_is_musicity_anim(wrapper, contents, dates, slides, $G['actual_slide']+1, $G);
						} else {
							$blank = false;
						}
					}).periodical($G['anim_interval']+$G['anim_speed']);
				}
			}
		}).periodical(250);
	});
});

function gk_is_musicity_anim(wrapper, contents, dates, slides, which, $G){
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
		
		switch($G['anim_type']){
			case 'opacity': break;
			case 'top': new Fx.Tween(slides[which],{property: 'margin-top', duration: $G['anim_speed']}).start((-1)*slides[which].getSize().size.y,0);break;
			case 'left': new Fx.Tween(slides[which],{property: 'margin-left', duration: $G['anim_speed']}).start((-1)*slides[which].getSize().size.x,0);break;
			case 'bottom': new Fx.Tween(slides[which],{property: 'margin-top', duration: $G['anim_speed']}).start(slides[which].getSize().size.y,0);break;
			case 'right': new Fx.Tween(slides[which],{property: 'margin-left', duration: $G['anim_speed']}).start(slides[which].getSize().size.x,0);break;
		}
		
		var txt = wrapper.getElement(".gkIsTextTitle");
		var date = wrapper.getElement('.gkIsDate');
		if(txt) txt.innerHTML = contents[which];
		if(date) date.innerHTML = dates[which];
		
		if(wrapper.getElement('.gkIsTextInterface span')) {
			wrapper.getElements('.gkIsTextInterface span').setProperty('class', '');
			wrapper.getElements('.gkIsTextInterface span')[which].setProperty('class', 'active');
		}
				
		(function(){slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);}).delay($G['anim_speed']);
	}
}