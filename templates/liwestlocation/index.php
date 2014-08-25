<?php defined('_JEXEC') or die;
$app = JFactory::getApplication();
?>
<?php echo '<?'; ?>xml version="1.0" encoding="<?php echo $this->_charset ?>"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="ru" >
<?php //Отключаем mootols
	  unset($this->_scripts[$this->baseurl.'/media/system/js/mootools-core.js'], 
			$this->_scripts[$this->baseurl.'/media/system/js/mootools-more.js'],
			$this->_scripts[$this->baseurl.'/media/system/js/core.js'],
			$this->_styleSheets[$this->baseurl.'/media/system/css/modal.css'],
			$this->_scripts[$this->baseurl.'/media/system/js/modal.js'],
			$this->_scripts[$this->baseurl.'/media/system/js/caption.js'],
			$this->_generator);
?>

<head>
<jdoc:include type="head" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/reset.css">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap.css">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap-responsive.css">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/main.css">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/font.css">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/blog.css">
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/font-awesome/css/font-awesome.css">  <!-- иконки -->
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.js" type="text/javascript"></script><!-- Sticky menu -->
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/my.js"></script>
<!--Lightbox-->
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/shadowbox.js"></script>
</head>


<body>

<?php
$background="background";
if($this->countModules('background') == 0)
    : $background="background";
endif;
?>

<?php
	$navbar="navbar-wrapper";
		if($this->countModules('logo and main_menu') == 0) 
			: $navbar="navbar-wrapper";
		endif;
?>

<?php $slider="row-fluid"; if($this->countModules('slider') == 0) : $slider="row-fluid"; endif; ?>

<?php
	$events="row-fluid";
		if($this->countModules('events') == 0) 
			: $events="row-fluid";
		endif;
?>

<?php
	$shop="row-fluid";
		if($this->countModules('help_user_shop and search_shop') == 0) 
			: $shop="row-fluid";
		endif;
?>

<?php
	$teaser="row-fluid";
		if($this->countModules('teaser') == 0) 
			: $teaser="row-fluid";
		endif;
?>

<?php
$top_wiget="top-wiget";
        if($this->countModules('top_wiget') == 0)
            : $top_wiget="row-fluid";
endif;
?>

<?php
	$componenT="offset1 span7";
		if($this->countModules('compnent-right') == 0)
			:$componenT="offset1 span10";

		endif;
?>

<?php
	$CompnentRight="span3";
		if($this->countModules('compnent-right') == 0)
			:$CompnentRight="row-fluid";
		endif;
?>

<?php
	$breadcrumbs="row-fluid";
		if($this->countModules('breadcrumbs') == 0) 
			: $breadcrumbs="row-fluid";
		endif;
?>
<?php
	$centerSomeText="row-fluid";
		if($this->countModules('mainbody and mainbody-right and mainbody-left') == 0) 
			: $centerSomeText="row-fluid";
		endif;
?>


<?php
	$offering="row-fluid";
		if($this->countModules('offering') == 0) 
			: $offering="row-fluid";
		endif;
?>

<?php
	$articles_preview="row-fluid";
		if($this->countModules('articles_preview') == 0) 
			: $articles_preview="row-fluid";
		endif;
?>

<?php
	$aside="row-fluid";
		if($this->countModules('aside') == 0) 
			: $aside="row-fluid";
		endif;
?>

<?php $map="row-fluid"; if($this->countModules('map') == 0) : $map="row-fluid"; endif; ?>
<?php $location="contacts-information"; if($this->countModules('location') == 0) : $location="contacts-information"; endif; ?>

<?php
	$footer="row-fluid";
		if($this->countModules('footer_menu and copyright') == 0) 
			: $footer="row-fluid";
		endif;
?>

<?php
	$debag="row-fluid";
		if($this->countModules('debag') == 0) 
			: $debag="row-fluid";
		endif;
?>


<?php if ($this->countModules('logo + main_menu')): ?>
	<div class="<?php echo $navbar;?>">
		<div class="row-fluid">
			<!-- Лого -->
			<div class="offset1 span2"><jdoc:include type="modules" name="logo" style="xhtml" /></div>

			<!-- Меню -->
			<div class="span8">
				<jdoc:include type="modules" name="main_menu" style="xhtml" /> 
			</div><div class="offset1"></div>
		</div>	
	</div>
<?php endif; ?>

<?php if ($this->countModules('teaser')): ?>
	<!-- Круглые плашки -->
	<div class="<?php echo $teaser;?>">

			<jdoc:include type="modules" name="teaser" style="xhtml" />
	</div>	
<?php endif; ?>

<?php if ($this->countModules('slider')): ?>
	<!-- Slider -->
	<div class="<?php echo $slider;?>">

		<div class="span12"><jdoc:include type="modules" name="slider" style="xhtml" /></div>
	</div>
<?php endif; ?>

<?php if ($this->countModules('top_wiget')):?>
    <div class="<?php echo $top_wiget;?>">
        <jdoc:include type="modules" name="top_wiget" style="xhtml" />
    </div>
<?php endif;?>

<?php if ($this->countModules('help_user_shop + search_shop')): ?>
	<div class="<?php echo $shop;?> help-shop">
		<!-- Меню для помощи покупателям -->
		<div class="offset1 span8"><jdoc:include type="modules" name="help_user_shop" style="clean" /></div>

		<!-- Поиск по томарам -->
		<div class="span3"><jdoc:include type="modules" name="search_shop" style="clean" /></div>
	</div>
<?php endif; ?>

<?php if ($this->countModules('events')): ?>
	<!-- Горячие новости и события -->
	<div class="<?php echo $events;?>">
		<div class="span12  hot-news">
			<jdoc:include type="modules" name="events" style="cleansale" />
		</div>
	</div><div class="hot-news-bg"></div>
<?php endif; ?>	

<?php if ($this->countModules('breadcrumbs')): ?>
	<!-- Навигация по хлебным крошкам -->
	<div class="<?php echo $breadcrumbs;?>">
		<div class="offset1 span10">
			<jdoc:include type="modules" name="breadcrumbs" style="xhtml" />
		</div> <div class="offset1"></div>
	</div>	
<?php endif; ?>

<div class="<?php echo $background;?>">
<jdoc:include type="modules" name="background" style="xhtml" />
    <div class="row-fluid">
            <div class="span12">
                <jdoc:include type="component" />
            </div>
    </div>
</div>



<?php if ($this->countModules('mainbody + mainbody-right + mainbody-left')): ?>
	<div class="<?php echo $centerSomeText;?>">
		<div class="row-fluid">
		<!-- Вы можете попробовать -->
		<div class="row-fluid">
				<div class="offset1 span10">
					<jdoc:include type="modules" name="mainbody" style="xhtml" />
				</div>
				<div class="span1">
				</div>		
		</div>

			<div class="row-fluid">
				<div class="offset1 span4">
					<jdoc:include type="modules" name="mainbody-right" style="xhtml" />
				</div>
				<div class="span6"><jdoc:include type="modules" name="mainbody-left" style="xhtml" /></div>
				<div class="offset1"></div>
			</div>
		</div>
	</div>
<?php endif; ?>	


<?php if ($this->countModules('offering')): ?>
	<div class="<?php echo $offering;?>">
		<div class="span12 additional-menu">
			<jdoc:include type="modules" name="offering" style="cleannews" />
		</div>
		<div class="offset1">
		</div>	
	</div>
<?php endif; ?>

<?php if ($this->countModules('articles_preview')): ?>
	<!-- Статьи и события на главной -->
	<div class="white-bg <?php echo $articles_preview;?>">
		<div class="span12">
			<div class="blog-style"><jdoc:include type="modules" name="articles_preview" style="clean" /></div>
		</div>
	</div> <div class="bottom-white-bg"></div>
<?php endif; ?>

<?php if ($this->countModules('aside')): ?>
	<!-- Особенности китайской продукции для здоровья -->
	<div class="<?php echo $aside;?> tree-bg">
		<div class="offset1 span6">

            <jdoc:include type="modules" name="aside" style="clean" />
		</div>	
	</div>
<?php endif; ?>

<?php if ($this->countModules('map')): ?>
	<!-- Карта -->
	<div class="<?php echo $map;?>">
		<div class="span12"><jdoc:include type="modules" name="map" style="xhtml" /></div>
	</div>
<?php endif; ?>

<?php if ($this->countModules('location')): ?>
	<!-- Контактная информация -->
	<div class="<?php echo $location;?>">
		<jdoc:include type="modules" name="location" style="xhtml" />
	</div>
<?php endif; ?>



    <?php if ($this->countModules('footer_menu + copyright')): ?>
        <div class="<?php echo $footer;?> footer-bg">
            <!-- Меню в подвале -->
            <div class="span6 footer-menu"><jdoc:include type="modules" name="footer_menu" style="clean" /></div>

            <!-- Копирайт -->
            <div class="span6 name-company"><jdoc:include type="modules" name="copyright" style="clean" /></div>
        </div>
    <?php endif; ?>

    <?php if ($this->countModules('debag')): ?>
        <div class="<?php echo $debag;?>">
            <!-- Меню в подвале -->
            <div class="span12 debag-bg"><jdoc:include type="modules" name="debag" style="clean" /></div>
        </div>
    <?php endif; ?>


<!--slider-->
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.ui.core.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.ui.rcarousel.js"></script>
<script type="text/javascript">
    jQuery(function($) {
        function generatePages() {
            var _total, i, _link;

            _total = $( "#carousel" ).rcarousel( "getTotalPages" );

            for ( i = 0; i < _total; i++ ) {
                _link = $( "<a href='#'></a>" );

                $(_link)
                    .bind("click", {page: i},
                    function( event ) {
                        $( "#carousel" ).rcarousel( "goToPage", event.data.page );
                        event.preventDefault();
                    }
                )
                    .addClass( "bullet off" )
                    .appendTo( "#pages" );
            }

            // mark first page as active
            $( "a:eq(0)", "#pages" )
                .removeClass( "off" )
                .addClass( "on" )
                .css( "background-image", "url(templates/liwest/css/img/page-on.png)" );

        }

        function pageLoaded( event, data ) {
            $( "a.on", "#pages" )
                .removeClass( "on" )
                .css( "background-image", "url(templates/liwest/css/img/page-off.png)" );

            $( "a", "#pages" )
                .eq( data.page )
                .addClass( "on" )
                .css( "background-image", "url(templates/liwest/css/img/page-on.png)" );
        }

        $( ".lb_gallery" ).rlightbox();

        $( "#carousel" ).rcarousel({
            auto: {enabled: true},
            start: generatePages,
            pageLoaded: pageLoaded,
            width: 160,
            height: 120
        });

        $( ".bullet" )
            .hover(
            function() {
                $( this ).css( "opacity", 0.7 );
            },
            function() {
                $( this ).css( "opacity", 1.0 );
            }
        );
    });
</script>
<script type='text/javascript'>
    //лайтбоксы рекламы
    Shadowbox.init();

</script>
</body>
</html>