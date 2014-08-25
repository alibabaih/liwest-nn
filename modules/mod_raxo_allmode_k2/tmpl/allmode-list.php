<?php
/*
 * =============================================================
 * RAXO All-mode K2 J2.5 - Template
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @subpackage	All-mode List Template
 * @copyright	Copyright (C) 2009-2012 RAXO Group
 * @license		GNU General Public License v2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://raxo.org
 * =============================================================
 */

// no direct access
defined('_JEXEC') or die;

// add CSS
JHtml::stylesheet('modules/mod_raxo_allmode_k2/tmpl/allmode-list/allmode-list.css');
?>


<ul class="allmode-itemsbox">
<?php										// All-mode Items Output
foreach ($list as $item) { ?>

	<li class="allmode-item">

		<?php if ($item->date) { ?>
		<span class="allmode-date"><?php echo $item->date; ?></span>
		<?php } ?>

		<?php if ($item->title) { ?>
		<h4 class="allmode-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h4>
		<?php } ?>

	</li>

<?php } ?>
</ul>