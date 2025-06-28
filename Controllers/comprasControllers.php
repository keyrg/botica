<?php
    if($ajaxRequest){
        require_once "../Models/comprasModels.php";
    }else{
        require_once "./Models/comprasModels.php";
    }
 
    class comprasControllers extends comprasModels{

        public function add_controller(){
            $proveedor=mainModel::clean_chain($_POST['proveedor']);
            $fecha=mainModel::clean_chain($_POST['fecha']);
            $num_documento=mainModel::clean_chain($_POST['num_documento']);
            $tipo_documento=mainModel::clean_chain($_POST['tipo_documento']);
            $num_serie=mainModel::clean_chain($_POST['num_serie']);
            $igv=mainModel::clean_chain($_POST['igv']);
            $total=mainModel::clean_chain($_POST['total_buy']);
 
 
            $query=mainModel::run_simple_query("SELECT compra_serie FROM compra WHERE compra_serie='$num_serie'");
            if($query->rowCount()==1){
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Número de comprobante ya existe",
                    "icon"=>"error"
                ];
            }else{
                $query2=mainModel::run_simple_query("SELECT compra_id FROM compra");
               
                $number=($query2->rowCount())+1;
                $codigo=mainModel::random_code("CMP-",6,$number);
                session_start(['name'=>'STR']);

                $usuario = $_SESSION['id_user_str'];

                $dataCompra=[
                    "Codigo"=>$codigo,
                    "TipoC"=>$tipo_documento,
                    "SerieC"=>$num_serie,
                    "NumC"=>$num_documento,
                    "Fecha"=>$fecha,
                    "Impuesto"=>$igv,
                    "Total"=>$total,
                    "Proveedor"=>$proveedor,
                    "Usuario"=>$usuario,
                    "Estado"=>"1"
                ];
                $saveCompra=comprasModels::add_model($dataCompra);
                if ($saveCompra->rowCount()>=1){

                    $obtainID=mainModel::run_simple_query("SELECT MAX(compra_id) as id  from compra");
                    $comp=$obtainID->fetch();
                    $p = $_POST['prod_id'];
                    $c = $_POST['quantity'];
                    $pc = $_POST['purchase_price'];
                    $fv = $_POST['fechaV']; 
                    $num_elementos=0;

                    $query3=mainModel::run_simple_query("SELECT lote_id FROM lote");
                    $number=($query3->rowCount())+1;
                    

                    while ($num_elementos < count($p)) {
                    $codigo_lote=mainModel::random_code("00",6,$number);
                    //Registrar detallegit 
                     $saveDetalle=comprasModels::add_detail_model($c[$num_elementos],$pc[$num_elementos]
                     ,$comp['id'],$p[$num_elementos]);
                    //Registrar lote 
                     $saveLote=comprasModels::add_lote_model($codigo_lote,$c[$num_elementos],$fv[$num_elementos]
                     ,$p[$num_elementos],$proveedor,$comp['id']);

                    $num_elementos=$num_elementos+1;
                    }   
                    if($saveDetalle->rowCount()>=1){
                        $alert=[
                            "Alert"=>"clean",
                            "title"=>"Operacion exitosa",
                            "text"=>"Compra registrada exitosamente",
                            "icon"=>"success"
                        ];
                    }else{
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"¡Error! No hemos podido registrar compra, en este momento",
                            "icon"=>"error"
                        ];
                    }
                }else {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! No hemos podido registrar compra, en este momento",
                        "icon"=>"error"
                    ];
                }
                
            }
            return mainModel::sweet_alert($alert);
        }
        public function list_controller(){
            session_start(['name'=>'STR']);
            $query = comprasModels::list_model();
            $data=Array();
            while ($reg=$query->fetch()) {

                $data[]=array(
                    "0"=>($reg['compra_estado'])?'
                    <div class="btn-group"><button class="btn btn-primary btn-sm edit"   title="Ver compra" id="'.mainModel::encryption($reg['compra_id']).'"><i class="fa fa-eye fa-xs"></i></button>'.' '.'<button class="btn btn-danger btn-sm anular"   title="Anular compra" id="'.mainModel::encryption($reg['compra_id']).'"><i class="fa fa-ban fa-xs"></i></button></div>':'<div class="btn-group"><button class="btn btn-primary btn-sm edit"   title="Ver compra" id="'.mainModel::encryption($reg['compra_id']).'"><i class="fa fa-eye fa-xs"></i></button></div>',
                    "1"=>$reg['compra_fecha'],
                    "2"=>$reg['proved_nombre'],
                    "3"=>$reg['usuario_nombre'],
                    "4"=>isset($reg['compra_tipoComprobante']) ? $reg['compra_tipoComprobante'] : '',
"5"=>(isset($reg['compra_serie']) ? $reg['compra_serie'] : '').'-'.(isset($reg['compra_numComprobante']) ? $reg['compra_numComprobante'] : ''),

                    "6"=>$_SESSION['simbolo_str'].formatMoney($reg['compra_total']),
                    "7"=>($reg['compra_estado'])?'<span class="badge badge-success">Aceptado</span>':'<span class="badge badge-danger">Anulado</span>'
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        } 
        public function list_prod_controller(){

            $query = comprasModels::list_prod_model();
            $data=Array();
            while ($reg=$query->fetch()) {
 
                $query2 = comprasModels::get_stock_model($reg['prod_id']);

                while ($obj=$query2->fetch()) {
                  
                    if(empty($obj['lote_stock'])){
                        $stock = '<span class="badge badge-danger">0</span>';
                    }else{
                        if($obj['lote_stock']>=15){
                            $stock = '<span class="badge badge-info">'.$obj['lote_stock'].'</span>';
                        }
                        if($obj['lote_stock']<15){
                            $stock = '<span class="badge badge-warning">'.$obj['lote_stock'].'</span>';
                        }
                    } 
                }
                $data[]=array(
                    "0"=>'<img src="'.$reg['prod_imagen'].'" width="40px">',
                    "1"=>$reg['prod_nombre'].' '.$reg['prod_concentracion'].' '.$reg['prod_adicional'],
                    "2"=>$stock,
                    "3"=>$reg['lab_nombre'], 
                    "4"=>$reg['tipo_nombre'],
                    "5"=>$reg['present_nombre'],
                    "6"=>'<button id="btnAgregarP" type="button" class="btn btn-info btn-sm" name="'.$reg['prod_id'].'" onclick="addDetail('.$reg['prod_id'].',\''.$reg['prod_nombre'].'\',\''.$reg['prod_concentracion'].'\',\''.$reg['prod_adicional'].'\')"><span class="fa fa-plus fa-xs"></span></button>'
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function cancel_purchase_controller(){

            $compra_id=mainModel::decryption($_POST['compra_id']);
            $compra_id=mainModel::clean_chain($compra_id);
            $rspta=comprasModels::cancel_purchase_model($compra_id);
            if ($rspta->rowCount()>=1) {                 
                $alert=[
                        "Alert"=>"simple",
                        "title"=>"Anulado",
                        "text"=>"Compra anulada exitoxamente",
                        "icon"=>"success"
                ];
            } else {
   
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido anular esta compra, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);       
        } 
public function show_controller(){
    $compra_id = mainModel::decryption($_POST['compra_id']);
    $compra_id = mainModel::clean_chain($compra_id);
    $query = comprasModels::show_model($compra_id);
    $rspta = $query->fetch(PDO::FETCH_ASSOC);

    if ($rspta) {
        $data = [
            "compra_id"             => $rspta["compra_id"] ?? "",
            "compra_fecha"          => $rspta["compra_fecha"] ?? "",
            "compra_total"          => $rspta["compra_total"] ?? "",
            "compra_impuesto"       => $rspta["compra_impuesto"] ?? "",
            "compra_tipoComprobante"=> $rspta["compra_tipoComprobante"] ?? "",
            "compra_serie"          => $rspta["compra_serie"] ?? "",
            "compra_numComprobante" => $rspta["compra_numComprobante"] ?? "",
            "proved_nombre"         => $rspta["proved_nombre"] ?? "",
            "compra_id_proveedor"   => $rspta["compra_id_proveedor"] ?? ""
        ];
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "No se encontró la compra."]);
    }
}

        public function show_detail_controller(){
            session_start(['name'=>'STR']);
            $compra_id=mainModel::decryption($_GET['id']);
            $compra_id=mainModel::clean_chain($compra_id);
            $query=comprasModels::show_detail_model($compra_id); 
            $query2=comprasModels::show_model($compra_id);
            $rspta= $query2->fetch();
            $subtotal=0;

            echo ' 
            <thead class="thead-light">
                <th >DESCRIPCIÓN</th>
                <th class="text-center">CANT.</th>
                <th class="text-center">P.U.</th>
                <th class="text-center">LOTE</th>
                <th class="text-center">VENCIMIENTO</th>
                <th class="text-center" >IMPORTE</th>
           </thead>';
           $i=1;

            while ($rows=$query->fetch()) {
                $query3=comprasModels::lote_producto_model($compra_id,$rows['detalleCompra_id_producto']);
                while($lote=$query3->fetch()){
                    $codigo_lote = $lote['lote_codigo'];
                    $fecha_lote = $lote['lote_fechaVencimiento'];
                }
                echo '
                <tr class="filas">
                    <td>'.$rows['prod_nombre'].' '.$rows['prod_concentracion'].' '.$rows['prod_adicional'].'</td>
                    <td class="text-center">'.$rows['detalleCompra_cantidad'].'</td>
                    <td class="text-center">'.$_SESSION['simbolo_str'].formatMoney($rows['detalleCompra_precioC']).'</td>
                    <td class="text-center">'.$codigo_lote.'</td>
                    <td class="text-center">'.$fecha_lote.'</td>
                    <td class="text-center">'.$_SESSION['simbolo_str'].formatMoney($rows['detalleCompra_precioC']*$rows['detalleCompra_cantidad']).'</td>
                </tr>';
                $subtotal=$subtotal+($rows['detalleCompra_precioC']*$rows['detalleCompra_cantidad']);
                $igv =  $subtotal * $rspta['compra_impuesto']/100;
                $total = $subtotal + $igv;

            }
            echo '
            <tfoot class="thead-light">
                <th>
                    <span>SUBTOTAL</span><br>
                    <span id="valor_impuesto">IGV '.$rspta['compra_impuesto'].' %</span><br>
                    <span>TOTAL</span>
                </th><th colspan="4"></th>
                <th>
                    <span id="total">'.$_SESSION['simbolo_str'].formatMoney($subtotal).'</span><br>
                    <span id="most_imp" maxlength="4">'.$_SESSION['simbolo_str'].formatMoney($igv).'</span><br>
                    <span id="most_total">'.$_SESSION['simbolo_str'].formatMoney($total).'</span>
                </>
            </tfoot>
            
            ';
        }   
        public function select_provider_controller(){
            $query=comprasModels::select_provider_model();
            $datas= $query->fetchAll();
            echo '<option value="0">Seleccionar</option>';
            foreach ($datas as $rows ) {
                echo '<option value="' . $rows['proved_id'].'">'.$rows['proved_nombre'].'</option>';
            }
        }  

    }