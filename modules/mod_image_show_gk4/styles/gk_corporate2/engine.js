window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_corporate2").each(function(el){
		if(!el.hasClass('activated')) {
			el.addClass('activated');	
			var elID = el.getProperty("id");
			var wrapper = document.id(elID);
			var $G = $Gavick[elID];
			$G['actual_animation'] = false;
			$G['actual_slide'] = 0;
			var slides = [];
			var contents = [];
			var loadedImages = false;
			var btns = false;
			var btns_fx = false;
			var show_btns = false;
			var hovers = false;
			var hovers_fx = false;
	
			if(window.webkit) wrapper.getElement('.gkIsContent').setStyles({"margin-left":0,"margin-right":0});
	
			wrapper.getElements('.gkIsContent').setStyle('overflow',"hidden");
			wrapper.getElement('.gkIsContent').setStyle('height',wrapper.getSize().y+"px");
			wrapper.getElements('.gkIsArt').removeClass('gkUnvisible');
			wrapper.getElements('.gkIsArt').setOpacity(0);
			wrapper.getElement('.gkIsArt').setOpacity(1);
	
			switch($G['anim_type']){
				case 'opacity': break;
				case 'top': 
					wrapper.getElements('.gkIsArt').setStyle('margin-top',(-1) * wrapper.getSize().y);
					wrapper.getElement('.gkIsArt').setStyle('margin-top',0);
				break;		
				
				case 'bottom':  
					wrapper.getElements('.gkIsArt').setStyle('margin-top', wrapper.getSize().y);
					wrapper.getElement('.gkIsArt').setStyle('margin-top', 0);
				break;	
			}
			
			if(wrapper.getElement('.gkIsPrev')) {
				btns = [wrapper.getElement('.gkIsPrev'),wrapper.getElement('.gkIsNext')];
				hovers = [wrapper.getElement('.gkIsPrev div'), wrapper.getElement('.gkIsNext div')];
				btns_fx = [new Fx.Tween(btns[0],{property:'opacity', duration:350}),new Fx.Tween(btns[1],{property:'opacity', duration:350})];
				hovers[0].setStyle("display","block");
				hovers[1].setStyle("display","block");
				hovers_fx = [new Fx.Tween(hovers[0],{property:'opacity',duration:200}).set(0),new Fx.Tween(hovers[1],{property:'opacity',duration:200}).set(0)];
				btns[0].addEvent("mouseenter", function(){hovers_fx[0].start(1);});
				btns[0].addEvent("mouseleave", function(){hovers_fx[0].start(0);});
				btns[1].addEvent("mouseenter", function(){hovers_fx[1].start(1);});
				btns[1].addEvent("mouseleave", function(){hovers_fx[1].start(0);});
	
				if(window.getWidth() < wrapper.getSize().x + 80){
					btns[0].setStyles({'left': '10px', 'z-index' : 100});
					btns[1].setStyles({'right': '10px','z-index' : 100});
				}
			}
			
			var imagesToLoad = [];
			
			if(btns && btns[0].hasClass('anim')){
				btns_fx[0].set(0);
				btns_fx[1].set(0);
	
				wrapper.addEvent("mousemove",function(){
					show_btns = true
				});
	
				[wrapper.getElement('.gkIsPrev'), wrapper.getElement('.gkIsNext'), wrapper].each(function(el){
					el.addEvent("mouseenter", function(){
						show_btns = true;
						btns_fx[0].start(1);	
						btns_fx[1].start(1);	
					});
					
					el.addEvent("mouseleave", function(){
						show_btns = false;
						(function(){
							if(show_btns == false){
								btns_fx[0].start(0);
								btns_fx[1].start(0);
							}
						}).delay(1000);
					});	
				});
			}
			
			wrapper.getElements('.gkIsSlide').each(function(el,i){
				var newImg = new Element('img',{
					"title":el.getProperty('title'),
					"class":el.getProperty('class'),
					"style":el.getProperty('style')
				});
	
				newImg.setProperty("src",el.innerHTML);
				imagesToLoad.push(newImg);
				newImg.injectAfter(el);
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
						
						(function() {
							wrapper.getElement('.gkIsPreloader').setStyle('visibility', 'hidden');
						}).delay(500);
					}).delay(400);
				}
			}).periodical(200);
	
			var time_main = (function(){
				if(loadedImages){
					$clear(time_main);
					
					wrapper.getElements(".gkIsSlide").each(function(elmt,i){
						slides[i] = elmt;
					});
					
					slides.each(function(el,i){
						if(i != 0) el.setOpacity(0);
					});
	
					$G['actual_slide'] = 0;
					if(wrapper.getElement('.gkIsList')) wrapper.getElement('.gkIsList ul li').setProperty("class", "active");
					if($G['autoanimation']){
						$G['actual_animation'] = (function(){
							gk_is_corporate2_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
						}).periodical($G['anim_interval']+$G['anim_speed']);
					}
	
					if(btns){
						btns[0].addEvent("click", function(){
							$clear($G['actual_animation']);
							gk_is_corporate2_anim(wrapper, contents, slides, $G['actual_slide']-1, $G);
							if($G['autoanimation']){
								$G['actual_animation'] = (function(){
									gk_is_corporate2_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
								}).periodical($G['anim_interval']+$G['anim_speed']);
							}
						});
	
						btns[1].addEvent("click", function(){
							$clear($G['actual_animation']);
							gk_is_corporate2_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
							if($G['autoanimation']){
								$G['actual_animation'] = (function(){
									gk_is_corporate2_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
								}).periodical($G['anim_interval']+$G['anim_speed']);						
							}
						});
					}
	
					if(wrapper.getElement('.gkIsList')){
						wrapper.getElements('.gkIsList li').each(function(el,i){
							el.addEvent("click", function(){
								$clear($G['actual_animation']);
								gk_is_corporate2_anim(wrapper, contents, slides, i, $G);
	
								if($G['autoanimation']){
									$G['actual_animation'] = (function(){
										gk_is_corporate2_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
									}).periodical($G['anim_interval']+$G['anim_speed']);
								}
							});	
						});
					}
				}
			}).periodical(250);
		}
	});
});

function gk_is_corporate2_anim(wrapper, contents, slides, which, $G){
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
		
		wrapper.getElements('.gkIsArt')[actual].set('tween', {duration: $G['anim_speed'] / 2});
		wrapper.getElements('.gkIsArt')[which].set('tween', {duration: $G['anim_speed'] / 2});
		
		wrapper.getElements('.gkIsArt')[actual].fade('out');
		wrapper.getElements('.gkIsArt')[which].fade('in');
		
		switch($G['anim_type']){
				case 'opacity': break;
				case 'top': new Fx.Tween(wrapper.getElements('.gkIsArt')[actual],{property:'margin-top',duration: 0.25 * $G['anim_speed'], transitions:Fx.Transitions.Circ.easeOut}).start(0, wrapper.getSize().y);break;
				case 'bottom': new Fx.Tween(wrapper.getElements('.gkIsArt')[actual],{property:'margin-top', duration: 0.25 * $G['anim_speed'], transitions:Fx.Transitions.Circ.easeOut}).start(0, (-1) * wrapper.getSize().y);break;
		}

		switch($G['anim_type']){
			case 'opacity': break;
			case 'top': new Fx.Tween(wrapper.getElements('.gkIsArt')[which],{property:'margin-top',duration: 0.75 * ($G['anim_speed']), transitions:Fx.Transitions.Circ.easeOut}).start((-1)*wrapper.getSize().y,0);break;
			case 'bottom': new Fx.Tween(wrapper.getElements('.gkIsArt')[which],{property:'margin-top',duration: 0.75 * ($G['anim_speed']), transitions:Fx.Transitions.Circ.easeOut}).start(wrapper.getSize().y,0);break;
		}

		if(wrapper.getElement('.gkIsList')){
			wrapper.getElements('.gkIsList ul li').setProperty("class", "");
			wrapper.getElements('.gkIsList ul li')[which].setProperty("class", "active");
		}

		(function(){slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);}).delay($G['anim_speed']);
	}
}