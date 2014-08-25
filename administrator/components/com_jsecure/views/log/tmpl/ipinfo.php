<?php
/**
 * jSecure Authentication components for Joomla!
 * jSecure Authentication extention prevents access to administration (back end)
 * login page without appropriate access key.
 *
 * @author      $Author: Ajay Lulia $
 * @copyright   Joomla Service Provider - 2011
 * @package     jSecure2.1.10
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: ipinfo.php  $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<style type="text/css">
	table {font-family: Helvetica,Arial,sans-serif;padding:10px;font-size: 15px;width:480px;}
	td {border-left:2px solid #CCC;border-bottom:2px solid #CCC;text-align: left;padding: 10px;}
	th {border-bottom:2px solid #CCC;text-align: left;padding:10px 10px 10px 10px;width:150px;}
	td.last,th.last{border-bottom: 0px;}
	th.first {border: 0px;}
</style>
<table class="adminlist" cellspacing="0" cellpadding="2">
<thead>
	<tr>
		<th colspan="2" class="first">
			IP Information
		</th>
	</tr>	
	<tr>
		<th width="5">
			<?php echo JText::_( 'Country' ); ?>
		</th>
		<td>
			<?php echo $this->ipInfo['country']; ?>
		</td>	
	</tr>
	<tr>
		<th width="5">
			<?php echo JText::_( 'Region' ); ?>
		</th>
		<td>
			<?php echo $this->ipInfo['region']; ?>
		</td>	
	</tr>
	<tr>
		<th width="5">
			<?php echo JText::_( 'City' ); ?>
		</th>
		<td>
			<?php echo $this->ipInfo['city']; ?>
		</td>	
	</tr>
		<tr>
		<th width="5">
			<?php echo JText::_( 'Latitude' ); ?>
		</th>
		<td>
			<?php echo $this->ipInfo['latitude']; ?>
		</td>	
	</tr>
	<tr>
		<th width="5">
			<?php echo JText::_( 'Longitude' ); ?>
		</th>
		<td>
			<?php echo $this->ipInfo['longitude']; ?>
		</td>	
	</tr>
	<tr>
		<th width="5" class="last">
			<?php echo JText::_( 'Flag' ); ?>
		</th>
		<td class="last">
			<img src="http://www.geobytes.com/Flags/<?php echo $this->ipInfo['internet'] ?>-flag.jpg" />
		</td>	
	</tr>
</thead>
</table>	