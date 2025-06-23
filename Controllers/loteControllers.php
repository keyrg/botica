<?php
    if($ajaxRequest){
        require_once "../Models/loteModels.php";
    }else{
        require_once "./Models/loteModels.php";
    }
 
    class loteControllers extends loteModels{

        public function add_lote_controller(){

            $proveedor=mainModel::clean_chain($_POST['proveedor']); 
            $stock=mainModel::clean_chain($_POST['stock']); 
            $lote_id_product=mainModel::clean_chain($_POST['lote_id_product']); 
            $fechaV=mainModel::clean_chain($_POST['fechaV']); 

            $query=mainModel::run_simple_query("SELECT lote_id FROM lote");
            $number=($query->rowCount())+1;
            $codigo_lote=mainModel::random_code("00",6,$number);

            $dataLote=[
                "Codigo"=>$codigo_lote,
                "Quanty"=>$stock,
                "DateV"=>$fechaV,
                "Product"=>$lote_id_product,
                "Provider"=>$proveedor
            ];

            $saveLote=loteModels::add_lote_model($dataLote);
 
            if($saveLote->rowCount()>=1) {
                $alert=[
                    "Alert"=>"clean",
                    "title"=>"Operacion exitosa",
                    "text"=>"Stock agregado exitosamente",
                    "icon"=>"success"
                ];
            }else{
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido registrar el stock, en este momento",
                    "icon"=>"error"
                ];
            }
            
            return mainModel::sweet_alert($alert);
        }
        public function update_prod_controller(){

            $lote_id=mainModel::clean_chain($_POST['lote_id']);
            $stock=mainModel::clean_chain($_POST['stock']);
         
            $dataLote=[
                "Quanty"=>$stock,
                "Code"=>$lote_id
            ];
    
            if (loteModels::update_lote_model($dataLote)) {
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
                    "text"=>"¡Error! No hemos podido actualizar el stock, en este momento",
                    "icon"=>"error"
                ];
            }
            return mainModel::sweet_alert($alert);
        }
        public function list_lote_controller(){
 
            $query = loteModels::list_lote_model();
            $data=Array();
            $fecha_actual = new DateTime(); 
            while ($reg=$query->fetch()) {
                $vencimiento = new DateTime($reg['lote_fechaVencimiento']);
                $diferencia = $vencimiento->diff($fecha_actual);
                $mes = $diferencia->m;
                $dia = $diferencia->d;
                $verificado = $diferencia->invert;
                if($verificado == 0){
                    $estado = '<span class="badge badge-danger">Vencido</span>';
                    $mes = $mes*(-1);
                    $dia = $dia*(-1);
                }else{
                    if($mes>3){
                        $estado = '<span class="badge badge-success">No vencido</span>';
                    }
                    if($mes<=3){
                        $estado = '<span class="badge badge-warning">Por vencer</span>';
                    }
                }
                $data[]=array(
                    "0"=>($reg['lote_cantUnitario'] && $verificado)?
                    '<div class="btn-group">
                    <button type="button" data-toggle="modal" title="Actualizar" class="btn btn-primary btn-sm editar" id="'.mainModel::encryption($reg['lote_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button></div>':
                    '<div class="btn-group">
                    <button type="button" data-toggle="modal" title="Actualizar"  class="btn btn-primary btn-sm editar" id="'.mainModel::encryption($reg['lote_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button" class="btn btn-danger btn-sm delete"  title="Eliminar" id="'.mainModel::encryption($reg['lote_id']).'">
                    <i class="fa fa-trash fa-xs"></i>
                    </button></div>',
                    "1"=>$reg['lote_codigo'],
                    "2"=>$reg['prod_nombre'],
                    "3"=>$reg['prod_concentracion'],
                    "4"=>$reg['prod_adicional'],
                    "5"=>"UND.".$reg['lote_cantUnitario'],
                    "6"=>$reg['lote_fechaVencimiento'], 
                    "7"=>$reg['lab_nombre'],
                    "8"=>$reg['tipo_nombre'],
                    "9"=>$reg['present_nombre'],
                    "10"=>$reg['proved_nombre'],
                    "11"=>$mes,
                    "12"=>$dia,
                    "13"=>$estado
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        } 
        public function riesgo_lote_controller(){
 
            $query = loteModels::riesgo_lote_model();
            $data=Array();
            $fecha_actual = new DateTime(); 
            while ($reg=$query->fetch()) {
                $vencimiento = new DateTime($reg['lote_fechaVencimiento']);
                $diferencia = $vencimiento->diff($fecha_actual);
                $mes = $diferencia->m;
                $dia = $diferencia->d;
                $verificado = $diferencia->invert;
                if($verificado == 0){
                    $estado = 'danger';
                    $mes = $mes*(-1);
                    $dia = $dia*(-1);
                }else{
                    if($mes>3){
                        $estado = 'light';
                    }
                    if($mes<=3){
                        $estado = 'warning';
                    }
                }
                $data[]=array(
                    "id"=>$reg['lote_codigo'],
                    "producto"=>$reg['prod_nombre'],
                    "stock"=>$reg['lote_cantUnitario'],
                    "laboratorio"=>$reg['lab_nombre'],
                    "presentacion"=>$reg['present_nombre'],
                    "proveedor"=>$reg['proved_nombre'],
                    "mes"=>$mes,
                    "dia"=>$dia,
                    "estado"=>$estado
                );
            }
            $jsonstring = json_encode($data);
            echo $jsonstring;
        } 
        public function delete_lote_controller(){

            $lote_id=mainModel::decryption($_POST['lote_id']);
            $lote_id=mainModel::clean_chain($lote_id);
        
            $proveedor=loteModels::delete_lote_model($lote_id);                 
            if($proveedor->rowCount()>=1){                 
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Operacion exitosa",
                    "text"=>"Lote eliminado exitosamente",
                    "icon"=>"success"
                ];
            }else{
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido eliminar lote, en este momento",
                    "icon"=>"error"
                ];
            }
            return mainModel::sweet_alert($alert);
        }
        public function show_lote_controller(){
            $lote_id=mainModel::decryption($_POST['lote_id']);
            $lote_id=mainModel::clean_chain($lote_id);
            $query=loteModels::show_lote_model($lote_id);
            $rspta= $query->fetch();
            echo json_encode($rspta);  
        }  
    }