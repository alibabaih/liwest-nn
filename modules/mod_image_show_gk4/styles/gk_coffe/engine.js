window.addEvent("load",function(){
    $$(".gkIsWrapper-gk_coffe").each(function(el){
        var elID = el.getProperty("id");
        var wrapper = $(elID);
        var $G = $Gavick[elID];
        var slides = [];
        var contents = [];
        var links = [];
        var loadedImages = (wrapper.getElement('.gkIsPreloader')) ? false : true;
    
        var btns = false;
        if(wrapper.getElement('.gkIsPrev')) {
            btns = [wrapper.getElement('.gkIsPrev'),wrapper.getElement('.gkIsNext')];
        
            btns[0].setStyle('opacity',0);
            btns[1].setStyle('opacity',0);
            
            wrapper.addEvent("mouseenter", function(){
                new Fx.Tween(btns[0],{duration:300, property: 'opacity'}).start(1);
                new Fx.Tween(btns[1],{duration:300, property: 'opacity'}).start(1);
            });
            
            wrapper.addEvent("mouseleave", function(){
                new Fx.Tween(btns[0],{duration:300, property: 'opacity'}).start(0);
                new Fx.Tween(btns[1],{duration:300, property: 'opacity'}).start(0);         
            });
        }
        
        if(!loadedImages){
            var imagesToLoad = [];
            
            wrapper.getElements('.gkIsSlide').each(function(el,i){
                var newImg = new Element('img',{
                    "title":el.getProperty('title'),
                    "class":el.getProperty('class'),
                    "style":el.getProperty('style')
                });
                newImg.store('num', i);
                links[i] = el.getElement('a').getProperty('href');
                el.getElement('a').destroy();
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
                        elmt.addEvent("click", function(e){ 
                            window.location = links[$(e.target).retrieve('num')]; 
                        });
                        elmt.setStyle("cursor", "pointer");
                    }
                });
                
                slides.each(function(el,i){
                    if(i != 0) { 
                        el.setOpacity(0);
                        el.setStyle('display', 'none');
                    }
                });
                
                if(wrapper.getElement(".gkIsText")){
                    wrapper.getElements(".gkIsTextItem").each(function(elmt,i){
                        contents[i] = elmt.innerHTML;
                    });
                }
                
                $G['actual_slide'] = 0;
                
                if(wrapper.getElements(".gkIsText")[0]) {
                    wrapper.getElements(".gkIsText")[0].innerHTML = contents[0];
                }
                
                if($G['autoanim']){
                    $G['actual_animation'] = (function(){
                        gk_is_gk_coffe_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
                    }).periodical($G['anim_interval']+$G['anim_speed']);
                }
                
                if(btns){
                    btns[0].addEvent("click", function(){
                        $clear($G['actual_animation']);
                        
                        gk_is_gk_coffe_anim(wrapper, contents, slides, $G['actual_slide']-1, $G);
                        
                        $G['actual_animation'] = (function(){
                            gk_is_gk_coffe_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
                        }).periodical($G['anim_interval']+$G['anim_speed']);
                    });
                    
                    btns[1].addEvent("click", function(){
                        $clear($G['actual_animation']);
                        
                        gk_is_gk_coffe_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
                        
                        $G['actual_animation'] = (function(){
                            gk_is_gk_coffe_anim(wrapper, contents, slides, $G['actual_slide']+1, $G);
                        }).periodical($G['anim_interval']+$G['anim_speed']);                        
                    });
                }
            }
        }).periodical(250);
    });
});

function gk_is_gk_coffe_anim(wrapper, contents, slides, which, $G){
    if(which != $G['actual_slide']){
        var max = slides.length-1;
        if(which > max) which = 0;
        if(which < 0) which = max;
        var actual = $G['actual_slide'];
        
        $G['actual_slide'] = which;
		
		slides[actual].set('tween', {duration: $G['anim_speed']});
		slides[which].set('tween', {duration: $G['anim_speed']});
		
		slides[actual].fade('out');
		(function(){ slides[actual].setStyle('display', 'none') }).delay($G['anim_speed'] + 50);
        slides[which].setStyle('display', 'block');
		slides[which].fade('in');  
            
        switch($G['anim_type']){
            case 'opacity': break;
            case 'top': new Fx.Tween(slides[which], {duration: $G['anim_speed'], property: 'margin-top' }).start((-1)*slides[which].getSize().y,0);break;
            case 'left': new Fx.Tween(slides[which], {duration: $G['anim_speed'], property: 'margin-left' }).start((-1)*slides[which].getSize().x,0);break;
            case 'bottom': new Fx.Tween(slides[which], {duration: $G['anim_speed'], property: 'margin-top' }).start(slides[which].getSize().y,0);break;
            case 'right': new Fx.Tween(slides[which], {duration: $G['anim_speed'], property: 'margin-left' }).start(slides[which].getSize().x,0);break;
        }
        
        var txt = wrapper.getElement(".gkIsText");
        if(txt) {
            txt.innerHTML = contents[which];
        }
                
        (function(){
            slides[$G['actual_slide']].setStyle("z-index",$G['actual_slide']);
        }).delay($G['anim_speed']);
    }
}