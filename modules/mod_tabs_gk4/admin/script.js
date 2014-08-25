window.addEvent("domready",function(){
	getUpdates();
	var add_form = document.id('gk_tab_add_form');
	// get the tabs data
	var tabs = JSON.decode(document.id('jform_params_tabs_data').innerHTML);
	if(tabs == null || tabs == '') tabs = [];
	// fix problem with the accordion height
	document.id('TABS_MANAGER-options').addEvent('click', function(){
		if(document.id('TABS_MANAGER-options').hasClass('pane-toggler')) {
			(function(){ $$('.pane-slider').setStyle('height', 'auto'); }).delay(750);
		}
	});
	// get public/register texts
	var public_text = add_form.getElements('.gk_tab_add_content_access option')[0].innerHTML;
	var registered_text = add_form.getElements('.gk_tab_add_content_access option')[1].innerHTML;
	var module_text = add_form.getElements('.gk_tab_add_type option')[0].innerHTML;
	var xhtml_text = add_form.getElements('.gk_tab_add_type option')[1].innerHTML;
	var published_text = document.id('invisible').getElements('.gk_tab_item_state span')[0].innerHTML;
	var unpublished_text = document.id('invisible').getElements('.gk_tab_item_state span')[1].innerHTML;
	document.id('invisible').getElements('.gk_tab_item_state span').destroy();
	// set the add form
	if(add_form.getElement('.gk_tab_add_type').value == 'module') {
		add_form.getElement('.gk_tab_add_content_xhtml').setStyle('display', 'none');
		add_form.getElement('.gk_tab_add_content_module').setStyle('display', 'block');
	} else {
		add_form.getElement('.gk_tab_add_content_xhtml').setStyle('display', 'block');
		add_form.getElement('.gk_tab_add_content_module').setStyle('display', 'none');
	}
	//
	// add tab form events
	//
	add_form.getElement('.gk_tab_add_type').addEvent('change', function(){
		if(add_form.getElement('.gk_tab_add_type').value == 'module') {
			add_form.getElement('.gk_tab_add_content_xhtml').setStyle('display', 'none');
			add_form.getElement('.gk_tab_add_content_module').setStyle('display', 'block');
		} else {
			add_form.getElement('.gk_tab_add_content_xhtml').setStyle('display', 'block');
			add_form.getElement('.gk_tab_add_content_module').setStyle('display', 'none');
		}
	});
	//
	var add_form_scroll_wrap = document.id('gk_tab_add_form').getElement('.height_scroll');
	var add_form_scroll = new Fx.Tween(add_form_scroll_wrap, { duration: 250, property: 'height', onComplete: function() { if(add_form_scroll_wrap.getSize().y > 0) add_form_scroll_wrap.setStyle('height', 'auto'); } });
	//
	document.id('gk_tab_add_header').getElement('a').addEvent('click', function(e) {
		e.stop();
		add_form_scroll.start(add_form.getElement('.gk_tab_add').getSize().y);
	});
	//
	document.id('gk_tab_add_header').addEvent('click', function(e) {
		e.stop();
		add_form_scroll.start(add_form.getElement('.gk_tab_add').getSize().y);
	});
	//
	var add_form_btns = add_form.getElements('.gk_tab_add_submit a');
	// cancel button
	add_form_btns[1].addEvent('click', function(e) {
		if(e) e.stop();
		// clear the form
		add_form.getElement('.gk_tab_add_name').set('value', '');
		add_form.getElement('.gk_tab_add_type').set('value', 'xhtml');
		add_form.getElement('.gk_tab_add_content_xhtml').setStyle('display', 'block');
		add_form.getElement('.gk_tab_add_content_module').setStyle('display', 'none');
		add_form.getElement('.gk_tab_add_content_module').set('value', 'tab1');
		add_form.getElement('.gk_tab_add_content_xhtml').set('value', '');
		add_form.getElement('.gk_tab_add_published').set('value', '1');
		add_form.getElement('.gk_tab_add_content_access').set('value', 'public');
		// hide the form
		add_form_scroll_wrap.setStyle('height', add_form_scroll_wrap.getSize().y + 'px');
		add_form_scroll.start(0);
	});
	// save button
	add_form_btns[0].addEvent('click', function(e) {
		create_item('new');
	});
	// create item
	function create_item(source) {
		// duplicate item structure
		var item = document.id('invisible').getElement('.gk_tab_item').clone();
		// get the values from the form
		var name = (source == 'new') ? add_form.getElement('.gk_tab_add_name').get('value') : source.name;
		var type = (source == 'new') ? add_form.getElement('.gk_tab_add_type').get('value') : source.type;
		var module = (source == 'new') ? add_form.getElement('.gk_tab_add_content_module').get('value') : (source.type == 'module') ? source.content : 'tab1';
		var xhtml = (source == 'new') ? add_form.getElement('.gk_tab_add_content_xhtml').get('value') : source.content;
		var published = (source == 'new') ? add_form.getElement('.gk_tab_add_published').get('value') : source.published;
		var access = (source == 'new') ? add_form.getElement('.gk_tab_add_content_access').get('value') : source.access;
		// put the values to the item
		item.getElement('.gk_tab_item_name').innerHTML = name;
		item.getElement('.gk_tab_item_type').innerHTML = (type == 'module') ? module_text : xhtml_text;
		item.getElement('.gk_tab_item_state').setProperty('class', (published == 1) ? 'gk_tab_item_state published' : 'gk_tab_item_state unpublished');
		item.getElement('.gk_tab_item_state').setProperty('title', (published == 1) ? published_text : unpublished_text);
		item.getElement('.gk_tab_item_access').innerHTML = (access == 'public') ? public_text : registered_text;
		//
		// add the events to the item buttons
		//
		// fill the edit form
		item.getElement('.gk_tab_edit_name').set('value', name);
		item.getElement('.gk_tab_edit_type').set('value', type);
		item.getElement('.gk_tab_edit_content_access').set('value', access);
		item.getElement('.gk_tab_edit_published').set('value', published);
		item.getElement('.gk_tab_edit_content_xhtml').set('value', htmlspecialchars_decode(xhtml));
		item.getElement('.gk_tab_edit_content_module').set('value', module);
		// edit
		item.getElements('.gk_tab_item_edit').addEvent('click', function(e){
			if(e) e.stop();
			item.getElement('.gk_tab_item_desc').fireEvent('click');
		});
		// edit
		item.getElement('.gk_tab_item_desc').addEvent('click', function(e){
			if(e) e.stop();
			var scroller = item.getElement('.gk_tab_editor_scroll');
			scroller.setStyle('height', scroller.getSize().y + "px");
			var fx = new Fx.Tween(scroller, { duration: 250, property: 'height', onComplete: function() { if(scroller.getSize().y > 0) scroller.setStyle('height', 'auto'); } });
			
			if(scroller.getSize().y > 0) {
				fx.start(0);
			} else {
				var items = item.getParent().getElements('.gk_tab_item');
				
				items.each(function(it) {
					if(it != item) it.getElements('.gk_tab_edit_submit a')[1].fireEvent('click');
				});
			
				fx.start(scroller.getElement('div').getSize().y);
			}
		});
		// publish / unpublish
		item.getElement('.gk_tab_item_state').addEvent('click', function(e) {
			if(e) e.stop();
			var btn = item.getElement('.gk_tab_item_state');
			if(btn.hasClass('published')) {
				item.getElement('.gk_tab_edit_published').set('value', 0);
				btn.setProperty('class', 'gk_tab_item_state unpublished');
				btn.setProperty('title', unpublished_text);
				item.getElements('.gk_tab_edit_submit a')[0].fireEvent('click');
			} else {
				item.getElement('.gk_tab_edit_published').set('value', 1);
				btn.setProperty('class', 'gk_tab_item_state published');
				btn.setProperty('title', published_text);
				item.getElements('.gk_tab_edit_submit a')[0].fireEvent('click');
			}
		});
		// set the content of the form
		if(item.getElement('.gk_tab_edit_type').value == 'module') {
			item.getElement('.gk_tab_edit_content_xhtml').setStyle('display', 'none');
			item.getElement('.gk_tab_edit_content_module').setStyle('display', 'block');
		} else {
			item.getElement('.gk_tab_edit_content_xhtml').setStyle('display', 'block');
			item.getElement('.gk_tab_edit_content_module').setStyle('display', 'none');
		}
		// change event
		item.getElement('.gk_tab_edit_type').addEvent('change', function(){
			if(item.getElement('.gk_tab_edit_type').value == 'module') {
				item.getElement('.gk_tab_edit_content_xhtml').setStyle('display', 'none');
				item.getElement('.gk_tab_edit_content_module').setStyle('display', 'block');
			} else {
				item.getElement('.gk_tab_edit_content_xhtml').setStyle('display', 'block');
				item.getElement('.gk_tab_edit_content_module').setStyle('display', 'none');
			}
		});
		// remove
		item.getElements('.gk_tab_item_remove').addEvent('click', function(e){
			if(e) e.stop();
			// get all items list
			var items = item.getParent().getElements('.gk_tab_item');
			// get the item ID on list
			var item_id = items.indexOf(item);
			// remove the object from the JSON array
			tabs.splice(item_id, 1);
			// remove the item from list
			item.destroy();
			// put the data to textarea field
			document.id('jform_params_tabs_data').innerHTML = JSON.encode(tabs);
		});
		// cancel edit
		item.getElements('.gk_tab_edit_submit a')[1].addEvent('click', function(e) {
			if(e) e.stop();
			// hide the form
			var scroller = item.getElement('.gk_tab_editor_scroll');
			scroller.setStyle('height', scroller.getSize().y + "px");
			new Fx.Tween(scroller, { duration: 250, property: 'height' }).start(0);
		});
		// save edit
		item.getElements('.gk_tab_edit_submit a')[0].addEvent('click', function(e) {
			if(e) e.stop();
			// get the data from editor
			var name = item.getElement('.gk_tab_edit_name').get('value');
			var type = item.getElement('.gk_tab_edit_type').get('value');
			var access = item.getElement('.gk_tab_edit_content_access').get('value');
			var published = item.getElement('.gk_tab_edit_published').get('value');
			var xhtml = item.getElement('.gk_tab_edit_content_xhtml').get('value');
			var module = item.getElement('.gk_tab_edit_content_module').get('value');
			// set the data in the JSON object
			var items = item.getParent().getElements('.gk_tab_item');
			var item_id = items.indexOf(item);
			tabs[item_id] = {
				"name" : name,
				"type" : type,
				"content" : (type == 'module') ? module : htmlspecialchars(xhtml),
				"published" : published,
				"access" : access
			};
			// update the item content
			item.getElement('.gk_tab_item_name').innerHTML = name;
			item.getElement('.gk_tab_item_type').innerHTML = (type == 'module') ? module_text : xhtml_text;
			item.getElement('.gk_tab_item_state').setProperty('class', (published == 1) ? 'gk_tab_item_state published' : 'gk_tab_item_state unpublished');
			item.getElement('.gk_tab_item_state').setProperty('title', (published == 1) ? published_text : unpublished_text);
			item.getElement('.gk_tab_item_access').innerHTML = (access == 'public') ? public_text : registered_text;
			// hide the form
			item.getElements('.gk_tab_edit_submit a')[1].fireEvent('click');
			// put the data to textarea field
			document.id('jform_params_tabs_data').innerHTML = JSON.encode(tabs);
		});
		// order up 
		item.getElement('.gk_tab_item_order_up').addEvent('click', function(e) {
			if(e) e.stop();
			var wrap = item.getParent();
			// get item ID
			var items = item.getParent().getElements('.gk_tab_item');
			var item_id = items.indexOf(item);
			// check item ID
			if(item_id > 0) {
				var tmp = tabs[item_id - 1];
				tabs[item_id - 1] = tabs[item_id];
				tabs[item_id] = tmp;
				item.inject(item.getPrevious(), 'before');
				// refresh order buttons state
				if(items.length > 0) {
					wrap.getElements('.gk_tab_item_order_down').setStyle('opacity', 1);
					wrap.getElements('.gk_tab_item_order_up').setStyle('opacity', 1);
					wrap.getElement('.gk_tab_item_order_up').setStyle('opacity', 0.3);
					wrap.getElements('.gk_tab_item_order_down')[items.length - 1].setStyle('opacity', 0.3);
				}
				// put the data to textarea field
				document.id('jform_params_tabs_data').innerHTML = JSON.encode(tabs);
			}	
		});
		// order down
		item.getElement('.gk_tab_item_order_down').addEvent('click', function(e) {
			if(e) e.stop();
			var wrap = item.getParent();
			// get item ID
			var items = wrap.getElements('.gk_tab_item');
			var item_id = items.indexOf(item);
			// check item ID
			if(item_id < items.length - 1) {
				var tmp = tabs[item_id + 1];
				tabs[item_id + 1] = tabs[item_id];
				tabs[item_id] = tmp;
				item.inject(item.getNext(), 'after');
				// refresh order buttons state
				if(items.length > 0) {
					wrap.getElements('.gk_tab_item_order_down').setStyle('opacity', 1);
					wrap.getElements('.gk_tab_item_order_up').setStyle('opacity', 1);
					wrap.getElement('.gk_tab_item_order_up').setStyle('opacity', 0.3);
					wrap.getElements('.gk_tab_item_order_down')[items.length - 1].setStyle('opacity', 0.3);
				}
				// put the data to textarea field
				document.id('jform_params_tabs_data').innerHTML = JSON.encode(tabs);
			}		
		});
		//
		// put the data to object
		//
		if(source == 'new') { // only new objects
			tabs.push({
				"name" : name,
				"type" : type,
				"content" : (type == 'module') ? module : htmlspecialchars(xhtml),
				"published" : published,
				"access" : access
			});
			// clear and hide the form
			add_form_btns[1].fireEvent('click');
			// put the data to textarea field
			document.id('jform_params_tabs_data').innerHTML = JSON.encode(tabs);
		}
		// put the item to the list
		item.inject(document.id('tabs_list'), 'bottom');
		// refresh order buttons state
		var wrap = item.getParent();
		var items = wrap.getElements('.gk_tab_item');
		if(items.length > 0) {
			wrap.getElements('.gk_tab_item_order_down').setStyle('opacity', 1);
			wrap.getElements('.gk_tab_item_order_up').setStyle('opacity', 1);
			wrap.getElement('.gk_tab_item_order_up').setStyle('opacity', 0.3);
			wrap.getElements('.gk_tab_item_order_down')[items.length - 1].setStyle('opacity', 0.3);
		}
	}
	// generate the list
	tabs.each(function(tab) {
		create_item(tab);
	});
	// other form operations
	$$('.input-pixels').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">px</span>"});
	$$('.input-percents').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">%</span>"});
	$$('.input-minutes').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">minutes</span>"});
	$$('.input-ms').each(function(el){el.getParent().innerHTML = el.getParent().innerHTML + "<span class=\"unit\">ms</span>"});
	// switchers
	$$('.gk_switch').each(function(el){
		el.setStyle('display','none');
		var style = (el.value == 1) ? 'on' : 'off';
		var switcher = new Element('div',{'class' : 'switcher-'+style});
		switcher.inject(el, 'after');
		switcher.addEvent("click", function(){
			if(el.value == 1){
				switcher.setProperty('class','switcher-off');
				el.value = 0;
			}else{
				switcher.setProperty('class','switcher-on');
				el.value = 1;
			}
		});
	});
	// demo link
	new Element('a', { 'href' : 'http://mootools.net/demos/?demo=Transitions', 'target' : '_blank',  'id' : 'gkDemoLink', 'html' : 'Demo'  }).inject(document.id('jform_params_animation_function'), 'after');
	// help link
	var link = new Element('a', { 'class' : 'gkHelpLink', 'href' : 'http://tools.gavick.com/tabs.html', 'target' : '_blank' })
	link.inject($$('div.panel')[$$('div.panel').length-1].getElement('h3'), 'bottom');
	link.addEvent('click', function(e) { e.stopPropagation(); });
	//
	document.id('TABS_MANAGER-options').getParent().getElement('.panelform .adminformlist li').setStyle('border', 'none');
});
// function to generate the updates list
function getUpdates() {
	document.id('jform_params_module_updates-lbl').destroy(); // remove unnecesary label
	var update_url = 'http://www.gavick.com/updates.raw?task=json&tmpl=component&query=product&product=mod_tabs_gk4';
	var update_div = document.id('gk_module_updates');
	update_div.innerHTML = '<div id="gk_update_div"><span id="gk_loader"></span>Loading update data from GavicPro Update service...</div>';
	
	new Asset.javascript(update_url,{
		id: "new_script",
		onload: function(){
			content = '';
			$GK_UPDATE.each(function(el){
				content += '<li><span class="gk_update_version"><strong>Version:</strong> ' + el.version + ' </span><span class="gk_update_data"><strong>Date:</strong> ' + el.date + ' </span><span class="gk_update_link"><a href="' + el.link + '" target="_blank">Download</a></span></li>';
			});
			update_div.innerHTML = '<ul class="gk_updates">' + content + '</ul>';
			if(update_div.innerHTML == '<ul class="gk_updates"></ul>') update_div.innerHTML = '<p>There is no available updates for this module</p>';	
		}
	});
}
// encode chars
function htmlspecialchars(string) {
    string = string.toString();
    string = string.replace(/&/g, '[ampersand]').replace(/</g, '[leftbracket]').replace(/>/g, '[rightbracket]');
    return string;
}
// decode chars
function htmlspecialchars_decode(string) {
	string = string.toString();
	string = string.replace(/\[ampersand\]/g, '&').replace(/\[leftbracket\]/g, '<').replace(/\[rightbracket\]/g, '>');
	return string;
}