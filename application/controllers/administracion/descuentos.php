<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class descuentos extends Base_Controller
{
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content;
	private $path;
	private $icon;

	private $offset, $limit_max;
	private $tab, $tab1, $tab2, $tab3;

	public function __construct()
	{
		parent::__construct();
		$this->modulo 			= 'administracion';
		$this->submodulo		= 'catalogos';
		$this->seccion          = 'descuentos';
		$this->icon 			= 'fa fa-tag'; 
		$this->path 			= $this->modulo.'/'.$this->seccion.'/'; #administracion/descuentos
		$this->view_content 	= 'content';
		$this->limit_max		= 5;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->seccion.'_model','db_model');
		// Diccionario
		$this->lang->load($this->modulo.'/'.$this->seccion,"es_ES");
	}
	public function config_tabs()
	{
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) #agregar
										,$this->lang_item($tab_2) #listado
										,$this->lang_item($tab_3) #detalle
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1             #administracion/impuestos/agregar
										,$path.$tab_2.'/'.$pagina #administracion/impuestos/listado
										,$tab_3                   #detalle
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('','', array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal()
	{
		return $this->modulo.'/'.$this->view_content;
	}

	public function index()
	{
		$tabl_inicial 			  = 2;
		$view_listado    		  = $this->listado();	
		$contenidos_tab           = $view_listado;
		$data['titulo_seccion']   = $this->lang_item($this->seccion);
		$data['titulo_submodulo'] = $this->lang_item("titulo_submodulo");
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		
		$js['js'][]  = array('name' => $this->seccion, 'dirname' => $this->modulo);
		$this->load_view($this->uri_view_principal(), $data, $js);
	}

	public function listado($offset=0)
	{
	// Crea tabla con listado de elementos capturados 
		$seccion 		= '/listado';
		$tab_detalle	= $this->tab3;	
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.$seccion;
		$url_link 		= $this->path.'listado';
		$filtro      	= ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		$uri_segment  = $this->uri_segment(); 
		$total_rows	  = count($this->db_model->db_get_data($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content = $this->db_model->db_get_data($sqlData);
		$url          = base_url($url_link);
		$paginador    = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		if($total_rows)
		{
			foreach ($list_content as $value)
			{
				// Evento de enlace
				$atrr = array(
								'href' => '#',
							  	'onclick' => $tab_detalle.'('.$value['id_administracion_descuentos'].')'
						);
				// Datos para tabla
				$tbl_data[] = array('id'            => $value['id_administracion_descuentos'],
									'impuesto'      => tool_tips_tpl($value['descuento'], $this->lang_item("tool_tip"), 'right' , $atrr),
									'valor'         => $value['valor_descuento'],
									'clave_corta'   => $value['clave_corta'],
									'descripcion'   => $value['descripcion']
									);
			}
			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id"),
										$this->lang_item("descuento"),
										$this->lang_item("valor"),
										$this->lang_item("clave_corta"),
										$this->lang_item("descripcion"));
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
								'iconsweets' => 'iconsweets-excel',
								'href'       => base_url($this->path.'export_xlsx?filtro='.base64_encode($filtro))
								);
		}
		else
		{
			$buttonTPL = "";
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
		$tabData['tabla']     = $tabla;
		$tabData['export']    = button_tpl($buttonTPL);
		$tabData['paginador'] = $paginador;
		$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);
		if($this->ajax_post(false))
		{
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}
		else
		{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}

	public function detalle()
	{
		$id_descuento                = $this->ajax_post('id_descuento');
		$detalle  	                 = $this->db_model->get_orden_unico_descuento($id_descuento);
		$seccion 	                 = 'detalle';
		$tab_detalle                 = $this->tab3;
		$sqlData        = array(
			 'buscar'      	=> ''
			,'offset' 		=> 0
			,'limit'      	=> 0
		);
		$btn_save                          = form_button(array('class'=>"btn btn-primary",'name' => 'actualizar' , 'onclick'=>'actualizar()','content' => $this->lang_item("btn_guardar") ));   
        $tabData['id_descuento']           = $id_descuento;
        $tabData["nombre_descuento"]       = $this->lang_item("nombre_descuento");
		$tabData["cvl_corta"]              = $this->lang_item("clave_corta");
		$tabData["desc"]                   = $this->lang_item("descripcion");
		$tabData["val"]                    = $this->lang_item("valor");
        $tabData['descuento']              = $detalle[0]['descuento'];
		$tabData['clave_corta']            = $detalle[0]['clave_corta'];
        $tabData['descripcion']            = $detalle[0]['descripcion'];
        $tabData["valor_descuento"]        = $detalle[0]['valor_descuento'];
        $tabData['lbl_ultima_modiciacion'] = $this->lang_item('lbl_ultima_modificacion', false);
        $tabData['val_fecha_registro']     = $detalle[0]['registro'];
		$tabData['lbl_fecha_registro']     = $this->lang_item('lbl_fecha_registro', false);
		$tabData['lbl_usuario_regitro']    = $this->lang_item('lbl_usuario_regitro', false);
        
        $this->load_database('global_system');
        $this->load->model('users_model');

        $usuario_registro                  = $this->users_model->search_user_for_id($detalle[0]['id_usuario']);
	    $usuario_name	                   = text_format_tpl($usuario_registro[0]['name'],"u");
	    $tabData['val_usuarios_registro']  = $usuario_name;

        if($detalle[0]['edit_id_usuario'])
        {
        	$usuario_registro                   = $this->users_model->search_user_for_id($detalle[0]['edit_id_usuario']);
        	$usuario_name 				        = text_format_tpl($usuario_registro[0]['name'],"u");
        	$tabData['val_ultima_modificacion'] = sprintf($this->lang_item('val_ultima_modificacion', false), $this->timestamp_complete($detalle[0]['edit_timestamp']), $usuario_name);
        }
        else
        {
        	$usuario_name = '';
    		$tabData['val_ultima_modificacion'] = $this->lang_item('lbl_sin_modificacion', false);
        }

        $tabData['button_save']           = $btn_save;
        $tabData['registro_por']    	= $this->lang_item("registro_por",false);
      	$tabData['usuario_registro']	= $usuario_name;
        									   #administracion/catalogos/sucursales/sucursales_detalle	
		$uri_view   				  = $this->modulo.'/'.$this->seccion.'/'.$this->seccion.'_'.$seccion;
		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}

	public function actualizar()
	{
		$incomplete  = $this->ajax_post('incomplete');
		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			$json_respuesta = array(
						 'id' 		     => 0
						,'contenido'     => alertas_tpl('error', $msg ,false)
						,'success' 	     => false
				);
		}
		else
		{
			$sqlData = array(
						 'id_administracion_descuentos' => $this->ajax_post('id_descuento')
						,'descuento'          		    => $this->ajax_post('descuento')
						,'valor_descuento'             	=> $this->ajax_post('valor_descuento')
						,'clave_corta' 	     		    => $this->ajax_post('clave_corta')
						,'descripcion'	     		    => $this->ajax_post('descripcion')
						,'edit_timestamp'    	   	    => $this->timestamp()
						,'edit_id_usuario'	 		    => $this->session->userdata('id_usuario')
						);
			$insert = $this->db_model->db_update_data($sqlData);
			if($insert)
			{
				$msg = $this->lang_item("msg_insert_success",false);
				$json_respuesta = array(
						 'id' 		     => 1
						,'contenido'     => alertas_tpl('success', $msg ,false)
						,'success' 	     => true
				);
			}
			else
			{
				$msg = $this->lang_item("msg_err_clv",false);
				$json_respuesta = array(
						 'id' 		    => 0
						,'contenido'    => alertas_tpl('', $msg ,false)
						,'success'    	=> false
				);
			}
		}
		echo json_encode($json_respuesta);
	}

	public function agregar()
	{							#administracion/catalogos_generales/descuentos/descuentos_save
		$seccion       = $this->modulo.'/'.$this->seccion.'/descuentos_save';
		$sqlData        = array(
			 'buscar'      	=> ''
			,'offset' 		=> 0
			,'limit'      	=> 0
		);
		$btn_save     = form_button(array('class'=>"btn btn-primary",'name' => 'save_almacen','onclick'=>'agregar()' , 'content' => $this->lang_item("btn_guardar") ));
		$btn_reset     = form_button(array('class'=>"btn btn-primary",'name' => 'reset','value' => 'reset','onclick'=>'clean_formulario()','content' => $this->lang_item("btn_limpiar")));

		$tab_1["nombre_descuento"]  = $this->lang_item("nombre_descuento");
		$tab_1["descuento"]         = $this->lang_item("descuento");
		$tab_1["valor"]             = $this->lang_item("valor");
		$tab_1["cvl_corta"]         = $this->lang_item("clave_corta");
		$tab_1["desc"]              = $this->lang_item("descripcion");

        $tab_1['button_save']       = $btn_save;
        $tab_1['button_reset']      = $btn_reset;


        if($this->ajax_post(false))
        {
				echo json_encode($this->load_view_unique($seccion , $tab_1, true));
		}
		else
		{
			return $this->load_view_unique($seccion , $tab_1, true);
		}
	}

	public function insert_descuento(){
		$incomplete  = $this->ajax_post('incomplete');

		if($incomplete>0){
			$msg = $this->lang_item("msg_campos_obligatorios",false);
			echo json_encode('0|'.alertas_tpl('error', $msg ,false));
		}else{
			$impuesto        = $this->ajax_post('descuento');
			$valor           = $this->ajax_post('valor_descuento');
			$clave_corta     = $this->ajax_post('clave_corta');
			$descripcion     = $this->ajax_post('descripcion');
			$data_insert     = array('descuento'   => $impuesto,
								 'valor_descuento' => $valor,
								 'clave_corta'     => $clave_corta,
								 'descripcion'     => $descripcion,
								 'id_usuario'      => $this->session->userdata('id_usuario'),  
								 'registro'        => $this->timestamp());
			$insert = $this->db_model->db_insert_data($data_insert);
			
			if($insert){
				$msg = $this->lang_item("msg_insert_success",false);
				echo json_encode('1|'.alertas_tpl('success', $msg ,false));
			}else{
				$msg = $this->lang_item("msg_err_clv",false);
				echo json_encode('0|'.alertas_tpl('', $msg ,false));
			}
		}
	}

	public function export_xlsx($offset=0){
		$filtro      = ($this->ajax_get('filtro')) ?  base64_decode($this->ajax_get('filtro') ): "";
		$limit 		 = $this->limit_max;
		$sqlData     = array(
			 'buscar'      	=> $filtro
			,'offset' 		=> $offset
			,'limit'      	=> $limit
		);
		$lts_content = $this->db_model->db_get_data($sqlData);
		if(count($lts_content)>0){
			foreach ($lts_content as $value) {
				$set_data[] = array(
									 $value['descuento'],
									 $value['valor_descuento'],
									 $value['clave_corta'],
									 $value['descripcion']);
			}
			
			$set_heading = array(
									$this->lang_item("descuento"),
									$this->lang_item("valor_descuento"),
									$this->lang_item("clave_corta"),
									$this->lang_item("descripcion"));
	
		}

		$params = array(	'title'   => $this->lang_item("Descuentos"),
							'items'   => $set_data,
							'headers' => $set_heading
						);
		
		$this->excel->generate_xlsx($params);
	}
}