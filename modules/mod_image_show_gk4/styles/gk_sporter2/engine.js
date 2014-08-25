window.addEvent("load",function(){
	$$(".gkIsWrapper-gk_sporter2").each(function(el){
		var elID = el.getProperty("id");
		var wrapper = document.id(elID);
		var $G = $Gavick[elID];
		var thumbs_array = wrapper.getElements('.gkIsThumbs a');
		var images_array = wrapper.getElements('.gkIsImage');
		var popup_initialized = false;
		
		var popup_handler = wrapper.getElement('.gkIsPopup-gk_sporter2');
		if(popup_handler) popup_handler.inject(document.body, 'top');
		var overlay_handler = wrapper.getElement('.gkIsOverlay-gk_sporter2');
		if(overlay_handler) overlay_handler.inject(document.body, 'top');
		
		thumbs_array.each(function(elm,j){
			elm.addEvent("click", function(e){
				new Event(e).stop();
				gk_is_sporter2_popup(popup_handler, $G['width'], $G['height'], overlay_handler, images_array[j].getElement('.gkIsImagePath').innerHTML, images_array[j].getElement('.gkIsTextBlock'),j,images_array,popup_initialized);
				popup_initialized = true;
			});
		});
	});
});

function gk_is_sporter2_popup(popup_id, x, y, overlay, image, content, num, images_array, init){
	var p = popup_id;
 	var layer = p.getElement('.overlay');
 	var actual = num;
 	var prev = p.getElement('.next');
 	var next = p.getElement('.prev');
 	
  	prev.setStyle("top",(y - prev.getStyle("height").toInt()) / 2);
	next.setStyle("top",(y - next.getStyle("height").toInt()) / 2);
	layer.setStyle("height",y+"px");
	layer.setStyle("opacity", 1);
	init = true;
	next.removeEvents("click");
	prev.removeEvents("click");
	    
	next.addEvent("click", function(){
		if(actual == 0) actual = images_array.length - 1;
		else actual--;
		gk_is_sporter2_popup(popup_id, x, y, overlay, images_array[actual].getElement('.gkIsImagePath').innerHTML, images_array[actual].getElement('.gkIsTextBlock'), actual, images_array, init);
	});

	prev.addEvent("click", function(){
	    if(actual == images_array.length - 1) actual = 0;
		else actual++;
		gk_is_sporter2_popup(popup_id, x, y, overlay, images_array[actual].getElement('.gkIsImagePath').innerHTML, images_array[actual].getElement('.gkIsTextBlock'), actual, images_array, init);
	});
 	
	if(p.getStyle("display") != "block"){
		var img = new Asset.image(image,{onload:function(){
			layer.fade('out');
			p.getElement('.content').empty();
			document.id(this).inject(p.getElement('.content'), 'top');
			if(p.getElement('.text')) p.getElement('.text').empty();
			if(content){
				document.id(content).clone().inject(p.getElement('.text'), 'top');
				p.getElement('.text').setStyle("bottom","-"+p.getElement('.text').getStyle("height"));
				(function(){new Fx.Tween(p.getElement('.text'),{property:'bottom',duration:350}).start(0);}).delay(1000);
			} 
		}});
		
		p.setStyle("display","block");
		p.setStyle("left",(window.getSize().x / 2)+"px");
	  	if(overlay) overlay.setStyle("display","block");
	  
		var fintop = ((window.getSize().y / 2) + window.getScrollTop());
	  
		if(window.opera){
			fintop = ((window.innerHeight / 2) + window.getScrollTop());
		}
		
		new Fx.Tween(p,{property:'top',duration:350}).start(fintop+120,fintop);
		
		new Fx.Tween(p,{property:'opacity',duration:350}).start(1);
		if(overlay) new Fx.Tween(overlay,{property:'opacity',duration:350}).start(0.6);

		p.setStyles({
			"overflow":"hidden",
			"width":"40px",
			"height":"40px"
		});
		
		p.getElement('.m').setStyle("display","none");
		
		(function(){
			p.getElement('.m').setStyle("display","block");
			var t = (fintop-((y+40)/2)) < 50 ? 50 : (fintop-((y+40)/2));
			
			new Fx.Morph(p,{duration:200}).start({
				"width":x+40+"px",
				"height":y+40+"px",
				"left":((window.getSize().x-(x-40)) / 2)+"px",
				"top": t+"px"
			});
			new Fx.Tween(p.getElement('.t'),{property:'width',duration:200}).start(0,x);
			new Fx.Tween(p.getElement('.b'),{property:'width',duration:200}).start(0,x);
			new Fx.Tween(p.getElement('.m'),{property:'width',duration:200}).start(0,x);
			
			new Fx.Tween(p.getElement('.m'),{property:'height',duration:200}).start(0,y);
			new Fx.Tween(p.getElement('.ml'),{property:'height',duration:200}).start(0,y);
			new Fx.Tween(p.getElement('.mr'),{property:'height',duration:200}).start(0,y);
			
			p.getElement('.close').setStyle("opacity",0);
			(function(){new Fx.Tween(p.getElement('.close'),{property:'opacity',duration:350}).start(1);}).delay(350);
			
			p.getElement('.padding').setStyle('opacity',0);
			(function(){new Fx.Tween(p.getElement('.padding'),{property:'opacity',duration:350}).start(0,1);}).delay(350);
		}).delay(350);

		p.getElement('.close').addEvent("click", function(){
			new Fx.Tween(p.getElement('.close'),{property:'opacity',duration:350}).start(0);
			new Fx.Tween(p.getElement('.padding'),{property:'opacity',duration:350}).start(0);
			(function(){
				new Fx.Tween(p.getElement('.t'),{property:'width',duration:200}).start(x, 0);
				new Fx.Tween(p.getElement('.b'),{property:'width',duration:200}).start(x, 0);
				new Fx.Tween(p.getElement('.m'),{property:'width',duration:200}).start(x, 0);
				
				new Fx.Tween(p.getElement('.m'),{property:'height',duration:200}).start(y, 0);
				new Fx.Tween(p.getElement('.ml'),{property:'height',duration:200}).start(y, 0);
				new Fx.Tween(p.getElement('.mr'),{property:'height',duration:200}).start(y, 0);
				
				new Fx.Morph(p,{duration:200}).start({
					"left":(window.getSize().x/2)+"px",
					"top":fintop+"px"
				}); 
				
				(function(){
					new Fx.Tween(p, {property:'opacity', duration:350}).start(0);
					if(overlay) overlay.fade('out');
				 	new Fx.Morph(p,{duration:350}).start({
						"width":"40px",
						"height":"40px",
						"top":(fintop+120)+"px"
					}); 
					
					(function(){
						p.setStyle("display","none");
						if(overlay) overlay.setStyle("display","none");
					}).delay(350);
				}).delay(200);
			}).delay(200);
		});
		
		if(overlay) {
			overlay.addEvent('click', function() {
				p.getElement('.close').fireEvent('click');
			});
		}
	}else{
		(function(){
			var img = new Asset.image(image,{onload:function(){
				layer.fade('out');
				p.getElement('.content').empty();
				document.id(this).inject(p.getElement('.content'), 'top');
				if(p.getElement('.text')) p.getElement('.text').empty();
				if(content){
					document.id(content).clone().inject(p.getElement('.text'), 'top');
					p.getElement('.text').setStyle("bottom","-"+p.getElement('.text').getStyle("height"));
					new Fx.Tween(p.getElement('.text'),{property:'bottom',duration:350}).start(0);
				} 
			}});
		}).delay(350);
	}
}
