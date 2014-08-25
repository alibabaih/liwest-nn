<?php

/**
* GK Tab - module template
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
	<?php 
		foreach(array_keys($this->mod_getter) as $m) { 
			echo JModuleHelper::renderModule($this->mod_getter[$m]); 
		}
	?>
	</div>
</div>