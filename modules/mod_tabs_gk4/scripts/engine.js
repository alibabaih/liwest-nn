window.addEvent('load', function(){
	$$('.gkTab').each(function(el,i){
		var module_id = el.getProperty('id');
		var $G = $Gavick['gktab-'+module_id]; 
		var tabs = el.getElements('.gkTabItem');
		var items = el.getElements('.gkTabs li');
		var animation = ($G['animation'] == 0) ? true : false;
		var currentTab = $G['active_tab'] - 1;
		var eventActivator = $G['activator'];
		var amount = tabs.length;
		var timer = false;
		var blank = false;
		var falsy_click = false;
		var animation_type = $G['animation_type'];
		var opacityFx = [];
		// prepare scroll effect
		var scrollFx = null; 
		if(animation_type == 'slider') {
			scrollFx = new Fx.Scroll(el.getElement(".gkTabContainer1"), {duration: $G['animation_speed'], wait: 'ignore', wheelStops:false, transition: $G['animation_function']});

			var sum = 0;
			
			el.getElement('.gkTabContainer0').setStyle('width', el.getElement('.gkTabContainer0').getSize().x + 'px');
			
			tabs.each(function(tab, i){ 
				var size = el.getElement('.gkTabContainer2 .active').getSize().x;
				sum += size; 
				tab.setStyles({
					'position' : 'absolute',
					'left' : i * size + 'px',
					'width' : size + 'px'
				});
			});
			
			el.getElement('.gkTabContainer2').setStyle('width', sum + 'px');
		}
		// initial settings for opacity animation
		if(animation_type == 'opacity') {
			tabs.each(function(tab, i){ 
				tab.setStyles({
					'left' : 0,
					'position' : 'absolute',
					'opacity' : tab.hasClass('active') ? 1 : 0
				});
				opacityFx[i] = new Fx.Tween(tab, {property: 'opacity', duration: $G['animation_speed'], wait: 'ignore'});
			});
			// hide unnecessary tabs
			(function() {
				tabs.setStyle('display', 'none');
				tabs[0].setStyle('display', 'block');
			}).delay(2000); // delay for the NSP module ;)
		}
		// add events to tabs
		items.each(function(item, i){
			
			item.addEvent(eventActivator, function(){
				if(i != currentTab) {
					// specific operations for selected type of the animation
					if(animation_type == 'slider') {
						scrollFx.toElement(tabs[i]);
					} else {
						opacityFx[currentTab].start(0);
						opacityFx[i].start(1);
						// anim
						tabs[i].setStyle('display', 'block');
						//
						(function(){
							tabs.setStyle('display', 'none');
							tabs[i].setStyle('display', 'block');	
						}).delay($G['animation_speed']);
					}
					// common operations for both types of animation
					currentTab = i;
					if(!falsy_click) blank = true;
					else falsy_click = false;
					items.removeClass('active');
					item.addClass('active');
					if($G['cookie_save'] == 1) Cookie.write('gktab-' + module_id, i + 1, { domain: '/', duration: 256 });
				}
			});
		});
		// add events to buttons
		if(el.getElement('.gkTabButtonNext')) {
			el.getElement('.gkTabButtonNext').addEvent('click', function() {
				if(currentTab < amount - 1) items[currentTab + 1].fireEvent(eventActivator);	
				else items[0].fireEvent(eventActivator);
			});
			
			el.getElement('.gkTabButtonPrev').addEvent('click', function() {
				if(currentTab > 0) items[currentTab - 1].fireEvent(eventActivator);	
				else items[amount - 1].fireEvent(eventActivator);
			});
		}
		
		if($G["animation"] == 1){
			timer = (function(){
				if(!blank) {
					falsy_click = true;
					if(currentTab < amount - 1) items[currentTab + 1].fireEvent(eventActivator);	
					else items[0].fireEvent(eventActivator);
				} else {
					blank = false;
				}
			}).periodical($G["animation_interval"]);
		}
	});
});