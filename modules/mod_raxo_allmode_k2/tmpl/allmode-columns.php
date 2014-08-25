<?php
/*
 * =============================================================
 * RAXO All-mode K2 J2.5 - Template
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @subpackage	All-mode Columns Template
 * @copyright	Copyright (C) 2009-2012 RAXO Group
 * @license		GNU General Public License v2.0 http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://raxo.org
 * =============================================================
 */

// no direct access
defined('_JEXEC') or die;

// add CSS
JHtml::stylesheet('modules/mod_raxo_allmode_k2/tmpl/allmode-columns/allmode-columns.css');

// set the Number of Columns
$topcolumn	= 2;
$column		= 2;
?>


<?php if ($toplist) { ?>
<!--<div class="allmode-topbox">-->
<div class="row-fluid">
<div class="span12">
<?php										// All-mode TOP Items Output
$a = 1;
foreach ($toplist as $item) { ?>

	<div class="allmode-topitem">

		<?php if ($item->date) { ?>
		<div class="allmode-date"><?php echo $item->date; ?></div>
		<?php } ?>

		<?php if ($item->image) { ?>
		<div class="allmode-img"><?php echo $item->image; ?></div>
		<?php } ?>

		<?php if ($item->category || $item->hits || $item->author || $item->rating) { ?>
		<div class="allmode-info">

			<?php if ($item->category) { ?>
			<span class="allmode-category"><?php echo $item->category; ?></span>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<span class="allmode-hits"><?php echo $item->hits; ?></span>
			<?php } ?>

			<?php if ($item->author) { ?>
			<span class="allmode-author"><?php echo $item->author; ?></span>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<span class="allmode-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></span>
			<?php } ?>
		</div>
		<?php } ?>

		<?php if ($item->title) { ?>
		<h3 class="allmode-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
			<?php if ($item->comments_count) { ?>
			<span class="allmode-comments">(<a href="<?php echo $item->comments_link; ?>"><?php echo $item->comments_count; ?></a>)</span>
			<?php } ?>
		</h3>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-text"><?php echo $item->text; ?></div>
		<?php } ?>

		<?php if ($item->readmore) { ?>
		<span class="allmode-readmore"><?php echo $item->readmore; ?></span>
		<?php } ?>
	</div>

	<?php //if (($a % $topcolumn) == 0) { ?>
	<!--<div class="allmode-clear"></div>-->
	<?php //} ?>

<?php $a++; } ?>
</div>
</div>
<?php } ?>


<?php if ($list) { ?>
<div class="allmode-itemsbox">
<?php										// All-mode Items Output
$a = 1;
foreach ($list as $item) { ?>

	<div class="allmode-item">

		<?php if ($item->title) { ?>
		<h4 class="allmode-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>
			<?php if ($item->comments_count) { ?>
			<span class="allmode-comments">(<a href="<?php echo $item->comments_link; ?>"><?php echo $item->comments_count; ?></a>)</span>
			<?php } ?>
		</h4>
		<?php } ?>

		<?php if ($item->date || $item->category || $item->hits || $item->author || $item->rating) { ?>
		<div class="allmode-info">

			<?php if ($item->date) { ?>
			<span class="allmode-date"><?php echo $item->date; ?></span>
			<?php } ?>

			<?php if ($item->category) { ?>
			<span class="allmode-category"><?php echo $item->category; ?></span>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<span class="allmode-hits"><?php echo $item->hits; ?></span>
			<?php } ?>

			<?php if ($item->author) { ?>
			<span class="allmode-author"><?php echo $item->author; ?></span>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<span class="allmode-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></span>
			<?php } ?>
		</div>
		<?php } ?>

		<?php if ($item->image) { ?>
		<div class="allmode-img"><?php echo $item->image; ?></div>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-text"><?php echo $item->text; ?></div>
		<?php } ?>

		<?php if ($item->readmore) { ?>
		<span class="allmode-readmore"><?php echo $item->readmore; ?></span>
		<?php } ?>
	</div>

	<?php if (($a % $column) == 0) { ?>
	<div class="allmode-clear"></div>
	<?php } ?>

<?php $a++; } ?>
</div>
<?php } ?>