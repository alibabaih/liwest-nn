window.addEvent("load",function(){ 
	$$(".gkIsWrapper-gk_music").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var slides = wrapper.getElements('.gkIsSlide');
		var contents = [];
		var dates = [];
		var links = [];
		var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
		$G['pages_content'] = wrapper.getElements('.gkIsSlides');
		$G['pages_content_h'] = [];
		$G['pages'] = Math.ceil(slides.length / $G['spp']);
		// progress bar animation object
		if(wrapper.getElement('.gkIsProgress')) {
			$G['progressFx'] = {
				'opacity': new Fx.Tween(wrapper.getElement('.gkIsProgress'), {
					duration: $G['anim_speed'] / 2,
					property: 'opacity',
					transition: Fx.Transitions.linear,
					wait: 'cancel'
				}),
				'width': new Fx.Tween(wrapper.getElement('.gkIsProgress'), {
					duration: $G['anim_interval'],
					property: 'width',
					unit: '%',
					transition: Fx.Transitions.linear,
					wait: 'cancel'
				})
			}
		}
		//
		if(!loadedImages){
			var imagesToLoad = [];
			
			wrapper.getElements('.gkIsImage a').each(function(el,i){
				links.push(el.getProperty('href'));
				var newImg = new Element('img');
				newImg.setProperty('alt',el.getProperty('href'));
				newImg.setProperty("src",el.innerHTML);
				imagesToLoad.push(newImg);
				el.innerHTML = '';
				newImg.inject(el, 'inside');
			});
			
			$G['pages_content'].each(function(el) {
				$G['pages_content_h'] = 0;
				el.getElements('.gkIsSlide').each(function(elm) {
					if(elm.getSize().y > $G['pages_content_h']) {
						$G['pages_content_h'] = elm.getSize().y;
					}
				});
				
				$G['pages_content_h'] = $G['pages_content_h'] + 10;
				el.setStyle('height', $G['pages_content_h'] + "px");
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
				
				$G['actual_slide'] = 0;
				
				if(wrapper.getElement('.gkIsPagination li')) {
					wrapper.getElement('.gkIsPagination li').setProperty('class', 'active');
					
					wrapper.getElements('.gkIsPagination li').each(function(elm, i){
						elm.addEvent('click', function() {
							gk_is_music_anim(wrapper, contents, dates, slides, i, $G);
							if($G['autoanim']) gk_is_music_autoanim(wrapper, contents, dates, slides, $G);
						});
					});
				}
				
				if($G['autoanim']){
					if(wrapper.getElement('.gkIsProgress')) {
						$G['progressFx'].width.start(100);
					}
					
					$G['actual_animation'] = (function(){
						gk_is_music_anim(wrapper, contents, dates, slides, $G['actual_slide'] + 1, $G);
						gk_is_music_autoanim(wrapper, contents, dates, slides, $G);
					}).delay($G['anim_interval']+$G['anim_speed']);
				}
			}
		}).periodical(250);
		
		if(wrapper.getElement('.gkIsLoader')) {
			var cursor = wrapper.getElement('.gkIsCursor');
			var loader = wrapper.getElement('.gkIsLoader');
			var center = Math.floor((loader.getSize().x - 37) / 2);
			var cursorFx = new Fx.Tween(cursor, { duration: 750, transition: Fx.Transitions.Elastic.easeOut });
			new Drag(cursor, {
				limit: {
					x: [0, loader.getSize().x - 37],
					y: [0, 0]
				},
				onComplete: function(element) {
					var start = element.getStyle('left').toInt();
					cursorFx.start('left', start, center);
					if(start >= center) gk_is_music_anim(wrapper, contents, dates, slides, $G['actual_slide'] + 1, $G);
					if(start < center) gk_is_music_anim(wrapper, contents, dates, slides, $G['actual_slide'] - 1, $G);
					
					if($G['autoanim']) gk_is_music_autoanim(wrapper, contents, dates, slides, $G);
				}
			});
			cursor.setStyle('left', center+"px");
		}
	});
});

function gk_is_music_autoanim(wrapper, contents, dates, slides, $G) {
	$clear($G['actual_animation']);
	$G['actual_animation'] = (function(){
		gk_is_music_anim(wrapper, contents, dates, slides, $G['actual_slide'] + 1, $G);
		gk_is_music_autoanim(wrapper, contents, dates, slides, $G);
	}).delay($G['anim_interval']+$G['anim_speed']);
}

function gk_is_music_anim(wrapper, contents, dates, slides, which, $G){	
	if(which != $G['actual_slide']){
		if(which > $G['pages'] - 1) which = 0;
		if(which < 0) which = $G['pages'] - 1;
		
		var actual = $G['actual_slide'];
		$G['actual_slide'] = which;
		$G['pages_content'][$G['actual_slide']].setStyle("z-index",$G['pages']+1);
		
		if(wrapper.getElement('.gkIsProgress')) {
			//$G['progressFx'].opacity.start(0);
			$G['progressFx'].width.stop();
			$G['progressFx'].width.set(0);
			
			(function() {
				//$G['progressFx'].opacity.start(1);
				$G['progressFx'].width.start(100);
			}).delay(($G['anim_speed'] / 2) + 25);
		}
		
		(function() {
			$G['pages_content'][actual].set('tween', { duration: $G['anim_speed'] / 2 });
			$G['pages_content'][actual].fade('out');
		}).delay($G['anim_speed'] / 2);
		
		$G['pages_content'][actual].getElements('.gkIsSlide').each(function(el,i) {
			(function() {
				new Fx.Tween(el, { duration: $G['anim_speed'] / 2}).start('margin-top', 0, $G['pages_content_h'] + 50);
				el.set('tween', { duration: $G['anim_speed'] / 2 });
				el.fade('out');
				el.addClass('gkIsHidden');
			}).delay(i * 80);
		});
		
		(function() {
			$G['pages_content'][which].getElements('.gkIsSlide').setStyles({
				'margin-top': ($G['pages_content_h'] + 50) + "px",
				'opacity': 0
			});
						
			$G['pages_content'][which].set('tween', { duration: $G['anim_speed'] / 2 });
			$G['pages_content'][which].fade('in');
			
			$G['pages_content'][which].getElements('.gkIsSlide').each(function(el,i) {
				(function() {
					new Fx.Tween(el, { duration: $G['anim_speed'] / 2 }).start('margin-top', 0);
					
					el.set('tween', { duration: $G['anim_speed'] / 2 });
					el.fade('in');
					
					if(el.hasClass('gkIsHidden')) el.removeClass('gkIsHidden');
				}).delay(i * 80);
			});
		}).delay($G['anim_speed']);
		
		if(wrapper.getElement('.gkIsPagination li')) {
			wrapper.getElements('.gkIsPagination li').setProperty('class', '');
			wrapper.getElements('.gkIsPagination li')[which].setProperty('class', 'active');
		}
		
		(function(){$G['pages_content'][$G['actual_slide']].setStyle("z-index",$G['actual_slide']);}).delay($G['anim_speed']);
	}
}