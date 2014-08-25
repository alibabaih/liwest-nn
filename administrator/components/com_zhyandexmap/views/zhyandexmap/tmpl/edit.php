<?php
/*------------------------------------------------------------------------
# com_zhyandexmap - Zh YandexMap
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');

?>
<form action="<?php echo JRoute::_('index.php?option=com_zhyandexmap&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="width-50 fltlft">

<?php echo JHtml::_('sliders.start', 'zhyandexmap3-slider'); ?>


	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_DETAIL'), 'map-mapinfo');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_DECOR_HEADER'), 'map-mapinfo-header');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapdecorheader') as $field): ?>
				<li>
				<?php 
				if ($field->id == 'jform_headerhtml')
				{
					echo '<div class="clr"></div>';
					echo $field->label;
					echo '<div class="clr"></div>';
					echo $field->input;
				}
				else
				{
					echo $field->label;
					echo $field->input;
				}
				?>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_DECOR_FOOTER'), 'map-mapinfo-footer');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapdecorfooter') as $field): ?>
				<li>
				<?php 
				if ($field->id == 'jform_footerhtml')
				{
					echo '<div class="clr"></div>';
					echo $field->label;
					echo '<div class="clr"></div>';
					echo $field->input;
				}
				else
				{
					echo $field->label;
					echo $field->input;
				}
				?>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	
	<?php echo JHtml::_('sliders.end'); ?>

</div>

<div class="width-50 fltrt">
<?php echo JHtml::_('sliders.start', 'zhyandexmap-slider'); ?>

<?php foreach ($params as $name => $fieldset): ?>
	<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
	<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
		<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
	<?php endif;?>
	<fieldset class="panelform" >
		<ul class="adminformlist">
		<?php foreach ($this->form->getFieldset($name) as $field) : ?>
			<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
		<?php endforeach; ?>
		</ul>
	</fieldset>
<?php endforeach; ?>

<?php echo JHtml::_('sliders.end'); ?>
</div>

<div class="width-50 fltrt">
<?php echo JHtml::_('sliders.start', 'zhyandexmap2-slider'); ?>
	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_MAPDECOR'), 'map-mapdecor-controls');?>

	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapdecor') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_MAPPOSITION'), 'control-positions');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('positions') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_MAPMARKER'), 'map-marker');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapmarker') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	
	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_DETAIL_PLACEMARKLIST'), 'map-markerlist');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapmarkerlist') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_MAPMARKERGROUP'), 'map-markergroup');?>

	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapmarkergroup') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_ADVANCED'), 'map-advanced');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapadvanced') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	
	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAP_GEOLOCATION'), 'map-geolocation');?>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('mapgeolocation') as $field): ?>
				<li><?php echo $field->label;echo $field->input;?></li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	
	<?php echo JHtml::_('sliders.panel', JText::_('COM_ZHYANDEXMAP_MAPMARKER_DETAIL_INTEGRATION'), 'integration-attributes');?>

	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('integration') as $field): ?>
				<li><?php 
					echo $field->label;
					echo $field->input;
					?>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	
<?php echo JHtml::_('sliders.end'); ?>

</div>


<div id="YMapsID" class="width-40 fltrt" style="margin:0;padding:0;width:100%;height:450px">
<div id="YMapsCredit" class="zhym-credit"></div>

<?php 

$apikey = $this->mapapikey;

$mapDefLat = $this->mapDefLat;
$mapDefLng = $this->mapDefLng;
$componentApiVersion = $this->mapAPIVersion;

$document	= JFactory::getDocument();

$scripttext = "";
$credits ="";

if ($componentApiVersion == "")
{
	$componentApiVersion = '2.x';
}

if ($componentApiVersion == '2.x')
{
	$mapVersion = "2.0";
	$loadmodules = '';

	$mapMapTypeYandex = $this->mapMapTypeYandex;
	$mapMapTypeOSM = $this->mapMapTypeOSM;
	$mapMapTypeCustom = $this->mapMapTypeCustom;
	
	$scriptlink	= 'http://api-maps.yandex.ru/'.$mapVersion.'/?coordorder=longlat&amp;load=package.full&amp;lang=ru-RU';

	$scripttext .= '<script type="text/javascript" >//<![CDATA[' ."\n";

	$scripttext .= 'ymaps.ready(initialize);' ."\n";

	$scripttext .= 'function initialize () {' ."\n";

	// Begin initialize function
	if ($mapDefLat != "" && $mapDefLng !="")
	{
		$scripttext .= 'spblocation = ['.$mapDefLng.', '.$mapDefLat.'];' ."\n";
		$do_default = 1;
	}
	else
	{
		$scripttext .= 'spblocation = [30.3158, 59.9388];' ."\n";
		$do_default = 0;
	}
	
	if (isset($this->item->latitude) && isset($this->item->longitude) )
	{
			$scripttext .= '    p_center = ['.$this->item->longitude.','.$this->item->latitude.'];' ."\n";
			$scripttext .= '    p_zoom = 14;' ."\n";
	}
	else
	{
		if ($do_default == 1)
		{
			$scripttext .= '    p_center = spblocation;' ."\n";
			$scripttext .= '    p_zoom = 14;' ."\n";
		}
		else
		{
        	$scripttext .= 'if (ymaps.geolocation) ' ."\n";
			$scripttext .= '{' ."\n";
			//$scripttext .= 'alert("Find");';
	        $scripttext .= '      	p_center = [ymaps.geolocation.longitude, ymaps.geolocation.latitude];' ."\n";
			$scripttext .= '  		p_zoom = 14;' ."\n";
			$scripttext .= '}' ."\n";
			$scripttext .= 'else' ."\n";
			$scripttext .= '{' ."\n";
			//$scripttext .= 'alert("SpbLocation");';
			$scripttext .= '    p_center = spblocation;' ."\n";
			$scripttext .= '    p_zoom = 14;' ."\n";
			$scripttext .= '}' ."\n";
		}
	}
		
	$scripttext .= '    map = new ymaps.Map("YMapsID", {' ."\n";
	$scripttext .= '    center: p_center,' ."\n";
	$scripttext .= '    zoom: p_zoom'."\n";
	$scripttext .= '    });' ."\n";

	$scripttext .= 'map.behaviors.enable(\'dblClickZoom\');' ."\n";

	if ((int)$mapMapTypeOSM != 0)
	{
		$scripttext .= 'osmMapType = function () { return new ymaps.Layer(' ."\n";
		$scripttext .= '\'http://tile.openstreetmap.org/%z/%x/%y.png\', {' ."\n";
		$scripttext .= '	projection: ymaps.projection.sphericalMercator' ."\n";
		$scripttext .= '});' ."\n";
		$scripttext .= '};' ."\n";

		$scripttext .= 'ymaps.mapType.storage.add(\'osmMapType\', new ymaps.MapType(' ."\n";
		$scripttext .= '	\'OSM\',' ."\n";
		$scripttext .= '	[\'osmMapType\']' ."\n";
		$scripttext .= '));' ."\n";

		$scripttext .= 'ymaps.layer.storage.add(\'osmMapType\', osmMapType);' ."\n";

		if ($credits != '')
		{
			$credits .= '<br />';
		}
		$credits .= 'OSM '.JText::_('COM_ZHYANDEXMAP_MAP_POWEREDBY').': ';
		$credits .= '<a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a>';
		
	}
	
	// Add Custom MapTypes - Begin
	if ((int)$mapMapTypeCustom != 0)
	{
		foreach ($this->mapMapTypeList as $key => $currentmaptype) 
		{
			$scripttext .= 'customMapLayer'.$currentmaptype->id.' = new ymaps.Layer(' ."\n";
			$scripttext .= '\'\', {' ."\n";

			switch ($currentmaptype->projection)
			{
				case 0:
					$scripttext .= '  projection: ymaps.projection.sphericalMercator' ."\n";
				break;
				case 1:
					$scripttext .= '  projection: ymaps.projection.wgs84Mercator' ."\n";
				break;
				case 2:
					$scripttext .= '  projection: ymaps.projection.Cartesian' ."\n";
				break;
				default:
					$scripttext .= '  projection: ymaps.projection.sphericalMercator' ."\n";
				break;
			}
			if ($currentmaptype->opacity != "")
			{
				$scripttext .= ', brightness: '.$currentmaptype->opacity ."\n";
			}

			$scripttext .= ', tileSize: ['.$currentmaptype->tilewidth.','.$currentmaptype->tileheight.']'."\n";

			if ((int)$currentmaptype->transparent == 0)
			{
				$scripttext .= ', tileTransparent: false' ."\n";
			}
			else
			{
				$scripttext .= ', tileTransparent: true' ."\n";
			}
			
			$scripttext .= '});' ."\n";
			
			$scripttext .= 'customMapLayer'.$currentmaptype->id.'.getTileUrl = '.$currentmaptype->gettileurl ."\n";

			$scripttext .= 'customMapType'.$currentmaptype->id.' = function () { return customMapLayer'.$currentmaptype->id.';';
			$scripttext .= '};' ."\n";
			
			$scripttext .= 'ymaps.mapType.storage.add(\'customMapType'.$currentmaptype->id.'\', new ymaps.MapType(' ."\n";
			$scripttext .= '	\''.str_replace('\'','\\\'', $currentmaptype->title).'\',' ."\n";
			$scripttext .= '	[\'customMapType'.$currentmaptype->id.'\']' ."\n";
			$scripttext .= '));' ."\n";

			$scripttext .= 'ymaps.layer.storage.add(\'customMapType'.$currentmaptype->id.'\', customMapType'.$currentmaptype->id.');' ."\n";
			// End loop by Enabled CustomMapTypes
			
		}
		// End loop by All CustomMapTypes
		
	}
		
	if ((isset($mapMapTypeYandex) && (int)$mapMapTypeYandex == 1) 
	  || (isset($mapMapTypeOSM) && (int)$mapMapTypeOSM != 0) 
	  || (isset($mapMapTypeCustom) && (int)$mapMapTypeCustom != 0) )
	{
		$ctrlPositionFullText ="";

		$ctrlMapType = "";
		
		if (isset($mapMapTypeYandex) && (int)$mapMapTypeYandex == 1) 
		{
			if ($ctrlMapType == "")
			{
				$ctrlMapType .= '"yandex#map", "yandex#satellite", "yandex#hybrid"';
			}
			else
			{
				$ctrlMapType .= ', "yandex#map", "yandex#satellite", "yandex#hybrid"';
			}
		}
		if (isset($mapMapTypeYandex) && (int)$mapMapTypeYandex == 1) 
		{
			if ($ctrlMapType == "")
			{
				$ctrlMapType .= '"yandex#publicMap", "yandex#publicMapHybrid"';
			}
			else
			{
				$ctrlMapType .= ', "yandex#publicMap", "yandex#publicMapHybrid"';
			}
		}

		if ((int)$mapMapTypeOSM != 0)
		{
			if ($ctrlMapType == "")
			{
				$ctrlMapType .= '"osmMapType"' ."\n";
			}
			else
			{
				$ctrlMapType .= ', "osmMapType"' ."\n";
			}
		}
		
		// Add Custom MapTypes - Begin
		if ((int)$mapMapTypeCustom != 0)
		{
			foreach ($this->mapMapTypeList as $key => $currentmaptype) 
			{
				if ($ctrlMapType == "")
				{
					$ctrlMapType .= '"customMapType'.$currentmaptype->id.'"' ."\n";
				}
				else
				{
					$ctrlMapType .= ', "customMapType'.$currentmaptype->id.'"' ."\n";
				}
				// End loop by Enabled CustomMapTypes
				
			}
			// End loop by All CustomMapTypes
			
		}
								
		$scripttext .= 'map.controls.add(new ymaps.control.TypeSelector(['.$ctrlMapType.'])'.$ctrlPositionFullText.');' ."\n";
	}

	
    if ((int)$mapMapTypeOSM != 0)
	{
		if (((int)$mapMapTypeOSM == 2)
		 || ((int)$mapMapTypeYandex == 0
	        || (int)$mapMapTypeCustom == 0))
		{
			$scripttext .= 'map.setType("osmMapType");' ."\n";
		}
	}

    if ((int)$mapMapTypeCustom != 0)
	{
		// Custom Map Type - part 2 (bind) - begin
		foreach ($this->mapMapTypeList as $key => $currentmaptype) 	
		{
			if (((int)$mapMapTypeCustom == 2)
				|| ((int)$mapMapTypeYandex == 0
					|| (int)$mapMapTypeOSM == 0))
			{
				$scripttext .= ' map.setType(\'customMapType'.$currentmaptype->id.'\');' ."\n";
			}
		}
		// Custom Map Type - part 2 (bind) - end
	}
	

	
	$scripttext .= 'map.controls.add(new ymaps.control.MapTools());' ."\n";
	$scripttext .= 'map.controls.add(new ymaps.control.SearchControl());' ."\n";
	$scripttext .= 'map.controls.add(new ymaps.control.ZoomControl());' ."\n";

	$scripttext .= 'var placemark = new ymaps.Placemark(p_center);'."\n";
	$scripttext .= 'placemark.options.set("hasBalloon", false);'."\n";;
	$scripttext .= 'placemark.options.set("draggable", true);'."\n";;

	$scripttext .= 'map.geoObjects.add(placemark);' ."\n";

	$scripttext .= 'placemark.events.add("drag", function (e) {' ."\n";
	$scripttext .= '    var current = placemark.geometry.getCoordinates();' ."\n";
	$scripttext .= '    document.forms.adminForm.jform_longitude.value = current[0];' ."\n";
	$scripttext .= '    document.forms.adminForm.jform_latitude.value = current[1];' ."\n";
	$scripttext .= '});' ."\n";
	
	$scripttext .= 'map.events.add("click", function (e) {' ."\n";
	$scripttext .= '    var current = e.get(\'coordPosition\');' ."\n";
	$scripttext .= '    placemark.geometry.setCoordinates(current);' ."\n";
	$scripttext .= '    document.forms.adminForm.jform_longitude.value = current[0];' ."\n";
	$scripttext .= '    document.forms.adminForm.jform_latitude.value = current[1];' ."\n";
	$scripttext .= '});' ."\n";

	if ($credits != '')
	{
		$scripttext .= '  document.getElementById("YMapsCredit").innerHTML = \''.$credits.'\';'."\n";
	}
	
	$scripttext .= '};' ."\n";
		
	$scripttext .= '//]]></script>' ."\n";
	// Script end

	$document->addScript($scriptlink . $loadmodules);
		
}
else
{
	$mapVersion = "1.1";
	$loadmodules = '';
	
	$scriptlink	= 'http://api-maps.yandex.ru/'.$mapVersion.'/index.xml?key='. $apikey ;

	//Script begin
	$scripttext .= '<script type="text/javascript" >//<![CDATA[' ."\n";

		$scripttext .= 'var map, geoResult;' ."\n";

		$scripttext .= 'YMaps.jQuery(function () {' ."\n";
			
		$scripttext .= '    map = new YMaps.Map(document.getElementById("YMapsID"));' ."\n";
			
		$scripttext .= 'map.enableDblClickZoom();' ."\n";
		$scripttext .= 'map.addControl(new YMaps.Zoom());' ."\n";
		$scripttext .= 'map.setType(YMaps.MapType.MAP);' ."\n";

		$scripttext .= 'map.addControl(new YMaps.ToolBar());' ."\n";
		$scripttext .= 'map.addControl(new YMaps.SearchControl());' ."\n";
		$scripttext .= 'map.addControl(new YMaps.TypeControl());' ."\n";

		if ($mapDefLat != "" && $mapDefLng !="")
		{
			$scripttext .= 'spblocation = new YMaps.GeoPoint('.$mapDefLng.', '.$mapDefLat.');' ."\n";
			$do_default = 1;
		}
		else
		{
			$scripttext .= 'spblocation = new YMaps.GeoPoint(30.3158, 59.9388);' ."\n";
			$do_default = 0;
		}
		
		if (isset($this->item->latitude) && isset($this->item->longitude) )
		{
				$scripttext .= '    p_center = new YMaps.GeoPoint('.$this->item->longitude.','.$this->item->latitude.');' ."\n";
				$scripttext .= '    p_zoom = 14;' ."\n";
		}
		else
		{
			if ($do_default == 1)
			{
				$scripttext .= '    p_center = spblocation;' ."\n";
				$scripttext .= '    p_zoom = 14;' ."\n";
			}
			else
			{
				$scripttext .= 'if (YMaps.location) {' ."\n";
				$scripttext .= '    p_center = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude);' ."\n";
				//$scripttext .= 'alert("Find");';
				$scripttext .= '    if (YMaps.location.zoom) {' ."\n";
				$scripttext .= '        p_zoom = YMaps.location.zoom;' ."\n";
				$scripttext .= '    	p_zoom = 14;' ."\n";
				$scripttext .= '    }' ."\n";
				$scripttext .= '}else {' ."\n";
				//$scripttext .= 'alert("SpbLocation");';
				$scripttext .= '    p_center = spblocation;' ."\n";
				$scripttext .= '    p_zoom = 14;' ."\n";
				$scripttext .= '}' ."\n";
			}
		}


		$scripttext .= '    map.setCenter(p_center, p_zoom );' ."\n";


		$scripttext .= 'var placemark = new YMaps.Placemark(p_center, {draggable: true});' ."\n";
		$scripttext .= 'placemark.name = "";' ."\n";
		$scripttext .= 'placemark.description = "";'."\n";
		$scripttext .= 'map.addOverlay(placemark);' ."\n";
		$scripttext .= 'placemark.openBalloon();' ."\n";

		$scripttext .= 'YMaps.Events.observe(placemark, placemark.Events.Drag, function (obj) {' ."\n";
		$scripttext .= '    var current = obj.getGeoPoint().copy();' ."\n";
		$scripttext .= '    document.forms.adminForm.jform_longitude.value = current.getLng();' ."\n";
		$scripttext .= '    document.forms.adminForm.jform_latitude.value = current.getLat();' ."\n";
		$scripttext .= '});' ."\n";

		$scripttext .= 'YMaps.Events.observe(map, map.Events.Click, function (map, mEvent) {' ."\n";
		$scripttext .= '    var current = mEvent.getGeoPoint().copy();' ."\n";
		$scripttext .= '    placemark.setGeoPoint(current);' ."\n";
		$scripttext .= '    document.forms.adminForm.jform_longitude.value = current.getLng();' ."\n";
		$scripttext .= '    document.forms.adminForm.jform_latitude.value = current.getLat();' ."\n";
		$scripttext .= '});' ."\n";

		
	$scripttext .= '});' ."\n";
		
	$scripttext .= '//]]></script>' ."\n";
	// Script end

	$document->addScript($scriptlink . $loadmodules);
	
}

echo $scripttext;

?>
</div>



<div>
	<input type="hidden" name="task" value="zhyandexmap.edit" />
	<?php echo JHtml::_('form.token'); ?>
</div>


</form>


