<?php
    if($ajaxRequest){
        require_once "../Models/productoModels.php";
    }else{
        require_once "./Models/productoModels.php";
    }
 
    class productoControllers extends productoModels{

        public function add_prod_controller(){
            $producto=mainModel::clean_chain($_POST['producto']);
            $barras=mainModel::clean_chain($_POST['codigo']);
            $concentracion=mainModel::clean_chain($_POST['concentracion']);
            $adicional=mainModel::clean_chain($_POST['adicional']);
            $precioC=mainModel::clean_chain($_POST['precioC']);
            $precioV=mainModel::clean_chain($_POST['precioV']);
            $laboratorio=mainModel::clean_chain($_POST['laboratorio']);
            $categoria=mainModel::clean_chain($_POST['categoria']);
            $presentacion=mainModel::clean_chain($_POST['presentacion']);
            $imagen="../Assets/images/producto/medicamento.png";

            $query1=mainModel::run_simple_query("SELECT prod_nombre FROM producto WHERE prod_nombre='$producto'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de producto ya existente",
                    "icon"=>"error"
                ];
            }else{
                $query2=mainModel::run_simple_query("SELECT prod_codigo FROM producto WHERE prod_codigo='$barras'");
                if($query2->rowCount()==1){
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! codigo de barras ya existente",
                        "icon"=>"error"
                    ];
                }else{
                    $query=mainModel::run_simple_query("SELECT prod_id FROM producto");
                    $number=($query->rowCount())+1;
                    $codigo_producto=mainModel::random_code("00",6,$number);

                    
                    $dataProduct=[
                        "Barras"=>$barras,
                        "Codigo"=>$codigo_producto,
                        "Nombre"=>$producto,
                        "Concentracion"=>$concentracion,
                        "Adicional"=>$adicional,
                        "Imagen"=>$imagen,
                        "PrecioC"=>$precioC,
                        "PrecioV"=>$precioV,
                        "Laboratorio"=>$laboratorio,
                        "Categoria"=>$categoria,
                        "Presentacion"=>$presentacion
                    ];
                    $save=productoModels::add_prod_model($dataProduct);
                        if ($save->rowCount()>=1){
                                $alert=[
                                    "Alert"=>"clean",
                                    "title"=>"Operacion exitosa",
                                    "text"=>"Producto registrado exitosamente",
                                    "icon"=>"success"
                                ];
                        } else {
                                $alert=[
                                    "Alert"=>"simple",
                                    "title"=>"Ocurrió un error inesperado",
                                    "text"=>"¡Error! No hemos podido registrar producto, en este momento",
                                    "icon"=>"error"
                                ];
                        }
                }
           
            }
            return mainModel::sweet_alert($alert);
        }
        public function add_imagen_controller(){

            $id_logo_pro=mainModel::clean_chain($_POST['id_logo_pro']);

            $nombre_producto=$_POST['nombre_producto'];

            if (!file_exists($_FILES['photo']['tmp_name'])|| !is_uploaded_file($_FILES['photo']['tmp_name'])){
                $ruta = "../Assets/images/producto/medicamento.png";
            
            }else{
                $ext=explode(".", $_FILES["photo"]["name"]);
                if ($_FILES['photo']['type']=="image/jpg" || $_FILES['photo']['type']=="image/jpeg" || $_FILES['photo']['type']=="image/png") {

                    $nombre_fichero = "../Assets/uploads/productos/".$nombre_producto;

                    if (!file_exists($nombre_fichero)) {
                        mkdir($nombre_fichero, 0755);
                    }

                    $imagen = round(microtime(true)).'.'. end($ext);
                    $ruta = "../Assets/uploads/productos/".$nombre_producto."/".$imagen;
                    move_uploaded_file($_FILES["photo"]["tmp_name"], $ruta);
                } 
            }
            $data=[
                "Imagen"=>$ruta,
                "Codigo"=>$id_logo_pro
            ];
            $guardar=productoModels::update_image_model($data);
                if($guardar->rowCount()>=1) {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Operacion exitosa",
                        "text"=>"Imagen actualizada exitosamente",
                        "icon"=>"success"
                    ];
                }else {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! No hemos podido registrar categoría, en este momento",
                        "icon"=>"error"
                    ];
                }
            return mainModel::sweet_alert($alert);
        }
        public function update_prod_controller(){

            $prod_id=mainModel::clean_chain($_POST['prod_id']);

            $producto=mainModel::clean_chain($_POST['producto']);
            $barras=mainModel::clean_chain($_POST['codigo']);
            $concentracion=mainModel::clean_chain($_POST['concentracion']);
            $adicional=mainModel::clean_chain($_POST['adicional']);
            $precioC=mainModel::clean_chain($_POST['precioC']);
            $precioV=mainModel::clean_chain($_POST['precioV']);
            $laboratorio=mainModel::clean_chain($_POST['laboratorio']);
            $categoria=mainModel::clean_chain($_POST['categoria']);
            $presentacion=mainModel::clean_chain($_POST['presentacion']);
    
            $query1=mainModel::run_simple_query("SELECT *FROM producto WHERE prod_id='$prod_id'");
            $answer=$query1->fetch();
    
            if($producto!=$answer['prod_nombre']){
                $query2=mainModel::run_simple_query("SELECT prod_nombre FROM producto WHERE prod_nombre='$producto'");
                if ($query2->rowCount()==1) {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Nombre de producto ya existente",
                        "icon"=>"error"
                    ];
                    return mainModel::sweet_alert($alert);
                    
                }
            }
            $dataProduct=[
                "Barras"=>$barras,
                "Nombre"=>$producto,
                "Concentracion"=>$concentracion,
                "Adicional"=>$adicional,
                "PrecioC"=>$precioC,
                "PrecioV"=>$precioV,
                "Laboratorio"=>$laboratorio,
                "Categoria"=>$categoria,
                "Presentacion"=>$presentacion,
                "Codigo"=>$prod_id
            ];
    
            if (productoModels::update_prod_model($dataProduct)) {
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
                    "text"=>"¡Error! No hemos podido registrar producto, en este momento",
                    "icon"=>"error"
                ];
            }
            return mainModel::sweet_alert($alert);
        }
       public function list_prod_controller(){
    session_start(['name'=>'STR']);
    $query = productoModels::list_prod_model();
    $data=Array();
    while ($reg=$query->fetch()) {

        $query2 = productoModels::get_stock_model($reg['prod_id']);

        $stock = 0; // Valor por defecto
        $status = '<span class="badge badge-danger">Agotado</span>'; // Estado por defecto

        while ($obj=$query2->fetch()) {
            $stock = !empty($obj['lote_stock']) ? $obj['lote_stock'] : 0;

            if($stock >= 15):
                $status = '<span class="badge badge-success">Abastecido</span>';
            elseif($stock > 0):
                $status = '<span class="badge badge-warning">Abastecer</span>';
            endif;
        }

        $data[]=array(
            "0"=>($stock > 0)? '<div class="btn-group">
            <button type="button" class="btn btn-info btn-sm editarImagen" title="Actualizar imagen" data-toggle="modal" 
            id="'.mainModel::encryption($reg['prod_id']).'">
            <i class="fa fa-image fa-xs"></i>
            </button>'.' '.' 
            <button type="button" class="btn btn-success btn-sm addLote" title="Aregar lote existente" data-toggle="modal" id="'.mainModel::encryption($reg['prod_id']).'">
            <i class="fa fa-plus-square fa-xs"></i>
            </button>'.' '.' 
            <button type="button"   title="Actualizar" class="btn btn-primary btn-sm edit" id="'.mainModel::encryption($reg['prod_id']).'">
            <i class="fa fa-edit fa-xs"></i>
            </button></div>':
            '<div class="btn-group">
            <button type="button" class="btn btn-info btn-sm editarImagen" title="Actualizar imagen" data-toggle="modal" 
            id="'.mainModel::encryption($reg['prod_id']).'">
            <i class="fa fa-image fa-xs"></i>
            </button>'.' '.' 
            <button type="button" class="btn btn-success btn-sm addLote" data-toggle="modal" id="'.mainModel::encryption($reg['prod_id']).'">
            <i class="fa fa-plus-square fa-xs"></i>
            </button>'.' '.' 
            <button type="button"   title="Actualizar" class="btn btn-primary btn-sm edit" id="'.mainModel::encryption($reg['prod_id']).'">
            <i class="fa fa-edit fa-xs"></i>
            </button>'.' '.' 
            <button type="button"   title="Eliminar" class="btn btn-danger btn-sm delete" id="'.mainModel::encryption($reg['prod_id']).'">
            <i class="fa fa-trash fa-xs"></i>
            </button></div>',
            "1"=>$reg['prod_nombre'],
            "2"=>$reg['prod_concentracion'],
            "3"=>$reg['prod_adicional'],
            "4"=>'UND.'.$stock,
            "5"=>$_SESSION['simbolo_str'].formatMoney(isset($reg['prod_precioV']) ? $reg['prod_precioV'] : 0),
            "6"=>$reg['lab_nombre'], 
            "7"=>$reg['tipo_nombre'],
            "8"=>$reg['present_nombre'],
            "9"=>$status
        );
    }
    $results=array(
             "sEcho"=>1,
             "iTotalRecords"=>count($data),
             "iTotalDisplayRecords"=>count($data),
             "aaData"=>$data);
    echo json_encode($results);
}

        public function list_catalog_controller(){
            session_start(['name'=>'STR']);
            $query = productoModels::list_catalog_model();
            $data=Array();
            while ($reg=$query->fetch()) {
 
                $query2 = productoModels::get_stock_model($reg['prod_id']);

                while ($obj=$query2->fetch()) {
                    if(empty($obj['lote_stock'])){
                        $stock = 0;
                    }else{
                        $stock = $obj['lote_stock'];
                    }    
                }
                $data[]=array(
                    "id"=>$reg['prod_id'],
                    "producto"=>$reg['prod_nombre'],
                    "concentracion"=>$reg['prod_concentracion'],
                    "adicional"=>$reg['prod_adicional'],
                    "stock"=>$stock,
                    "precio"=>$_SESSION['simbolo_str'].formatMoney($reg['prod_precioV']),
                    "laboratorio"=>$reg['lab_nombre'], 
                    "tipo"=>$reg['tipo_nombre'],
                    "presentacion"=>$reg['present_nombre'],
                    "imagen"=>$reg['prod_imagen'],
                );
            }
            $jasonstring = json_encode($data);
            echo $jasonstring;
        }  
        public function delete_prod_controller(){

            $prod_id=mainModel::decryption($_POST['prod_id']);
            $prod_id=mainModel::clean_chain($prod_id);
           
            $query=mainModel::run_simple_query("SELECT detalleCompra_id_producto  FROM detalle_compra WHERE detalleCompra_id_producto ='$prod_id'");
            if ($query->rowCount()==1){             
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"!Error¡Producto asociado a una compra",
                    "icon"=>"error"
                ];
            }else{
                $query2=mainModel::run_simple_query("SELECT detalleVenta_id_producto  FROM detalle_venta WHERE detalleVenta_id_producto ='$prod_id'");
                if($query2->rowCount()==1){             
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"!Error¡Producto asociado a una venta",
                        "icon"=>"error"
                    ];
                }else{
                    $query3=mainModel::run_simple_query("SELECT prod_nombre,prod_imagen  FROM producto WHERE prod_id ='$prod_id'");
                    $answer=$query3->fetch();

                    $nombre_fichero = "../Assets/uploads/productos/".$answer['prod_nombre'];
                       
                    
                 
                    $producto=productoModels::delete_prod_model($prod_id);                 
                    if ($producto->rowCount()>=1){   
                        $lote=productoModels::delete_lote_model($prod_id); 
                        if ($lote->rowCount()>=1){ 
                            if(file_exists($nombre_fichero)){
                                unlink($answer["prod_imagen"]);
                                rmdir($nombre_fichero);
                            }
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Operacion exitosa",
                                "text"=>"Producto eliminado exitosamente",
                                "icon"=>"success"
                            ];
                        }else{
                            if(file_exists($nombre_fichero)){
                                unlink($answer["prod_imagen"]);
                                rmdir($nombre_fichero);
                            }
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Operacion exitosa",
                                "text"=>"Producto eliminado exitosamente",
                                "icon"=>"success"
                            ];
                        }
                    }else{
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"¡Error! No hemos podido eliminar producto, en este momento",
                            "icon"=>"error"
                        ];
                    }
                   
                }  
            }
     
            return mainModel::sweet_alert($alert);
        }
        public function show_prod_controller(){
            $prod_id=mainModel::decryption($_POST['prod_id']);
            $prod_id=mainModel::clean_chain($prod_id);
            $query=productoModels::show_prod_model($prod_id);
            $rspta= $query->fetch();
            echo json_encode($rspta);  
        }  
        public function select_provider_controller(){
            $query=productoModels::select_provider_model();
            $datas= $query->fetchAll();
            echo '<option value="0">Seleccionar</option>';
            foreach ($datas as $rows ) {
                echo '<option value=' . $rows['proved_id'].'>'.$rows['proved_nombre'].'</option>';
            }
        }  
        public function select_laboratory_controller(){
            $query=productoModels::select_laboratory_model();
            $datas= $query->fetchAll();
            echo '<option value="0">Seleccionar</option>';
            foreach ($datas as $rows ) {
                echo '<option value=' . $rows['lab_id'].'>'.$rows['lab_nombre'].'</option>';
            }
        }  
        public function select_category_controller(){
            $query=productoModels::select_category_model();
            $datas= $query->fetchAll();
            echo '<option value="0">Seleccionar</option>';
            foreach ($datas as $rows ) {
                echo '<option value=' . $rows['tipo_id'].'>'.$rows['tipo_nombre'].'</option>';
            }
        }  
        public function select_presentation_controller(){
            $query=productoModels::select_presentation_model();
            $datas= $query->fetchAll();
            echo '<option value="0">Seleccionar</option>';
            foreach ($datas as $rows ) {
                echo '<option value=' . $rows['present_id'].'>'.$rows['present_nombre'].'</option>';
            }
        }      
    }