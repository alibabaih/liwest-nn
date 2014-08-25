var $this = [];

window.addEvent('load', function() {
    $$(".gkIsWrapper-gk_black_and_white").each(function(el){
        var elID = el.getProperty('id');
        new GK_IS_oct2010_12($Gavick[elID], el, elID);
    });
});

var GK_IS_oct2010_12 = new Class({
    
    options: {
        "anim_type" : "opacity", // opacity, stripes-top, stripes-height, stripes-opacity-top, stripes-opacity-height, stripes-mixed 
        "anim_interval" : 5000,
        "anim_speed" : 500,
        "stripe_width" : 20,
        "slide_links" : true
    },
    
    initialize: function(options, el, elID) {
 		if(!el.hasClass('activated')) {
 			el.addClass('activated');
	        this.setOptions(options);
	        $this[elID] = this;
	        this.loadedImages = false;
	        this.wrapper = document.id(elID);   
	        this.slides = [];
	        this.contents = [];
	        this.links = [];
	        this.play = false;
	        this.$blank = false;
	        this.overlayAnim = false;
	        this.actual_slide = 0;
	        this.stripes = [];
	        this.wrap_width = this.wrapper.getElement('.gkIsSlides').getSize().x;
	        this.wrap_height = this.wrapper.getElement('.gkIsSlides').getSize().y;
	        this.text = this.wrapper.getElement('.gkIsText');
	        this.blank = false;
	        this.pagination = this.wrapper.getElement('.gkIsPagination');
	        this.playing = false;
	        
	        this.wrapper.getElements('.gkIsTextItem').each(function(el){
	            $this[elID].links.push(el.getElement('h4 a').getProperty('href'));
	        });
	        
	        if(this.wrapper.getElement('.gkIsTextItem h4 a.gkToRemove')) {
	        	this.wrapper.getElements('.gkIsTextItem h4 a.gkToRemove').each(function(el) {
	        		var text = el.innerHTML;
	        		var parent = el.getParent();
	        		parent.innerHTML = text;
	        	});
	        }
	        
	        if(this.text) this.text_anim(0, elID);
	        if(this.pagination) {
	            this.pagination_active(0, elID);
	            this.pagination.getElements('li').each(function(li, i) {
	                li.addEvent('click', function(){
	                    if(!$this[elID].playing) {
	                        $this[elID].blank = true;
	                        $this[elID].anim(i, elID);
	                    }
	                });
	            });
	        }
	        
	        var imagesToLoad = [];
	        var amount = this.wrapper.getElements('.gkIsSlide').length;
	        
	        this.wrapper.getElements('.gkIsSlide').each(function(elm,i){
	            $this[elID].links.push(elm.innerHTML);
	            var newImg = new Element('img', { 'class' : 'gkIsSlide', 'alt' : '', 'src' : elm.innerHTML, 'style' : 'z-index:' + (amount - i) });
	            imagesToLoad.push(newImg);
	            newImg.inject(elm, 'after');
	            elm.destroy();
	        });
	        
	        if($this[elID].options.anim_type != 'opacity') this.init_stripes(elID);
	              
	        var time = (function(){
	            var process = 0;                
	            imagesToLoad.each(function(el,i){ if(el.complete) process++; });
	            
	            if(process == imagesToLoad.length){
	                $clear(time);
	                $this[elID].loadedImages = process;
	                (function(){
	                    $this[elID].wrapper.getElement('.gkIsPreloader').fade('out'); 
	                    $this[elID].wrapper.getElements('.gkIsSlide').each(function(slide,i){
	                    	$this[elID].slides.push({
	                    		"slide":slide, 
	                    		"anim": new Fx.Tween(slide, {property: 'opacity', duration: $this[elID].options['anim_speed'] }).set(i != 0 ? 0 : 1)
	                    	});
	                    	if($this[elID].options.slide_links) slide.addEvent('click', function() { $this[elID].redirect($this[elID].actual_slide, elID); });
	                    });
	                }).delay(400);
	            }
	        }).periodical(200);
	        
	        if(this.options['autoanim']){
	            (function(){
	                if($this[elID].blank == false){
	                    $this[elID].anim($this[elID].actual_slide+1, elID);
	                }else $this[elID].blank = false;
	            }).periodical($this[elID].options['anim_interval']+$this[elID].options['anim_speed']);
	        }
	    }
    },
    // down top anim
    init_stripes: function(elID) {
		// create stripes
        var amount_of_stripes = Math.ceil($this[elID].wrap_width / $this[elID].options.stripe_width);
		
		for(var i = 0; i < amount_of_stripes; i++) {
            var stripe = new Element('div', { 'class' : 'gkIsStripe' });
            stripe.setStyles({ 'left' : i * $this[elID].options.stripe_width + "px", 'width' : $this[elID].options.stripe_width + "px" });
			stripe.injectInside($this[elID].wrapper.getElement('.gkIsSlides'));
			
			if($this[elID].options.slide_links) stripe.addEvent('click', function() { $this[elID].redirect($this[elID].actual_slide, elID); });
			
			$this[elID].stripes.push({
				"stripe" : stripe, 
				"anim_top" : new Fx.Tween(stripe, { property: 'top', 
					duration: $this[elID].options.anim_speed - ((amount_of_stripes - i) * (Math.floor($this[elID].options.anim_speed / amount_of_stripes)))
				}).set(($this[elID].options.anim_type !== 'stripes-mixed') ? (($this[elID].options.anim_type == 'stripes-opacity-height' || $this[elID].options.anim_type == 'stripes-height') ? 0 : -$this[elID].wrap_height) : (i%2 == 1 ? -$this[elID].wrap_height : $this[elID].wrap_height)  ), 
				"anim_height" : new Fx.Tween(stripe, { property: 'height', 
					duration: $this[elID].options.anim_speed - ((amount_of_stripes - i) * (Math.floor($this[elID].options.anim_speed / amount_of_stripes)))
				}).set(($this[elID].options.anim_type == 'stripes-opacity-height' || $this[elID].options.anim_type == 'stripes-height') ? 0 : $this[elID].wrap_height),
				"anim_opacity" : new Fx.Tween(stripe, { property: 'opacity',
					duration: $this[elID].options.anim_speed				
				}).set(($this[elID].options.anim_type == 'stripes-mixed' || $this[elID].options.anim_type == 'stripes-opacity-height' || $this[elID].options.anim_type == 'stripes-opacity-top') ? 0 : 1)
			});
		}
    },
    
    text_anim: function(which, elID) {
        var max = $this[elID].slides.length-1;
        which = (which > max) ? 0 : ((which < 0) ? max : which);
        
        $this[elID].wrapper.getElement('.gkIsText').innerHTML = $this[elID].wrapper.getElements('.gkIsTextItem')[which].innerHTML;
    },
    
    pagination_active: function(which, elID) {
        if($this[elID].pagination) {
            $this[elID].pagination.getElements('li').setProperty('class', '');
            $this[elID].pagination.getElements('li')[which].setProperty('class', 'active');
        }
    },
    
    anim: function(which, elID) {
        if(which != $this[elID].actual_slide){ 
            var max = $this[elID].slides.length-1;
            which = (which > max) ? 0 : ((which < 0) ? max : which);
            var actual = $this[elID].actual_slide;
            $this[elID].actual_slide = which;
            
            $this[elID].playing = true;
            
            if($this[elID].text) $this[elID].text_anim(which, elID);
            $this[elID].pagination_active(which, elID);
            // opacity anim
           	if($this[elID].options.anim_type == 'opacity') {
	            $this[elID].slides[actual].slide.setStyle("z-index",max+1);
	            $this[elID].slides[actual].anim.start(1,0);
	            $this[elID].slides[which].anim.start(0,1);
	            
	            (function(){
	                $this[elID].slides[$this[elID].actual_slide].slide.setStyle("z-index", $this[elID].actual_slide);
	                $this[elID].playing = false;
	            }).delay($this[elID].options['anim_speed']);
	        }
			// top anim
            if($this[elID].options.anim_type == 'stripes-top' || $this[elID].options.anim_type == 'stripes-opacity-top') {
       			$this[elID].stripes.each(function(item,i){
            		item.stripe.setStyle("background", "transparent url('" + $this[elID].slides[which].slide.getProperty('src') + "') " + (-1 * i * $this[elID].options.stripe_width) + "px 0");
           			item.anim_top.start(0); 
           			if($this[elID].options.anim_type == 'stripes-opacity-top') item.anim_opacity.start(1);
            	});
            	
            	(function(){
                    $this[elID].slides[actual].anim.set(0);
            		$this[elID].slides[which].anim.set(1);
            	    $this[elID].slides[$this[elID].actual_slide].slide.setStyle("z-index", $this[elID].actual_slide);
            	    
                     $this[elID].stripes.each(function(item){ 
            		 	item.anim_top.set(-$this[elID].wrap_height); 
            		 	if($this[elID].options.anim_type == 'stripes-opacity-top') item.anim_opacity.set(0);
            		 });
            		 
            		 $this[elID].playing = false;
            	}).delay($this[elID].options['anim_speed']);
            }
            // height anim
            if($this[elID].options.anim_type == 'stripes-height' || $this[elID].options.anim_type == 'stripes-opacity-height') {
            	$this[elID].stripes.each(function(item,i){
            		item.stripe.setStyle("background", "transparent url('" + $this[elID].slides[which].slide.getProperty('src') + "') " + (-1 * i * $this[elID].options.stripe_width) + "px 0");
           			item.anim_height.start($this[elID].wrap_height); 
            		if($this[elID].options.anim_type == 'stripes-opacity-height') item.anim_opacity.start(1);
            	});
            	
            	(function(){
            		$this[elID].slides[actual].anim.set(0);
            		$this[elID].slides[which].anim.set(1);
            		$this[elID].slides[$this[elID].actual_slide].slide.setStyle("z-index", $this[elID].actual_slide);
            	
            		 $this[elID].stripes.each(function(item){ 
            		 	item.anim_height.set(0);
            		 	if($this[elID].options.anim_type == 'stripes-opacity-height') item.anim_opacity.set(0); 
            		 	$this[elID].playing = false;
            		 });
            	}).delay($this[elID].options['anim_speed']);
            }
            // anim mixed	
            if($this[elID].options.anim_type == 'stripes-mixed') {
            	$this[elID].stripes.each(function(item,i){
            		item.stripe.setStyle("background", "transparent url('" + $this[elID].slides[which].slide.getProperty('src') + "') " + (-1 * i * $this[elID].options.stripe_width) + "px 0");
           			item.anim_top.start(0); 
           			item.anim_opacity.start(1);
            	});
            	
            	(function(){
            		$this[elID].slides[actual].anim.set(0);
            		$this[elID].slides[which].anim.set(1);
            		$this[elID].slides[$this[elID].actual_slide].slide.setStyle("z-index", $this[elID].actual_slide);
            	
            		 $this[elID].stripes.each(function(item,i){ 
            		 	item.anim_top.set(i%2 == 1 ? -$this[elID].wrap_height : $this[elID].wrap_height);
            		 	item.anim_opacity.set(0); 
            		 	$this[elID].playing = false;
            		 });
            	}).delay($this[elID].options['anim_speed']);
            }
        }
    },
    
    redirect: function(where, elID) {
        window.location = $this[elID].links[where];
    }
});

GK_IS_oct2010_12.implement(new Options);