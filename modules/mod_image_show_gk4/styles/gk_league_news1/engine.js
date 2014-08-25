window.addEvent("load",function(){ 
	$$(".gkIsWrapper-gk_league_news1").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var slides = wrapper.getElements('.gkIsImage');
		var titles = wrapper.getElement('.gkIsTitle') ? wrapper.getElements('.gkIsTitle') : false;
		var contents = [];
		var dates = [];
		var links = [];
		var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
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
		slides.each(function(slide, i) { 
			slide.set('tween', {duration: $G['anim_speed']});
			
			if(i > 0) {
				slide.fade('out');
			}
		});

		if(titles) {
			titles.each(function(title, i) { 
				title.set('tween', {duration: $G['anim_speed']});
				if(i == 0) title.setStyle('bottom', 0);
			});
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
				
				wrapper.getElement('.gkIsMoreNews').addEvent('click', function(e) {
					e.stop();	
					wrapper.getElement('.gkMoreNews').setStyle('display', 'block');
					wrapper.getElements('.gkMoreNewsItem').setStyle('opacity', '0');
					wrapper.getElement('.gkMoreNews').set('tween', {duration: 300});
					wrapper.getElement('.gkMoreNews').fade('in');
					
					(function() {
						wrapper.getElements('.gkMoreNewsItem').each(function(el,i) {
							(function() { 
								el.fade('in');
							}).delay(i * 150);
						});
					}).delay(400);
				});
				
				wrapper.getElement('.gkMoreNews').addEvent('click', function(e) {
					wrapper.getElement('.gkMoreNews').set('tween', {duration: 300});
					wrapper.getElement('.gkMoreNews').fade('out');
					(function() {
						wrapper.getElement('.gkMoreNews').setStyle('display', 'none');
					}).delay(400);
				});
				
				var scrl = new Scroller(wrapper.getElement('.gkMoreNews div'), {area: 50});
				scrl.start();
				
				$G['actual_slide'] = 0;
				
				if($G['autoanim']){
					if(wrapper.getElement('.gkIsProgress')) {
						$G['progressFx'].width.start(100);
					}
					
					$G['actual_animation'] = (function(){
						gk_is_league_news1_anim(wrapper, contents, dates, slides, titles, $G['actual_slide'] + 1, $G);
						gk_is_league_news1_autoanim(wrapper, contents, dates, slides, titles, $G);
					}).delay($G['anim_interval']+$G['anim_speed']);
				}
			}
		}).periodical(250);
	});
});

function gk_is_league_news1_autoanim(wrapper, contents, dates, slides, titles, $G) {
	$clear($G['actual_animation']);
	$G['actual_animation'] = (function(){
		gk_is_league_news1_anim(wrapper, contents, dates, slides, titles, $G['actual_slide'] + 1, $G);
		gk_is_league_news1_autoanim(wrapper, contents, dates, slides, titles, $G);
	}).delay($G['anim_interval']+$G['anim_speed']);
}

function gk_is_league_news1_anim(wrapper, contents, dates, slides, titles, which, $G){	
	if(which != $G['actual_slide']){
		var max = slides.length-1;
		if(which > max) which = 0;
		if(which < 0) which = max;
		var actual = $G['actual_slide'];

		$G['actual_slide'] = which;
		slides[$G['actual_slide']].setStyle("z-index",max+1);
		
		slides[actual].fade('out');
		slides[which].fade('in');
		titles[actual].tween('bottom', -150);
		titles[which].tween('bottom', 0);
		
		if(wrapper.getElement('.gkIsProgress')) {
			$G['progressFx'].width.stop();
			$G['progressFx'].width.set(0);
			
			(function() {
				$G['progressFx'].width.start(100);
			}).delay(($G['anim_speed'] / 2) + 25);
		}
		
		(function(){
			slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);
		}).delay($G['anim_speed']);
	}
}