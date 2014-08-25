Array.prototype.shuffle = function() {
	var i = this.length;
	while (i--) {
		var p = parseInt(Math.random() * this.length);
		var t = this[i];
  		this[i] = this[p];
  		this[p] = t;
 	}
};

window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_eSport").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		$G['blank'] = false;
		$G['focus'] = true;
		$G['scroll'] = null;
		$G['baseTextY'] = 0;
		$G['baseThumbsY'] = (wrapper.getElement('.gkIsThumbnails')) ? wrapper.getElement('.gkIsThumbnails').getSize().y : 0;
		$G['mode'] = wrapper.hasClass('gkThumbsBottom') ? 'bottom' : 'top';
		$G['animState'] = 0;
		var slides = [];
		var contents = [];
		var links = [];
		var loadedImages = false;
		var preloader = wrapper.getElement('.gkIsPreloader');
		var current = 0;
		var textBlocks = wrapper.getElements('.gkIsTextTitle');
		var imagesToLoad = [];
		preloader.addClass('loading');
		// options related to the bubble animation
		var rowAmount = $G['row_amount'];
		var lvl2Divider = $G['lvl2_divider'];
		var maxAnim = $G['max_anim'];
		var animInterval = $G['anim_interval'];
		var animSpeed = $G['anim_speed'];
		// variables used in animation
		var squares, squareSizes, animOrder;
		// events to avoid artifacts
		$(window).addEvent('focus', function() { $G['focus'] = true; });
		$(window).addEvent('blur', function() { $G['focus'] = false; });
		//
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
		
		if(wrapper.hasClass('gkOpacity')) {
			(function(){
				var time_main = (function(){
					if(loadedImages){
						$clear(time_main);
						preloader.removeClass('loading')
						preloader.addClass('loaded');
						// prepare an block area
						var main = wrapper.getElement('.gkIsImageAnimArea');
						// creating area of elements
						slides = wrapper.getElements(".gkIsSlide");
						slides.each(function(el,i){
						     el.setStyles({
						        'display': 'none',
						        'opacity': 0,
						        'visibility': 'hidden',
						        'right': 0,
						        'bottom': 0
						     });
						});
						// slide links					
						if($G['slide_links']){
							wrapper.getElement('.gkIsImage').addEvent("click", function(){
								window.location = imagesToLoad[$G['actual_slide']].getProperty('alt');
							});
							main.setStyle("cursor", "pointer");
						}
						// text block animation
						textBlocks.each(function(el,i){
							if(i != 0) el.setStyle('opacity', 0);
							else el.setStyle('opacity', 1);
						});
						
						$G['actual_slide'] = 0;
						
						gk_is_eSport(wrapper, contents, slides, textBlocks, 'init', squares, squareSizes, animOrder, imagesToLoad, $G);
					}
				}).periodical(250);
			}).delay(2000);
		} else {
			(function(){
				var time_main = (function(){
					if(loadedImages){
						$clear(time_main);
						preloader.removeClass('loading')
						preloader.addClass('loaded');
						// prepare an block area
						var main = wrapper.getElement('.gkIsImageAnimArea');
						// calculate square width
						var a = Math.ceil(main.getSize().y / rowAmount);
						// calculate amount of columns
						var columnAmount = Math.ceil(main.getSize().x / a);
						// create the squares
						squares = [];
						// in loop
						for(var i = 0; i < rowAmount; i++) {
							for(var j = 0; j < columnAmount; j++) {
								var div = new Element('div', {
									style: "left: " + (j * a) + "px;top: " +(i * a) + "px;height: "+ a + "px;width: "+ a + "px;"
								});
								
								div.inject(main);
								squares.push(div);
							}
						}
						// get random squares to divide for smaller squares
						if(!Browser.ie8) {
							var drawed = [];
							var drawedIndex = [];
							var aLvl2 = Math.ceil(a / 2);
							// loop
							for(var i = 0; i < lvl2Divider; i++) {
								var index = Number.random(0, squares.length - 1);
								
								while(drawedIndex.contains(index)) {
									index = Number.random(0, squares.length - 1);
								}
								
								drawedIndex.push(index);
							}
							// create smaller squares
							for(var x = 0; x < drawedIndex.length; x++) {	
								var baseTop = squares[drawedIndex[x]].getStyle('top').toInt();
								var baseLeft = squares[drawedIndex[x]].getStyle('left').toInt();
								
								for(var y = 0; y < 2; y++) {
									for(var z = 0; z < 2; z++) {	
										var div = new Element('div', {
											style: "left: "+ (baseLeft + (z * aLvl2)) + "px;top: " + (baseTop + (y * aLvl2)) + "px;height: "+ aLvl2 + "px;width:"+ aLvl2 + "px;"
										});
										
										div.inject(main);
										drawed.push(div);
									}
								}
							}
							// clear unnecessary squares 
							for(var x = 0; x < drawedIndex.length; x++) {
								squares[drawedIndex[x]].dispose();
								squares[drawedIndex[x]] = false;
							}
							// put the smaller squares to the squares array
							for(var n = 0; n < drawed.length; n++) {
								squares.push(drawed[n]);
							}
						}
						//
						// change squares to circles - R = (sqrt(2)/2) * a  ==>  width = 2R = sqrt(2) * a
						//
						// and create an stack of animations to made
						//
						squareSizes = [];
						animOrder = [];
						
						for(var m = 0; m < squares.length; m++) {
							if(squares[m] !== false) {
								var baseA = squares[m].getStyle('width').toInt();
								var newA = Math.ceil(Math.sqrt(2) * baseA);
								var diffA = Math.floor((newA - baseA) / 2);
								
								squareSizes[m] = {
									"a": newA,
									"top": squares[m].getStyle('top').toInt() - diffA,
									"left": squares[m].getStyle('left').toInt() - diffA
								};
								
								squares[m].setStyles({
									left: Math.ceil(squareSizes[m].left + (newA / 2)) + "px",
									top: Math.ceil(squareSizes[m].top + (newA / 2)) + "px",
									height: "0px",
									width: "0px",
									//opacity: 0,
									"background-position": -1 * (squareSizes[m].left + (newA / 2)) + "px " + -1 * (squareSizes[m].top + (newA / 2)) + "px"  
								});
								
								animOrder.push(m);
							}
						}
						// creating area of elements
						slides = wrapper.getElements(".gkIsSlide")
						// slide links					
						if($G['slide_links']){
							main.addEvent("click", function(){
								window.location = imagesToLoad[$G['actual_slide']].getProperty('alt');
							});
							main.setStyle("cursor", "pointer");
						}
						// text block animation
						textBlocks.each(function(el,i){
							if(i != 0) el.setStyle('opacity', 0);
							else el.setStyle('opacity', 1);
						});
						
						$G['actual_slide'] = 0;
						
						gk_is_eSport(wrapper, contents, slides, textBlocks, 'init', squares, squareSizes, animOrder, imagesToLoad, $G);
					}
				}).periodical(250);
			}).delay(2000);
		}
		
		if(wrapper.getElement('.gkIsThumbnails')) {
			wrapper.getElements('.gkIsThumbnailsWrap li').each(function(item, i) {
				item.addEvent('click', function() {
					if($G['actual_slide'] != i && $G['animState'] == 0) {
						$G['blank'] = true;
						gk_is_eSport(wrapper, contents, slides, textBlocks, i, squares, squareSizes, animOrder, imagesToLoad, $G);
					}
				});
			});
			wrapper.getElement('.gkIsThumbnailsWrap li').setProperty('class', 'active');
		}
		 
		if(wrapper.getElement('.gkIsTextTitle')) {
			if(wrapper.hasClass('gkThumbsBottom')) {
				$G['baseTextY'] = wrapper.getElement('.gkIsTextTitle').getStyle('bottom').toInt();
				wrapper.getElements('.gkIsTextTitle').setStyles({
					'visibility': 'visible',
					'opacity': 0//,
					//'bottom': "-100px"
				});
			} else {
				$G['baseTextY'] = wrapper.getElement('.gkIsTextTitle').getStyle('top').toInt();
				wrapper.getElements('.gkIsTextTitle').setStyles({
					'visibility': 'visible',
					'opacity': 0//,
					//'top': "-100px"
				});
			}
			
			//new Fx.Tween(wrapper.getElements('.gkIsTextTitle')[0]).start($G['mode'], -100, $G['baseTextY']);
		}
		
		if(wrapper.getElement('.gkIsThumbnails')) {
			$G['scroll'] = new Fx.Scroll(wrapper.getElement('.gkIsThumbnailsWrap'), { duration: 300, wheelStops: false }).set(0, 0);
			
			wrapper.getElement('.gkIsNext').addEvent('click', function(e) {
				e.stop();
				if($G['animState'] == 0) {
					$G['blank'] = true;
					gk_is_eSport(wrapper, contents, slides, textBlocks, $G['actual_slide']+1, squares, squareSizes, animOrder, imagesToLoad, $G);
				}
			});
			
			wrapper.getElement('.gkIsPrev').addEvent('click', function(e) {
				e.stop();
				if($G['animState'] == 0) {
					$G['blank'] = true;
					gk_is_eSport(wrapper, contents, slides, textBlocks, $G['actual_slide']-1, squares, squareSizes, animOrder, imagesToLoad, $G);
				}
			});
		} 
		
		if($G['autoanim']){	
			$G['actual_animation'] = (function(){
				if(!$G['blank'] && $G['focus']) {
					gk_is_eSport(wrapper, contents, slides, textBlocks, $G['actual_slide']+1, squares, squareSizes, animOrder, imagesToLoad, $G);
				} else {
					$G['blank'] = false;
				}
			}).periodical($G['slide_interval']+1000);
		}
	});
});

function gk_is_eSport(wrapper, contents, slides, textBlocks, which, squares, squareSizes, animOrder, imagesToLoad, $G){
	if(which != $G['actual_slide']){
		if(which == 'init') {
			which = 0;
			actual = 0;
			$G['actual_slide'] = 0;
			
			if(wrapper.hasClass('gkOpacity')) {
				slides[0].set('tween', {duration: $G['anim_speed']});
				slides[0].setStyle('display', 'block');
				slides[0].fade('in'); 
			} else {
				// shuffle the stack
				animOrder.shuffle();
				$G['animState'] = 1;
				// run the stack
				var k = 0;
				var timer = (function() {
					var numAnim = Number.random(1, $G['max_anim']);
					
					for(var l = 0; l < numAnim; l++) {
						if(k < animOrder.length) {
					  		squares[animOrder[k]].setStyle('background-image', 'url('+imagesToLoad[actual].getProperty('src')+')');
					  		new Fx.Morph(squares[animOrder[k]], { duration: $G['anim_speed'], transition: Fx.Transitions.Expo.easeInOut }).start({
					  			top: squareSizes[animOrder[k]].top,
					  			left: squareSizes[animOrder[k]].left,
					  			width: squareSizes[animOrder[k]].a,
					  			height: squareSizes[animOrder[k]].a,
					  			//opacity: 1,
					  			"background-position": -1 * (squareSizes[animOrder[k]].left) + "px " + -1 * (squareSizes[animOrder[k]].top) + "px"
					  		});
						} else {
							window.clearInterval(timer);
							(function() {
								$G['animState'] = 0;
							}).delay($G['anim_speed']);
						}
						
						k++;
					}
				}).periodical($G['anim_interval']);
			}
			 
			if(wrapper.getElement('.gkIsTextTitle')) {
				new Fx.Tween(wrapper.getElement('.gkIsTextTitle'), { transition: Fx.Transitions.Expo.easeIn }).start('opacity', 1);
			}
		} else {
			var max = slides.length-1;
			if(which > max) which = 0;
			if(which < 0) which = max;
			var actual = $G['actual_slide'];
			$G['actual_slide'] = which;
			
			if(wrapper.getElement('.gkIsThumbnails')) {
				wrapper.getElements('.gkIsThumbnailsWrap li').setProperty('class', '');
				wrapper.getElements('.gkIsThumbnailsWrap li')[$G['actual_slide']].setProperty('class', 'active');
				$G['scroll'].toElement(wrapper.getElements('.gkIsThumbnailsWrap li')[which]);
			}
			
			if(wrapper.hasClass('gkOpacity')) {
				slides[actual].set('tween', {duration: $G['anim_speed']});
				slides[which].set('tween', {duration: $G['anim_speed']});
				
				slides[actual].fade('out');
				(function(){ slides[actual].setStyle('display', 'none') }).delay($G['anim_speed'] + 50);
				slides[which].setStyle('display', 'block');
				slides[which].fade('in'); 
				
				if(wrapper.getElement('.gkIsTextTitle')) {
					//new Fx.Tween(wrapper.getElements('.gkIsTextTitle')[which]).start($G['mode'], $G['baseTextY']);
					new Fx.Tween(wrapper.getElements('.gkIsTextTitle', { transition: Fx.Transitions.Expo.easeIn })[which]).start('opacity', 1);
				}
			} else {
				//
				// shuffle the stack
				animOrder.shuffle();
				$G['animState'] = 1;
				// run the stack
				var k = 0;
				var timer = (function() {
					var numAnim = Number.random(1, $G['max_anim']);
					
					for(var l = 0; l < numAnim; l++) {
						if(k < animOrder.length) {
					  		squares[animOrder[k]].setStyle('background-image', 'url('+imagesToLoad[actual].getProperty('src')+')');
					  		new Fx.Morph(squares[animOrder[k]], { duration: $G['anim_speed'], transition: Fx.Transitions.Expo.easeInOut }).start({
					  			top: squareSizes[animOrder[k]].top + (squareSizes[animOrder[k]].a / 2),
					  			left: squareSizes[animOrder[k]].left + (squareSizes[animOrder[k]].a / 2),
					  			width: 0,
					  			height: 0,
					  			//opacity: 1,
					  			"background-position": -1 * (squareSizes[animOrder[k]].left + (squareSizes[animOrder[k]].a / 2)) + "px " + -1 * (squareSizes[animOrder[k]].top + (squareSizes[animOrder[k]].a / 2)) + "px"
					  		});
						} else {
							window.clearInterval(timer);
							(function() {
								// shuffle the stack
								animOrder.shuffle();
								//
								var kk = 0;
								var timerInner = (function() {
									var numAnim = Number.random(1, $G['max_anim']);
									
									for(var l = 0; l < numAnim; l++) {
										if(kk < animOrder.length) {
									  		squares[animOrder[kk]].setStyle('background-image', 'url('+imagesToLoad[which].getProperty('src')+')');
									  		new Fx.Morph(squares[animOrder[kk]], { duration: $G['anim_speed'], transition: Fx.Transitions.Expo.easeInOut }).start({
									  			top: squareSizes[animOrder[kk]].top,
									  			left: squareSizes[animOrder[kk]].left,
									  			width: squareSizes[animOrder[kk]].a,
									  			height: squareSizes[animOrder[kk]].a,
									  			//opacity: 1,
									  			"background-position": -1 * (squareSizes[animOrder[kk]].left) + "px " + -1 * (squareSizes[animOrder[kk]].top) + "px"
									  		});
										} else {
											window.clearInterval(timerInner);
											
											if(wrapper.getElement('.gkIsTextTitle')) {
												//new Fx.Tween(wrapper.getElements('.gkIsTextTitle')[which]).start($G['mode'], $G['baseTextY']);
												new Fx.Tween(wrapper.getElements('.gkIsTextTitle', { transition: Fx.Transitions.Expo.easeIn })[which]).start('opacity', 1);
											}
											
											(function(){
												$G['animState'] = 0;
											}).delay($G['anim_speed']);
										}
										
										kk++;
									}
								}).periodical($G['anim_interval']);
							}).delay($G['anim_speed']);
						}
						
						k++;
					}
				}).periodical($G['anim_interval']);
			}
			 
			if(wrapper.getElement('.gkIsTextTitle')) {
				//new Fx.Tween(wrapper.getElements('.gkIsTextTitle')[actual]).start($G['mode'], -100);
				new Fx.Tween(wrapper.getElements('.gkIsTextTitle', { transition: Fx.Transitions.Expo.easeOut })[actual]).start('opacity', 0);
			}
		}
	}
}