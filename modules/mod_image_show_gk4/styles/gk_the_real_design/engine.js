window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_the_real_design").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var slides = [];
		var contents = [];
		var links = [];
		var loadedImages = false;
		var preloader = wrapper.getElement('.gkIsPreloader');
		var $blank = false;
		var current = 0;
		var textBlocks = wrapper.getElements('.gkIsTextTitle');
		
		var imagesToLoad = [];
		preloader.getElement('span').setProperty('class', 'loading');
		
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
			}
		}).periodical(200);
		
		(function(){
			var time_main = (function(){
				if(loadedImages){
					preloader.getElement('span').setProperty('class', 'loaded');
					(function(){
						preloader.fade('out');
					}).delay(400);
					
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
						else el.setStyle('opacity', 1);
					});
					
					textBlocks.each(function(el,i){
						if(i != 0) el.setStyle('opacity', 0);
						else el.setStyle('opacity', 1);
					});
					
					new Fx.Tween(slides[0], {property:'right', duration: $G['anim_speed']}).start($G['image_x']);	
					new Fx.Tween(slides[0], {property:'bottom', duration: $G['anim_speed']}).start($G['image_y']);
					
					new Fx.Tween(textBlocks[0], {property:'left', duration: $G['anim_speed']}).start($G['text_x']);	
					new Fx.Tween(textBlocks[0], {property:'top', duration: $G['anim_speed']}).start($G['text_y']);
					
					$G['actual_slide'] = 0;
					
					if(wrapper.getElement('.gkIsInterface')) {
						wrapper.getElements('.gkIsInterface li').each(function(item, i) {
							item.addEvent('click', function() {
								if(current != i) {
									$blank = true;
									current = i;
									gk_is_the_real_design_anim(wrapper, contents, slides, textBlocks, i, $G);
								}
							});
						});
						wrapper.getElement('.gkIsInterface li').setProperty('class', 'active');
					}
					
					if($G['autoanim']){	
						$G['actual_animation'] = (function(){
							if(!$blank) {
								gk_is_the_real_design_anim(wrapper, contents, slides, textBlocks, $G['actual_slide']+1, $G);
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
		}).delay(2000);
	});
});

function gk_is_the_real_design_anim(wrapper, contents, slides, textBlocks, which, $G){
	if(which != $G['actual_slide']){
		var max = slides.length-1;
		if(which > max) which = 0;
		if(which < 0) which = max;
		var actual = $G['actual_slide'];
		
		if(wrapper.getElement('.gkIsInterface')) {
			wrapper.getElements('.gkIsInterface li').setProperty('class', '');
			wrapper.getElements('.gkIsInterface li')[which].setProperty('class', 'active');
		}
		
		$G['actual_slide'] = which;
		slides[$G['actual_slide']].setStyle("z-index",max+1);
		new Fx.Tween(slides[actual], {property:'right', duration: $G['anim_speed']}).start(-999);
		new Fx.Tween(slides[which], {property:'right', duration: $G['anim_speed']}).start(-999, $G['image_x']);	
		new Fx.Tween(slides[actual], {property:'bottom', duration: $G['anim_speed']}).start(999);
		new Fx.Tween(slides[which], {property:'bottom', duration: $G['anim_speed']}).start(-999, $G['image_y']);
		new Fx.Tween(slides[actual], {property:'opacity', duration: $G['anim_speed']/2}).start(1, 0);
		(function() {
			new Fx.Tween(slides[which], {property:'opacity', duration: $G['anim_speed']/2}).start(0, 1);
		}).delay($G['anim_speed']/2);
		
		new Fx.Tween(textBlocks[actual], {property:'left', duration: $G['anim_speed']}).start(-999);
		new Fx.Tween(textBlocks[which], {property:'left', duration: $G['anim_speed']}).start(-999, $G['text_x']);	
		new Fx.Tween(textBlocks[actual], {property:'top', duration: $G['anim_speed']}).start(999);
		new Fx.Tween(textBlocks[which], {property:'top', duration: $G['anim_speed']}).start(-999, $G['text_y']);	
		new Fx.Tween(textBlocks[actual], {property:'opacity', duration: $G['anim_speed']/2}).start(1, 0);
		(function() {
			new Fx.Tween(textBlocks[which], {property:'opacity', duration: $G['anim_speed']/2}).start(0, 1);
		}).delay($G['anim_speed']/2);
			
		(function(){	
			slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);
		}).delay($G['anim_speed']);
	}
}