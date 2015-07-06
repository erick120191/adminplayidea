<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ciclos extends Base_Controller{

	private $modulo;
	private $submodulo;
	private $view_content;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct(){
		parent::__construct();
		$this->modulo 			= 'nutricion';
		$this->seccion		    = 'ciclos';
		$this->icon 			= 'fa fa-cutlery'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; 
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'configurar';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','ciclos');
		$this->load->model('administracion/sucursales_model','sucursales');
		$this->load->model('administracion/servicios_model','servicios');
		$this->load->model('nutricion/tiempos_model','tiempos');
		$this->load->model('nutricion/familias_model','familias');
		$this->load->model('nutricion/recetario_model','recetas');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}

	public function config_tabs()
	{
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$path  	= $this->path;
		
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) 
										,$this->lang_item($tab_2) 
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1             
										,$path.$tab_2                   
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
								);
		// Atributos 
		$config_tab['attr']     = array('','', array('style' => 'display:none'));

		$config_tab['style_content'] = array('','');

		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content;
	}

	public function index(){
		$tabl_inicial 			  = 1;
		$view_agregar    		  = $this->agregar();	
		$contenidos_tab           = $view_agregar;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function agregar(){
		$seccion   = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_save';
		$sqlData = array(
			 'buscar' => ''
			,'offset' => 0
			,'limit' => 0
			);
		//Combo box que muestra las sucursales
		$dropdown_sucursales = array(
						 'data'		=> $this->sucursales->db_get_data($sqlData)
						,'value' 	=> 'id_sucursal'
						,'text' 	=> array('clave_corta','sucursal')
						,'name' 	=> "lts_sucursales"
						,'leyenda' 	=> "-----"
						,'class' 	=> "requerido"
						,'event'    => array('event'      => 'onchange', 
											'function'    => 'load_ciclos', 
											'params'      => array('this.value'), 
											'params_type' => array(false))
					);
		$sucursales = dropdown_tpl($dropdown_sucursales);

		$btn_save  = form_button(array('class'=>'btn btn-primary', 'name'=>'save_puesto', 'onclick'=>'agregar()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));

		$tabIn['lbl_tipo_insert']    = $this->lang_item("lbl_tipo_insert");
		$tabIn['lbl_auto']           = $this->lang_item("lbl_auto");
		$tabIn['lbl_manual']         = $this->lang_item("lbl_manual");
		$tabIn['lbl_cantidad_ciclo'] = $this->lang_item("lbl_cantidad_ciclo");
		$tabIn['lbl_nombre_ciclo']   = $this->lang_item("lbl_nombre_ciclo");
		$tabIn['lbl_clave_corta']    = $this->lang_item('lbl_clave_corta');
		$tabIn['lbl_sucursal']       = $this->lang_item('lbl_sucursal');

		$tabIn['list_sucursales']    = $sucursales;

		$tabIn['btn_save']  = $btn_save;
		$tabIn['btn_reset'] = $btn_reset;

		if($this->ajax_post(false))
		{
			echo json_encode($this->load_view_unique($seccion,$tabIn,true));
		}
		else
		{
			return $this->load_view_unique($seccion, $tabIn, true);
		}
	}

	public function insert_ciclo(){
		$incomplete = $this->ajax_post('incomplete');
		if($incomplete > 0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$sqlData = array(
				 'ciclo'       => $this->ajax_post('txt_cantidad_ciclo')
				,'nom_ciclo'   => $this->ajax_post('txt_ciclo')
				,'id_sucursal' => $this->ajax_post('lts_sucursales')
				,'clave_corta' => $this->ajax_post('txt_clave_corta')
				,'id_usuario'  => $this->session->userdata('id_usuario')
				,'timestamp'   => $this->timestamp()
				,'tipo'        => $this->ajax_post('tipo')
				);
			$insert = $this->ciclos->insert_ciclo($sqlData);
			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}

	public function configurar(){
		$seccion   = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_config';

		$sqlData = array(
			 'buscar' => 0
			,'offset' => 0
			,'limit'  => 0
			);
		//Combo box que muestra las sucursales
		$dropdown_sucursales = array(
						 'data'		=> $this->sucursales->db_get_data($sqlData)
						,'value' 	=> 'id_sucursal'
						,'text' 	=> array('clave_corta','sucursal')
						,'name' 	=> "lts_sucursales"
						,'class' 	=> "requerido"
						,'leyenda' 	=> "-----"
						,'event'    => array('event'      => 'onchange', 
											'function'    => 'load_ciclos', 
											'params'      => array('this.value'), 
											'params_type' => array(false))
					);
		$sucursales = dropdown_tpl($dropdown_sucursales);

		$dropdown_ciclos = array(
					 'value' 	=> 'id_nutricion_ciclos'
					,'text' 	=> array('ciclo')
					,'class' 	=> "requerido"
					,'name' 	=> "lts_ciclos"
			   									);
		$ciclos = dropdown_tpl($dropdown_ciclos);

		$dropdown_servicios = array(
				 'value' 	=> 'id_administracion_servicio'
				,'text' 	=> array('servicio')
				,'leyenda' 	=> "-----"
				,'class' 	=> "requerido"
				,'name' 	=> "lts_servicios"
		   									);
		$servicios = dropdown_tpl($dropdown_servicios);

		$data_tiempo = $this->tiempos->db_get_data($sqlData);
		$dropdown_tiempos = array(
				 'data'     => $data_tiempo
				,'value' 	=> 'id_nutricion_tiempo'
				,'text' 	=> array('tiempo')
				,'leyenda' 	=> "-----"
				,'class' 	=> "requerido"
				,'name' 	=> "lts_tiempos"
					);						
		$tiempos = dropdown_tpl($dropdown_tiempos);

		$data_familia = $this->familias->db_get_data($sqlData);
		$dropdown_familias = array(
				 'data'     => $data_familia
				,'value' 	=> 'id_nutricion_familia'
				,'text' 	=> array('familia')
				,'leyenda' 	=> "-----"
				,'class' 	=> "requerido"
				,'name' 	=> "lts_familias"
				,'event'    => array('event'       => 'onchange', 
									 'function'    => 'load_recetas', 
									 'params'      => array('this.value'), 
									 'params_type' => array(false))
			);						
		$familias = dropdown_tpl($dropdown_familias);

		$recetas  = array(
						// 'data'		=> $this->recetas->get_data($sqlData)
						'value' 	=> 'id_nutricion_receta'
						,'text' 	=> array('receta')
						,'name' 	=> "lts_recetas"
						,'class' 	=> "requerido"
					);

		$list_recetas  = multi_dropdown_tpl($recetas);

		$btn_save  = form_button(array('class'=>'btn btn-primary', 'name'=>'save', 'onclick'=>'insert_config()','content'=>$this->lang_item("btn_guardar")));
		$btn_reset = form_button(array('class'=>'btn btn_primary', 'name'=>'reset','onclick'=>'clean_formulario()','content'=>$this->lang_item('btn_limpiar')));

		$data['lbl_sucursal']     	 = $this->lang_item('lbl_sucursal');
		$data['lbl_ciclos']       	 = $this->lang_item('lbl_ciclos');
		$data['lbl_servicios']    	 = $this->lang_item('lbl_servicios');
		$data['lbl_tiempos']      	 = $this->lang_item('lbl_tiempos');
		$data['lbl_familias']     	 = $this->lang_item('lbl_familias');
		$data['lbl_asignar_recetas'] = $this->lang_item('lbl_recetas');

		$data['btn_save']     	   	 = $btn_save;
		$data['btn_reset']    	     = $btn_reset;
		$data['list_sucursales']  	 = $sucursales;
		$data['list_ciclos']  		 = $ciclos;
		$data['list_servicios']   	 = $servicios;
		$data['list_tiempos']     	 = $tiempos;
		$data['list_familias']     	 = $familias;
		$data['multiselect_recetas'] = $list_recetas;
		if($this->ajax_post(false)){
			echo json_encode($this->load_view_unique($seccion,$data,true));
		}else{
			return $this->load_view_unique($seccion, $data, true);
		}
	}

	public function cargar_ciclos(){
		$id_sucursal = $this->ajax_post('id_sucursal');
		if($id_sucursal){
			$sqlData = array(
							 'buscar' => $id_sucursal
							,'offset' => 0
							,'limit' => 0
							);
			$data_ciclo = $this->ciclos->db_get_data($sqlData);
			$dropdown_ciclos = array(
					 'data'     => $data_ciclo
					,'value' 	=> 'id_nutricion_ciclos'
					,'text' 	=> array('ciclo')
					,'class' 	=> "requerido"
					,'leyenda'  => '-----'
					,'name' 	=> "lts_ciclos"
					,'event'    => array('event' => 'onchange',
							   						'function' => 'load_contenido_ciclo',
			   										'params'   => array('this.value'),
			   										'params_type' => array(false)
			   									));
			$data_servicio = $this->servicios->db_get_data_x_sucursal($id_sucursal);
			$dropdown_servicios = array(
				 'data'     => $data_servicio
				,'value' 	=> 'id_administracion_servicio'
				,'text' 	=> array('servicio')
				,'class' 	=> "requerido"
				,'leyenda' 	=> "-----"
				,'name' 	=> "lts_servicios"
		   									);
			$servicios = dropdown_tpl($dropdown_servicios);
		}else{
			$dropdown_ciclos = array(
					'value' 	=> 'id_nutricion_ciclos'
					,'text' 	=> array('ciclo')
					,'class' 	=> "requerido"
					,'leyenda'  => '-----'
					,'name' 	=> "lts_ciclos");

			$dropdown_servicios = array(
				 'value' 	=> 'id_administracion_servicio'
				,'text' 	=> array('servicio')
				,'class' 	=> "requerido"
				,'leyenda' 	=> "-----"
				,'name' 	=> "lts_servicios"
		   									);
		}

		$ciclos = dropdown_tpl($dropdown_ciclos);
		$servicios = dropdown_tpl($dropdown_servicios);
		$data['ciclos']     = $ciclos;
		$data['servicios']  = $servicios;

		echo json_encode($data);
	}

	public function ciclo_receta(){
		$id_familia  = $this->ajax_post('id_familia');
		$id_sucursal = $this->ajax_post('id_sucursal');
		if($id_familia){
			$receta  = $this->recetas->get_data_recetas_x_familia($id_familia);

			$recetas = array(
							 'data'		=> $receta
							,'value' 	=> 'id_nutricion_receta'
							,'text' 	=> array('receta')
							,'name' 	=> "lts_recetas"
							,'class' 	=> "requerido"
						);
			$list_recetas  = multi_dropdown_tpl($recetas);

			echo json_encode($list_recetas);
		}
	}
	public function ciclo_detalle($id_ciclo = false){
		$id_ciclo = ($this->ajax_post('id_ciclo'))?$this->ajax_post('id_ciclo'):$id_ciclo;
		//print_debug($id_ciclo);
		//$id_ciclo  = $this->ajax_post('id_ciclo');
		//$nom_ciclo = $this->ajax_post('nombre_ciclo');
		$list = '';
		$contenido_ciclo = $this->ciclos->get_ciclo_contenido($id_ciclo);
		//print_debug($contenido_ciclo);
		if(!is_null($contenido_ciclo)){
			foreach ($contenido_ciclo as $key => $value) {
				//print_debug($value);
				/*$servicio[$value['servicio']][] = array('id_servicio' => $value['id_servicio'],
														 'receta'     => $value['receta'] , 
														 'id_vinculo' => $value['id_nutricion_receta']);*/
				$servicios[$value['servicio']][$value['tiempo']][$value['familia']][$value['receta']] = array('id_servicio' => $value['id_servicio'],
																							 'id_tiempo'   => $value['id_tiempo'],
																							 'id_familia'  => $value['id_familia'],
																							 'familia '    => $value['familia'],
																							 'receta'      => $value['receta'], 
																							 'id_vinculo'  => $value['id_nutricion_receta']);
			}
			$nom_ciclo = ($this->ajax_post('nombre_ciclo'))?$nom_ciclo = $this->ajax_post('nombre_ciclo'):'';
			//print_debug($nom_ciclo);
			//print_debug($servicios);
			$list ='<br><div id="sidetreecontrol"><a href="?#">Colapsar</a> | <a href="?#">Extender</a></div>';
			$list .= '<ul id="treeview_ciclos" class=" treeview-gray">';
			
			foreach ($servicios as $servicio => $tiempos) {
				//print_debug($servicio);
				//print_debug($servicio);
				$m = '<a class ="onclick_on" onclick ="eliminar_servicio('.$value['id_servicio'].','.$id_ciclo.')"><span class=" iconfa-trash"></span></a>';
				$list .= '<li><span class=" iconfa-fire"></span>'.$servicio.$m;
				if(is_array($tiempos)){
					$list .= '<ul>';
					
					foreach ($tiempos as $tiempo => $familias){
						//print_debug($tiempo);
						$m = '<a class ="onclick_on" onclick ="eliminar_receta('.$value['id_tiempo'].','.$id_ciclo.')"><span class=" iconfa-trash"></span></a>';
						$list .= '<li><span class=" iconfa-bookmark"></span>'.$tiempo.$m;

						if(is_array($familias)){
							$list .= '<ul>';
							foreach ($familias as $familia => $recetas){
								//print_debug($familia);
								$m = '<a class ="onclick_on" onclick ="eliminar_receta('.$value['id_familia'].','.$id_ciclo.')"><span class=" iconfa-trash"></span></a>';
								$list .= '<li><span class=" iconfa-bookmark"></span>'.$familia.$m;
								if(is_array($recetas)){
									$list .= '<ul>';
									foreach ($recetas as $receta => $val) {
										//print_debug($val);
										$m = '<a class ="onclick_on" onclick ="eliminar_receta('.$value['id_nutricion_receta'].','.$id_ciclo.')"><span class=" iconfa-trash"></span></a>';
										$list .= '<li><span class=" iconfa-bookmark"></span>'.$receta.$m.'</li>';
									}
									$list .= '</ul>';
								}
							}
							$list .= '</ul>';
						}
					}
					$list .= '</ul>';
				}
			}
			$list .= '</ul>';

			$m = '<a class ="onclick_on" onclick="eliminar_servicio(0,'.$id_ciclo.')"">Eliminar todo <span class=" iconfa-trash"></span></a>';
		}else{
			$m = '<a class ="onclick_on">No se tienen recetas vinculadas a este ciclo</a>';
		}
		$detalle = widgetbox_tpl($nom_ciclo, $m.$list);
		echo json_encode($detalle);
	}

	public function insert_config(){
		$objData  	= $this->ajax_post('objData');
		//print_debug($objData);
		if($objData['incomplete']>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode(alertas_tpl('error', $msg ,false));
		}else{
			$arr_recetas  = explode(',',$objData['lts_recetas']);
			if(!empty($arr_recetas)){
				$sqlData = array();
				foreach ($arr_recetas as $key => $value){
					$sqlData = array(
						 'id_ciclo'    => $objData['lts_ciclos']
						,'id_servicio' => $objData['lts_servicios']
						,'id_receta'   => $value
						,'id_familia'  => $objData['lts_familias']
						,'id_tiempo'   => $objData['lts_tiempos']
						,'id_usuario'  => $this->session->userdata('id_usuario')
					    ,'timestamp'   => $this->timestamp()
						);
					$insert = $this->ciclos->insert_ciclo_receta($sqlData);
				}
				$arbol = $this->ciclo_detalle($objData['lts_ciclos']);
				//print_debug($arbol);
				//$msg = $this->lang_item("msg_insert_success",false);
				//echo json_encode('1|'.alertas_tpl('success', $msg ,false));
				//echo json_encode($arbol);
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode(alertas_tpl('', $msg ,false));
			}
		}
	}

	public function eliminar_receta(){
		$id_receta = $this->ajax_post('id_receta');
		$id_ciclo  = $this->ajax_post('id_ciclo');
		//print_debug($this->ajax_post(false));
	}
}