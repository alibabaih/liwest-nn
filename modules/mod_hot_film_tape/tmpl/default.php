<?php
/*------------------------------------------------------------------------
# "Hot Film Tape" Joomla module
# Copyright (C) 2013 HotThemes. All Rights Reserved.
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Author: HotThemes
# Website: http://www.hotjoomlatemplates.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); // no direct access

// get the document object
$doc =& JFactory::getDocument();

// add your stylesheet
$doc->addStyleSheet( 'modules/mod_hot_film_tape/tmpl/style.css' );

// style declaration
$doc->addStyleDeclaration( '

.list_carousel li {
    border: '.$borderSize.'px solid '.$borderColor.';
    width: '.$elementWidth.'px;
    height: '.$elementHeight.'px;
    padding: '.$elementPadding.'px;
    margin: '.$elementMargin.'px;
}

.list_carousel li, .list_carousel li a {
    color: '.$textColor.';
    text-decoration: none;
}

a#'.$elementID.'_prev.prev, a#'.$elementID.'_next.next {
    border: none;
    outline: none;
    padding: 5px 20px;
    text-decoration: none;
    display: inline-block;
}

.pager a, a#'.$elementID.'_prev.prev, a#'.$elementID.'_next.next {
    background: '.$buttonColor.';
    color: '.$buttonTextColor.' !important;
    text-decoration:none;
}

.pager a:hover, a#'.$elementID.'_prev.prev:hover, a#'.$elementID.'_next.next:hover {
    background: '.$buttonHoverColor.';
}

' );

?>

<?php if ($enablejQuery!=0) { ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<?php } if ($noConflict!=0) { ?>
<script type="text/javascript">
     jQuery.noConflict();
</script>
<?php } ?>

<script type="text/javascript" language="javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_hot_film_tape/js/jquery.carouFredSel-5.6.4-packed.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_hot_film_tape/js/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" language="javascript">
    jQuery(function() {

        //  Fluid layout example 1, resizing the items
        jQuery('#<?php echo $elementID; ?>').carouFredSel({
            <?php if($responsive) { ?>responsive: true,<?php } ?>
            width: '100%',
            scroll: <?php echo $scrollAmount ?>,
            <?php if($navigation) { ?>
            prev: '#<?php echo $elementID; ?>_prev',
            next: '#<?php echo $elementID; ?>_next',<?php } ?>
            <?php if($pagination) { ?>pagination: "#<?php echo $elementID; ?>_pager",<?php } ?>
            <?php if($timer) { ?>
            auto: {
                pauseOnHover: 'resume',
                onPauseStart: function( percentage, duration ) {
                    jQuery(this).trigger( 'configuration', ['width', function( value ) { 
                        jQuery('#<?php echo $elementID; ?>_timer').stop().animate({
                            width: value
                        }, {
                            duration: duration,
                            easing: 'linear'
                        });
                    }]);
                },
                onPauseEnd: function( percentage, duration ) {
                    jQuery('#<?php echo $elementID; ?>_timer').stop().width( 0 );
                },
                onPausePause: function( percentage, duration ) {
                    jQuery('#<?php echo $elementID; ?>_timer').stop();
                }
            },<?php }else{ ?>
            auto: <?php echo $autoSlideShow; ?>, <?php } ?>
            items: {
                visible: {
                    min: <?php echo $minVisible; ?>,
                    max: <?php echo $maxVisible; ?>
                }
            }
        });

    });
</script>

<div class="list_carousel<?php if($responsive) { echo " responsive"; }?>">
    <ul id="<?php echo $elementID; ?>">
        <?php
            for ($loop = 1; $loop <= 20; $loop += 1) {
                if ($enableSlide[$loop]=="true") {
        ?>
        <li><?php if($imageLinkArray[$loop]) { ?><a href="<?php echo $imageLinkArray[$loop]; ?>"<?php if($imageTitleArray[$loop]) { ?> title="<?php echo $imageTitleArray[$loop]; ?>"<?php } ?>><?php } ?>
        <?php echo $imageContentArray[$loop] ?>
        <?php if($imageLinkArray[$loop]) { ?></a><?php } ?>
        </li>
        <?php
                }
            }
        ?>
    </ul>
    <div align="center">
    <a id="<?php echo $elementID; ?>_prev" class="prev" href="#">&larr;</a>
    <div id="<?php echo $elementID; ?>_pager" class="pager"></div>
    <a id="<?php echo $elementID; ?>_next" class="next" href="#">&rarr;</a>
    </div>
    <div id="<?php echo $elementID; ?>_timer" class="timer"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        var bodyWidth = jQuery('.caroufredsel_wrapper').innerWidth();
        var h3Padding = (bodyWidth-996)/2+10;
        jQuery('.mp_bottom1 h3').css('padding-left',h3Padding)
    });
</script>

