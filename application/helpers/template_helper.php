<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	if(!function_exists('tabbed_tpl')){
		function tabbed_tpl($config, $uri_string, $segment = 1, $content = ""){
			$link         = "";
			$tabs         = "";
			$tabbed       = "";
			$tabs_li      = "";
			$tabs_content = "";
			$total_tabs   = count($config['names']);

			for ($i=0; $i < $total_tabs; $i++) { 
				$activate = ($i==($segment-1)) ? 'ui-tabs-active ui-state-active' : "";
				$selected = ($i==($segment-1)) ? 'true' : "";
				$display  = ($i==($segment-1)) ? 'display: block' : "";
				if(is_array($content)){
					$data = (array_key_exists($i, $content) ) ? $content[$i] : '';
				}else{
					$data     = ($i==($segment-1)) ? $content : "";
				}
				
				$uri_string = ($uri_string=="") ? "" : trim($uri_string,'/').'/';

				$link    = ($config['links'][$i]=="") ? '"'.trim($uri_string,'/').'"'  : '"'.$uri_string.$config['links'][$i].'"';
				
				if($config['action'][$i]==""){
					$onclick  = "";
				}else{
					$action = $config['action'][$i];
					if(is_array($action)){
						foreach ($action as $function => $params) {
							if(is_array($params)){
								$params = implode(',', $params);
							}
							$onclick  = "onclick='$function($link, $params);'";
						}
						//print_debug($action);
					}else{
						$onclick  = "onclick='$action($link, $i);'";
					}
				}

				$attr     = array_2_string_format($config['attr'][$i]);
				$tabs_li .= "<li   class='ui-state-default ui-corner-top $activate' role='tab' tabindex='$i' aria-controls='a-$i' aria-labelledby='ui-id-$i' aria-selected='$selected'>
								<a $attr href='#a-$i' $onclick class='ui-tabs-anchor' role='presentation' tabindex='$i' id='ui-id-$i'>
									".$config['names'][$i]."
								</a>
							</li>";

				$tabs_content .= "<div id='a-$i' aria-labelledby='ui-id-$i' class='ui-tabs-panel ui-widget-content ui-corner-bottom' role='tabpanel' aria-expanded='$selected' aria-hidden='false' style='overflo $display'>
        							$data
    								</div>";
			}

			$tabbed .= "<div class='tabbedwidget tab-primary ui-tabs ui-widget ui-widget-content ui-corner-all' style='overflow:visible;'>";
    		$tabbed .= "<ul class='ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all' role='tablist'>";
    		$tabbed .= $tabs_li;
    		$tabbed .= "</ul>";
    		$tabbed .= $tabs_content;
    		$tabbed .= "</div>";

    	return $tabbed;

		}
	}
	if(!function_exists('alertas_tpl')){
		function alertas_tpl($type = '', $mensaje = '' ,$close = false){
			$alert = "";
			$button_close = "";
			if($type == ""){
				$type = "alert";
			}else{
				$type = "alert-$type";
			}
			if($close){
				$button_close = "button data-dismiss='alert' class='close' type='button'>×</button>";
			}
			
			$alert ="<div class='alert $type'> $mensaje </div>";

			return $alert;
		}
	}
	if(!function_exists('text_format_tpl')){
		function text_format_tpl($string, $format = "f"){
			if($string==''){
				return $string;
			}
			if($format=="f"){
				return ucfirst(strtolower($string));
			}else{
				return ucwords(strtolower($string));
			}
		}
	}
	if(!function_exists('tool_tips_tpl')){
		function tool_tips_tpl($value, $tool_tip = '', $pocision = 'right', $attr = array()){
			$attr = array_2_string_format($attr);
			$tool_tip ="<ul class='tooltips'>
                       		<a $attr data-placement='$pocision' data-rel='tooltip'  data-original-title='$tool_tip' >$value</a></li>
                    	</ul>";
			
			return $tool_tip ;
		}
	}
	if(!function_exists('dropdown_tpl')){
		// Crea una lista <select>
		function dropdown_tpl($params=array()){			
			if(!empty($params)){
				$data 		= (isset($params['data']))?$params['data']:false;
				$selected 	= (isset($params['selected']))?$params['selected']:'';
				$value 		= (isset($params['value']))?$params['value']:false;
				$text 		= (isset($params['text']))?$params['text']:false;
				$name 		= (isset($params['name']))?$params['name']:false;
				$class 		= (isset($params['class']))?$params['class']:'';
				$event 		= (isset($params['event']))?$params['event']:'';
				$disabled   = (isset($params['disabled']))?$params['disabled']:'';
				$leyenda 	= (isset($params['leyenda']))?$params['leyenda']:'-----';
			}
			$name         = ($name=="")?"selected": $name;
			$count        = 0;
			if($data && $name && $value && $text){
				foreach ($data as $option => $item) {
					$option_value = "";
					if($count==0){
						$options[0]= $leyenda;
					}
					if(is_array($text)){
						foreach ($text as $string) {
							$option_value .= $item[$string].'-';
						}
						$options[$item[$value]] = trim($option_value, '-');
					}else{
						$options[$item[$value]]= $item[$text];
					}					
					$count++;
				}
				$selected = "<span class='formwrapper'>".form_dropdown($name, $options, $selected, " class='chzn-select $class' $event $disabled data-campo='$name'")."</span>";
				return $selected;
			}
			return false;
		}
	}

	if(!function_exists('dropMultiselect_tpl')){
		// Crea una lista <multiselect> 
		function dropMultiselect_tpl($params=array()){			
			if(!empty($params)){
				$data 		= (isset($params['data']))?$params['data']:false;
				$selected 	= (isset($params['selected']))?$params['selected']:'';
				$value 		= (isset($params['value']))?$params['value']:false;
				$text 		= (isset($params['text']))?$params['text']:false;
				$name 		= (isset($params['name']))?$params['name']:false;
				$class 		= (isset($params['class']))?$params['class']:'';
				$event 		= (isset($params['event']))?$params['event']:'';
				$disabled   = (isset($params['disabled']))?$params['disabled']:'';
				//$insert     = $params['insert'];
			}
			$name         = ($name=="") ? "selected" : $name;
			$count        = 0;

			if($data && $name && $value && $text){
				foreach ($data as $option => $item) {
					$option_value = "";
					if(is_array($text)){
						foreach ($text as $string) {
							$option_value .= $item[$string].'-';
						}
						$options[$item[$value]] = trim($option_value, '-');
					}else{
						$options[$item[$value]]= $item[$text];
					}					
					$count++;
				}
<<<<<<< HEAD
				if($insert)
					$multiple  = form_multiselect('list', array(), $selected,"multiple='multiple' class='$class' size='10'");
				else
					$multiple =  form_multiselect($name, $options, $selected,"multiple='multiple' class='$class' size='10'");
=======
				/*if($insert)
					//print_debug($options);
					//$multiple  = form_multiselect('list', array(), $selected,"multiple='multiple' class='$class' size='10'");
				else*/
				$multiple =  form_multiselect($name, $options, $selected,"multiple='multiple' class='$class' size='10'");
>>>>>>> 2d9bf2eeecf6873256f457d3ca5c17c55e8fac54
				$selected = "<span id='dualselect' class='dualselect'>"
							.form_multiselect($name, $options, $selected,"multiple='multiple' size='10'")
				               ."<span class='ds_arrow'>
				               	<button class='btn ds_prev'>
							    	<i class='iconfa-chevron-left'>
							    	</i>
							    </button>
							    <br>
							    <button class='btn ds_next'>
							    	<i class='iconfa-chevron-right'>
							        </i>
								    </button>
				              		</span>"
				              	.$multiple
				            ."</span>";
				return $selected;
			}
			return false;
		}
	}

	
	if(!function_exists('button_tpl')){
		function button_tpl($params=array()){
			$button = "";
			if(!empty($params)){
				$text 		= (array_key_exists('text', $params))?$params['text']:false;
				$iconsweets = (array_key_exists('iconsweets',$params))?$params['iconsweets']:'';
				$event      = (array_key_exists('event',$params))?data_event_tpl($params['event']):false;
				$href    	= (array_key_exists('href',$params))?$params['href']:false;
				//style="color:red;"
			}else{
				return false;
			}

			if(is_array($text)){
				for ($i=0; $i < count($text); $i++) { 
					
					$label = $text[$i];
					$icon  = ($iconsweets[$i]) ? $iconsweets[$i] : 'iconsweets-link'; 
					$jsOn  = ($onclick[$i]) ? 'onclick="'.$onclick[$i].'"' : ''; 
					$link  = ($href[$i]) ? $href[$i] : ''; 
					$button .= "<li><a href='$link' class='btn btn-rounded'> <i class='$icon'></i> &nbsp; $label</a> </li>";
				}

				$button .= '<ul class="list-nostyle list-inline">'.$button.'</ul>';
			}else{
				$label = $text;
				$icon  = ($iconsweets) ? $iconsweets: 'iconsweets-link'; 
				$event  = ($event) ? $event : ''; 
				$link  = ($href) ? "href='".$href."'" : ''; 
				$button = "<ul class='list-nostyle list-inline'><li><a $link $event class='btn btn-rounded'> <i class='$icon'></i> &nbsp; $label</a> </li></ul>";
			}

			return $button;
		}
	}
	if(!function_exists('data_event_tpl')){
		function data_event_tpl($data = ''){
			
			$var = array();
			if(is_array($data)){
				if(!empty($data)){
					$event    = (array_key_exists('event', $data)) ? $data['event'] : false;
					$function = (array_key_exists('function', $data)) ? $data['function'] : false;
					$params   = (array_key_exists('params', $data)) ? $data['params'] : false;
					if($event){
						if($function){
							if(is_array($params)){
								foreach ($params as $key => $value) {
									$vars[] = '"'.$value.'"';
								}
								$params = implode(',', $vars);
								$event = $event."='".$function."(".$params.");"."'";
							}else{
								$event = $event."='".$function."();'";
							}
							return $event;
						}
					}
					return false;
				}
				return false;
			}
			return false;
			
		}
	}

		
?>