<?php

/**
* GK Tab - content (X)HTML template
* @package Joomla!
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');

?>

<div class="gkTabItem<?php echo $active_class; ?>">
	<div class="gkTabItemSpace">
		<?php echo str_replace('[ampersand]', '&', str_replace('[leftbracket]', '<', str_replace('[rightbracket]', '>', $this->tabs_content[$i]))); ?>
	</div>
</div>