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
    <!-- Google fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/img/favicon.ico">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/fontello.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/flexslider.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/owl.carousel.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/responsive-calendar.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/chosen.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/cloud-zoom.css">
    <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/style.css">

    <!--
    <link href="js/revolution-slider/css/settings.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="jackbox/css/jackbox.min.css" rel="stylesheet" type="text/css" />
    <link href="css/colorpicker.css" rel="stylesheet" type="text/css">
    <link href="css/" rel="stylesheet" type="text/css">
    -->
    <!-- jQuery -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery-ui-1.10.4.min.js"></script>

    <!-- Preloader -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.queryloader2.min.js"></script>


<script type="text/javascript">
    $('html').addClass('no-fouc');

    $(document).ready(function(){

        $('html').show();

        var window_w = $(window).width();
        var window_h = $(window).height();
        var window_s = $(window).scrollTop();

        $("body").queryLoader2({
            backgroundColor: '#f2f4f9',
            barColor: '#63b2f5',
            barHeight: 4,
            percentage:false,
            deepSearch:true,
            minimumTime:1000,
            onComplete: function(){

                $('.animate-onscroll').filter(function(index){

                    return this.offsetTop < (window_s + window_h);

                }).each(function(index, value){

                        var el = $(this);
                        var el_y = $(this).offset().top;

                        if((window_s) > el_y){
                            $(el).addClass('animated fadeInDown').removeClass('animate-onscroll');
                            setTimeout(function(){
                                $(el).css('opacity','1').removeClass('animated fadeInDown');
                            },2000);
                        }

                    });

            }
        });

    });
</script>

</head>

<body>


<!-- Main Slider -->
    <?php
        $main_slider="flexslider main-flexslider animate-onscroll";
        if($this->countModules('main_slider') == 0)
            : $main_slider="flexslider main-flexslider animate-onscroll";
        endif;
    ?>
<!-- /Main Slider -->








<div class="container">

<!--Top with menu-->
<header id="header" class="animate-onscroll">
    <!--Top module main header-->
    <?php
    $logo="col-lg-3 col-md-3 col-sm-3";
    if($this->countModules('logo') == 0)
        : $logo="col-lg-3 col-md-3 col-sm-3";
    endif;
    ?>

    <?php
    $top1="col-lg-5 col-md-4 col-sm-4";
    if($this->countModules('top1') == 0)
        : $top1="col-lg-5 col-md-4 col-sm-4";
    endif;
    ?>

    <?php
    $top2="col-lg-4 col-md-5 col-sm-5";
    if($this->countModules('top2') == 0)
        : $top2="col-lg-4 col-md-5 col-sm-5";
    endif;
    ?>

    <!--Top main header-->
    <div id="main-header">
        <div class="container">
            <div class="row">
                <?php if ($this->countModules('logo')): ?>
                    <div id="logo" class="<?php echo $logo;?>">
                        <jdoc:include type="modules" name="logo" style="clean" />
                    </div>
                <?php endif; ?>

                <?php if ($this->countModules('top1')): ?>
                    <div id="top1" class="<?php echo $top1;?>">
                        <jdoc:include type="modules" name="top1" style="clean" />
                    </div>
                <?php endif; ?>

                <?php if ($this->countModules('top2')): ?>
                    <div id="top2" class="<?php echo $top2;?>">
                        <jdoc:include type="modules" name="top2" style="clean" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!--Menu module-->
    <?php
    $main_menu="";
        if($this->countModules('main_menu') == 0)
            : $main_menu="";
        endif;
    ?>

    <!--Menu-->
    <div id="lower-header">
        <div class="container">
            <div id="menu-button">
                <div>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span>Меню</span>
            </div>

            <?php if ($this->countModules('main_menu')): ?>
                <div class="<?php echo $main_menu;?>">
                    <jdoc:include type="modules" name="main_menu" style="clean" />
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>




<section id="content">



<!--Slider, component and right modules-->
<?php
$main_slider="flexslider main-flexslider";
if($this->countModules('main_slider') == 0)
    : $main_slider="";
endif;
?>
<?php
$breadcrumbs="breadcrumbs";
if($this->countModules('breadcrumbs') == 0)
    : $breadcrumbs="";
endif;
?>
<?php
$right="col-lg-3 col-md-3 col-sm-4";
if($this->countModules('right') == 0)
    : $right="";
endif;
?>

<?php
$component="col-lg-9 col-md-9 col-sm-8";
if($this->countModules('right') == 0)
    :$component="col-lg-12 col-md-12 col-sm-12";
endif;
?>

<!--Slider, component and right-->
<section class="section full-width-bg">
    <div class="row">
        <!--Component-->
        <div class="<?php echo $component;?>">
                <!-- Main Slider -->
                <?php if ($this->countModules('main_slider')): ?>
                    <div class="<?php echo $main_slider;?>">
                        <jdoc:include type="modules" name="main_slider" style="clean" />
                    </div>
                <?php endif; ?>
                <!-- /Main Slider -->

                <!-- Breadcrumbs -->
                <?php if ($this->countModules('breadcrumbs')): ?>
                    <div class="<?php echo $breadcrumbs;?>">
                        <jdoc:include type="modules" name="breadcrumbs" style="clean" />
                    </div>
                <?php endif; ?>
                <!-- /Breadcrumbs -->

            <jdoc:include type="component" />
        </div>

        <!--Right module-->
        <?php if ($this->countModules('right')): ?>
            <div class="<?php echo $right;?>">
                <jdoc:include type="modules" name="right" style="clean" />
            </div>
        <?php endif; ?>
    </div>
</section>


<!--Modules on grey background-->
<section class="section full-width-bg gray-bg">

    <?php
    $content_1="col-lg-9 col-md-9 col-sm-8";
    if($this->countModules('content_4') == 0)
        : $content_1="col-lg-12 col-md-12 col-sm-12";
    endif;
    ?>

    <?php
    $content_4="col-lg-3 col-md-3 col-sm-4";
    if($this->countModules('content_4') == 0)
        : $content_4="";
    endif;
    ?>

    <div class="row">
        <?php if ($this->countModules('content_1')): ?>
            <div class="<?php echo $content_1?>">
                <jdoc:include type="modules" name="content_1" style="news" />
            </div>
        <?php endif;?>

        <?php if ($this->countModules('content_4')): ?>
            <div class="<?php echo $content_4?>">
                <jdoc:include type="modules" name="content_4" style="clean" />
            </div>
        <?php endif;?>
    </div>

    <?php
    $content_2="col-lg-9 col-md-9 col-sm-8";
    if($this->countModules('content_3') == 0)
        : $content_2="col-lg-12 col-md-12 col-sm-12";
    endif;
    ?>

    <?php
    $content_3="col-lg-3 col-md-3 col-sm-4";
    if($this->countModules('content_3') == 0)
        : $content_3="";
    endif;
    ?>

    <div class="row">
        <?php if ($this->countModules('content_2')): ?>
            <div class="<?php echo $content_2?>">
                <jdoc:include type="modules" name="content_2" style="carousel" />
            </div>
        <?php endif;?>

        <?php if ($this->countModules('content_3')): ?>
            <div class="<?php echo $content_3?>">
                <jdoc:include type="modules" name="content_3" style="clean" />
            </div>
        <?php endif;?>
    </div>

</section>


<!--Footer module-->
<?php
$footer_1="col-lg-3 col-md-3 col-sm-6 animate-onscroll";
if($this->countModules('footer_1') == 0)
    : $footer_1="row";
endif;
?>

<?php
$footer_2="col-lg-3 col-md-3 col-sm-6 animate-onscroll";
if($this->countModules('footer_2') == 0)
    : $footer_2="row";
endif;
?>

<?php
$footer_3="col-lg-3 col-md-3 col-sm-6 twitter-widget-area animate-onscroll";
if($this->countModules('footer_3') == 0)
    : $footer_3="row";
endif;
?>

<?php
$footer_4="col-lg-3 col-md-3 col-sm-6 animate-onscroll";
if($this->countModules('footer_4') == 0)
    : $footer_4="row";
endif;
?>

<?php
$footer_5="col-lg-4 col-md-4 col-sm-4 animate-onscroll";
if($this->countModules('footer_5') == 0)
    : $footer_5="row";
endif;
?>

<?php
$footer_6="col-lg-8 col-md-8 col-sm-8 animate-onscroll";
if($this->countModules('footer_6') == 0)
    : $footer_6="row";
endif;
?>

<!--Footer-->
<div id="footer">
    <div id="main-footer">
        <div class="row">
            <?php if ($this->countModules('footer_1')): ?>
                <div class="<?php echo $footer_1;?>">
                    <jdoc:include type="modules" name="footer_1" style="clean" />
                </div>
            <?php endif; ?>

            <?php if ($this->countModules('footer_2')): ?>
                <div class="<?php echo $footer_2;?>">
                    <jdoc:include type="modules" name="footer_2" style="clean" />
                </div>
            <?php endif; ?>

            <?php if ($this->countModules('footer_3')): ?>
                <div class="<?php echo $footer_3;?>">
                    <jdoc:include type="modules" name="footer_3" style="clean" />
                </div>
            <?php endif; ?>

            <?php if ($this->countModules('footer_4')): ?>
                <div class="<?php echo $footer_4;?>">
                    <jdoc:include type="modules" name="footer_4" style="clean" />
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div id="lower-footer">
        <?php if ($this->countModules('footer_5')): ?>
            <div class="<?php echo $footer_5;?>">
                <jdoc:include type="modules" name="footer_5" style="clean" />
            </div>
        <?php endif; ?>

        <?php if ($this->countModules('footer_6')): ?>
            <div class="<?php echo $footer_6;?>">
                <jdoc:include type="modules" name="footer_6" style="clean" />
            </div>
        <?php endif; ?>
    </div>
</div>













<!-- Back To Top -->
<a href="#" id="button-to-top"><i class="icons icon-up-dir"></i></a>

<!--<div class="customize-box"> Customize Box DELITE!!!!!!!!!!!!!!!!!!!
    <h5>Layout Settings</h5>
    <form id="customize-box">
        <label>Layout type:</label><br>
        <input type="radio" value="boxed" name="layout-type" id="boxed-layout-radio"><label for="boxed-layout-radio">Boxed</label>
        <input type="radio" value="wide" name="layout-type" checked="checked" id="wide-layout-radio"><label for="wide-layout-radio">Wide</label>
        <br>
        <label>Background:</label>
        <select id="background-option" class="chosen-select">
            <option value=".background-color">Color</option>
            <option selected value=".background-image">Background</option>
        </select>
        <div class="background-color">
            <div id="colorpicker"></div>
            <input type="hidden" id="colorpicker-value" value="#000">
        </div>
        <div class="background-image">
            <input type="radio" value="img/background/1.jpg" name="background-image-radio" id="background-img-radio-1" checked>
            <label for="background-img-radio-1"><img src="img/background/1-thumb.jpg" alt=""></label>

            <input type="radio" value="img/background/2.jpg" name="background-image-radio" id="background-img-radio-2">
            <label for="background-img-radio-2"><img src="img/background/2-thumb.jpg" alt=""></label>

            <input type="radio" value="img/background/3.jpg" name="background-image-radio" id="background-img-radio-3">
            <label for="background-img-radio-3"><img src="img/background/3-thumb.jpg" alt=""></label>
        </div>
        <input type="submit" value="Submit">
        <input type="reset" value="Reset">
    </form>
    <div class="customize-box-button">
        <i class="icons icon-cog-3"></i>
    </div>
</div>-->

</div>

    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/modernizr.js"></script>
    <!-- Sliders/Carousels -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.flexslider-min.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/owl.carousel.min.js"></script>
    <!-- Revolution Slider  -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.themepunch.plugins.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.themepunch.revolution.min.js"></script>
    <!-- Calendar -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/responsive-calendar.min.js"></script>
    <!-- Raty -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.raty.min.js"></script>
    <!-- Chosen -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/chosen.jquery.min.js"></script>
    <!-- Twitter -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.tweet.js"></script>
    <!-- MixItUp -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.mixitup.js"></script>
    <!-- JackBox -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jackbox-packed.min.js"></script>
    <!-- CloudZoom -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/zoomsl-3.0.min.js"></script>
    <!-- Main Script -->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/script.js"></script>

    <!--if IE9-->
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/jquery.placeholder.js"></script>
    <script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/script_ie.js"></script>

</body>
</html>