window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_appsite1").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var slides = [];
		var contents = [];
		var dates = [];
		var links = [];
		var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
		var blankPrev = false;
		var blankNext = false;
		$G["base_x"] = 0;
		$G["base_y"] = 0;
		var progressOpacity = new Fx.Tween(wrapper.getElement('.gkIsProgress'), {property: 'opacity', duration: $G['anim_speed']});
		var progressFx = new Fx.Tween(wrapper.getElement('.gkIsProgress'), {property: 'width', duration: $G['anim_interval'], unit: '%', onComplete: function() {
				progressOpacity.start(0);
				
				(function(){ 
					gk_is_appsite1_anim(wrapper, contents, dates, slides, $G['actual_slide']+1, $G);
				}).delay($G['anim_speed'] + 50);
				
				(function(){
					progressFx.start(0, 100);
					progressOpacity.start(1);
				}).delay($G['anim_speed'] * 2 + 50);
			}
		}).set(0);
		
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
				
				$G["base_x"] = slides[0].getSize().x;
				$G["base_y"] = slides[0].getSize().y;
				
				slides.each(function(el,i){
					if(i != 0) el.setOpacity(0);
				});
				
				if(wrapper.getElement(".gkIsText")){
					wrapper.getElements(".gkIsTextItem").each(function(elmt,i){
						contents[i] = elmt.innerHTML;
					});
				}
				
				$G['actual_slide'] = 0;
				
				if(wrapper.getElement(".gkIsText")) {
					wrapper.getElement(".gkIsText").innerHTML = contents[0];
				}
				
				progressFx.start(0, 100);
				
				if(wrapper.getElement(".gkIsNext")) {
					var nextFx = new Fx.Tween(wrapper.getElement(".gkIsNext"), {property:'opacity'}).set(0);
					var prevFx = new Fx.Tween(wrapper.getElement(".gkIsPrev"), {property:'opacity'}).set(0);
					
					wrapper.addEvent('mouseenter', function() {
						nextFx.start(1);
						prevFx.start(1);
					});
					
					wrapper.addEvent('mouseleave', function() {
						nextFx.start(0);
						prevFx.start(0);
					});
					
					wrapper.getElement(".gkIsNext").addEvent('click', function() {	
						if(!blankNext) {
							blankNext = true;					
							progressOpacity.start(0);
							progressFx.pause();
							
							(function(){ 
								gk_is_appsite1_anim(wrapper, contents, dates, slides, $G['actual_slide']+1, $G);
							}).delay($G['anim_speed'] + 50);
							
							(function(){
								progressFx.start(0, 100);
								progressOpacity.start(1);
								blankNext = false;
							}).delay($G['anim_speed'] * 2 + 50);
						}
					});
					
					wrapper.getElement(".gkIsPrev").addEvent('click', function() {
						if(!blankPrev) {
							blankPrev = true;
							progressOpacity.start(0);
							progressFx.pause();
							
							(function(){ 
								gk_is_appsite1_anim(wrapper, contents, dates, slides, $G['actual_slide']-1, $G);
							}).delay($G['anim_speed'] + 50);
							
							(function(){
								progressFx.start(0, 100);
								progressOpacity.start(1);
								blankPrev = false;
							}).delay($G['anim_speed'] * 2 + 50);
						}
					});
				}
			}
		}).periodical(250);
	});
});

function gk_is_appsite1_anim(wrapper, contents, dates, slides, which, $G){
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
		
		var txt = wrapper.getElement(".gkIsText");
		
		if(txt) {
			new Fx.Morph(txt, {duration: $G['anim_speed'], onComplete: function() { 
				txt.innerHTML = contents[which]; 
			}}).start({
				'opacity': 0,
				'margin-top': [0,30]
			});
			
			(function() {
				new Fx.Morph(txt, {duration: $G['anim_speed']}).start({
					'opacity': 1,
					'margin-top': [-45, 0]
				});
			}).delay($G['anim_speed'] + 50);
		}
				
		(function(){
			slides[$G['actual_slide']].setStyle("z-index", $G['actual_slide']);
		}).delay($G['anim_speed']);
	}
}