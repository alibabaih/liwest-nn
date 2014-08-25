/*
 * =============================================================
 * RAXO All-mode K2 J2.5 - Interface JS
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2012 RAXO Group
 * @license		RAXO Commercial Licence
 * 				This file is forbidden for redistribution
 * @link		http://raxo.org
 * =============================================================
 */


window.addEvent('domready', function() {

	// Source Selection
	var source_selection = $('jform_params_source_selection');
	var source_cat = $('jform_params_source_cat').getParent();
	var source_art = $('jform_params_source_itm').getParent();

	// Default Settings
	if (source_selection.getElement('input:checked').get('value') == 1) {
		source_art.toggleClass('hide-field');
	} else {
		source_cat.toggleClass('hide-field');
	}

	// Changed Settings
	source_selection.addEvent('change', function(){
		source_cat.toggleClass('hide-field');
		source_art.toggleClass('hide-field');
	});

});