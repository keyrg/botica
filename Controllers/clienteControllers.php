<?php
    if($ajaxRequest){
        require_once "../Models/clienteModels.php";
    }else{
        require_once "./Models/clienteModels.php";
    }
 
    class clienteControllers extends clienteModels{

        public function add_client_controller(){
            $nombre=mainModel::clean_chain($_POST['nombre']);
            $dni=mainModel::clean_chain($_POST['dni']);
            $direccion=mainModel::clean_chain($_POST['direccion']);
            $celular=mainModel::clean_chain($_POST['celular']);
            $correo=mainModel::clean_chain($_POST['correo']);
 
            $query1=mainModel::run_simple_query("SELECT cliente_nombre FROM cliente WHERE cliente_nombre='$nombre'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de cliente ya existente",
                    "icon"=>"error"
                ];
            }else{
                $query2=mainModel::run_simple_query("SELECT cliente_dni FROM cliente WHERE cliente_dni='$dni'");
                if($query2->rowCount()==1){
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Número de dni ya existente",
                        "icon"=>"error"
                    ];
                }else{
                    $dataCliente=[
                        "Nombre"=>$nombre,
                        "Dni"=>$dni,
                        "Celular"=>$celular,
                        "Direccion"=>$direccion,
                        "Correo"=>$correo
                    ];
                    $savecliente=clienteModels::add_client_model($dataCliente);
                        if ($savecliente->rowCount()>=1){
                                $alert=[
                                    "Alert"=>"clean",
                                    "title"=>"Operacion exitosa",
                                    "text"=>"Proveedor registrado exitosamente",
                                    "icon"=>"success"
                                ];
                        } else {
                                $alert=[
                                    "Alert"=>"simple",
                                    "title"=>"Ocurrió un error inesperado",
                                    "text"=>"¡Error! No hemos podido registrar proveeor, en este momento",
                                    "icon"=>"error"
                                ];
                        }
                }
           
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_client_controller(){

            $cliente_id=mainModel::clean_chain($_POST['cliente_id']);

            $nombre=mainModel::clean_chain($_POST['nombre']);
            $dni=mainModel::clean_chain($_POST['dni']);
            $direccion=mainModel::clean_chain($_POST['direccion']);
            $celular=mainModel::clean_chain($_POST['celular']);
            $correo=mainModel::clean_chain($_POST['correo']);
    
            $query1=mainModel::run_simple_query("SELECT *FROM cliente WHERE cliente_id='$cliente_id'");
            $answer=$query1->fetch();
    
            if($dni!=$answer['cliente_dni']){
                $query2=mainModel::run_simple_query("SELECT cliente_dni FROM cliente WHERE cliente_dni='$dni'");
                if ($query2->rowCount()==1) {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Número de dni ya existente",
                        "icon"=>"error"
                    ];
                    return mainModel::sweet_alert($alert);
                    exit();
                }
            }
            $dataCliente=[
                "Nombre"=>$nombre,
                "Dni"=>$dni,
                "Celular"=>$celular,
                "Correo"=>$correo,
                "Direccion"=>$direccion,   
                "Codigo"=>$cliente_id
            ];
    
            if (clienteModels::update_client_model($dataCliente)) {
                $alert=[
                    "Alert"=>"clean",
                    "title"=>"Operacion exitosa",
                    "text"=>"Datos actualizado exitosamente",
                    "icon"=>"success"
                ];
            } else {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido registrar cliente, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);
        }
        public function list_client_controller(){

            session_start(['name'=>'STR']);
            $query = clienteModels::list_client_model();
            $data=Array();
            $i=1;
            while ($reg=$query->fetch()) {
                $data[]=array(
                    "0"=>($_SESSION['type_str']=="Administrador")?
                    '<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm edit" title="Actualizar" id="'.mainModel::encryption($reg['cliente_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button" class="btn btn-danger btn-sm delete" title="Eliminar" id="'.mainModel::encryption($reg['cliente_id']).'">
                    <i class="fa fa-trash fa-xs"></i>
                    </button></div>':'<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm denegado" title="Actualizar">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button" class="btn btn-danger btn-sm denegado" title="Eliminar">
                    <i class="fa fa-trash fa-xs"></i>
                    </button></div>',
                    "1"=>$reg['cliente_nombre'],
                    "2"=>$reg['cliente_dni'],
                    "3"=>$reg['cliente_celular'],
                    "4"=>$reg['cliente_direccion'],
                    "5"=>$reg['cliente_correo']
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function delete_client_controller(){

            $cliente_id=mainModel::decryption($_POST['cliente_id']);
            $cliente_id=mainModel::clean_chain($cliente_id);
           
            $query=mainModel::run_simple_query("SELECT venta_id_cliente  FROM venta WHERE venta_id_cliente ='$cliente_id'");
                if ($query->rowCount()==1){             
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"!Error¡ Cliente asociado a una venta",
                        "icon"=>"error"
                    ];
                } else {
                    $cliente=clienteModels::delete_client_model($cliente_id);                 
                    if($cliente->rowCount()>=1){                 
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Operacion exitosa",
                            "text"=>"Cliente eliminado exitosamente",
                            "icon"=>"success"
                        ];
                    }else{
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"¡Error! No hemos podido eliminar clienteW, en este momento",
                            "icon"=>"error"
                        ];
                    }  
                }
     
            return mainModel::sweet_alert($alert);
        }
        public function show_client_controller(){
            $cliente_id=mainModel::decryption($_POST['cliente_id']);
            $cliente_id=mainModel::clean_chain($cliente_id);
            $query=clienteModels::show_client_model($cliente_id);
            $rspta= $query->fetch();
            echo json_encode($rspta);
        }   
          
    }