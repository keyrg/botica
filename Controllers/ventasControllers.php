<?php
    if($ajaxRequest){
        require_once "../Models/ventasModels.php";
    }else{
        require_once "./Models/ventasModels.php";
    }
 
    class ventasControllers extends ventasModels{

        public function add_controller(){
    $cliente = mainModel::decryption($_POST['cliente']);
    $cliente = mainModel::clean_chain($cliente);
    $fecha = mainModel::clean_chain($_POST['fecha']);
    $tipo_comprobante = mainModel::decryption($_POST['tipo_comprobante']);
    $tipo_comprobante = mainModel::clean_chain($tipo_comprobante);
    $serie_comprobante = mainModel::clean_chain($_POST['serie_comprobante']);
    $num_comprobante = mainModel::clean_chain($_POST['num_comprobante']);
    $impuesto = mainModel::clean_chain($_POST['impuesto']);
    $total_venta = mainModel::clean_chain($_POST['total_venta']);

    $query = mainModel::run_simple_query("SELECT venta_id FROM venta");
    $number = ($query->rowCount()) + 1;
    $codigo = mainModel::random_code("VTN-", 6, $number);
    session_start(['name' => 'STR']);
    $usuario = $_SESSION['id_user_str'];

    $dataVenta = [
        "Codigo" => $codigo,
        "TipoC" => $tipo_comprobante,
        "SerieC" => $serie_comprobante,
        "NumC" => $num_comprobante,
        "Fecha" => $fecha,
        "Impuesto" => $impuesto,
        "Total" => $total_venta,
        "Usuario" => $usuario,
        "Cliente" => $cliente,
        "Estado" => "1"
    ];

    $idvc = null;
    $saveVenta = ventasModels::add_model($dataVenta);

    if ($saveVenta->rowCount() >= 1) {
        $obtainID = mainModel::run_simple_query("SELECT MAX(venta_id) as id FROM venta");
        $vent = $obtainID->fetch();
        $idvc = $vent['id'];
        $p = $_POST['prod_id'];
        $c = $_POST['cantidad'];
        $pc = $_POST['precio_venta'];
        $des = $_POST['descuento'];
        $num_elementos = 0;

        while ($num_elementos < count($p)) {
            for ($i = 0; $i < $c[$num_elementos]; $i++) {
                $lote = mainModel::run_simple_query("SELECT lote_cantUnitario, lote_id FROM lote WHERE lote_id_producto = {$p[$num_elementos]} AND lote_cantUnitario > 0 LIMIT 1");
                $loteCant = $lote->fetch();
                $loteCantO = $loteCant['lote_cantUnitario'] - 1;
                $loteId = $loteCant['lote_id'];
                ventasModels::update_lote($loteCantO, $loteId);
            }

            ventasModels::add_detail_model($c[$num_elementos], $pc[$num_elementos], $des[$num_elementos], $idvc, $p[$num_elementos]);
            $num_elementos++;
        }

        $alert = [
            "Alert" => "sales",
            "title" => "Operacion exitosa",
            "text" => "Venta registrada exitosamente",
            "icon" => "success"
        ];
    } else {
        $alert = [
            "Alert" => "simple",
            "title" => "Ocurrió un error inesperado",
            "text" => "¡Error! No hemos podido registrar venta, en este momento",
            "icon" => "error"
        ];
    }

    return json_encode([
        "alert" => mainModel::sweet_alert($alert),
        "id" => $idvc ? mainModel::encryption($idvc) : null,
        "comprobante" => $tipo_comprobante
    ]);
}

        public function list_controller(){
            session_start(['name'=>'STR']);
            $query = ventasModels::list_model();
            $data=Array();
            while ($reg=$query->fetch()){
                if ($reg['comprobante_nombre']=='Ticket') {
                    $url= '../Reports/ticket.php?id=';
                }else{
                    $url= '../Reports/voucher.php?id=';
                }
                $data[]=array(
                    "0"=>($reg['venta_estado'])?'
                    <div class="btn-group"><button class="btn btn-primary btn-sm edit"   title="Ver venta" id="'.mainModel::encryption($reg['venta_id']).'"><i class="fa fa-eye fa-xs"></i></button>'.' '.'<button class="btn btn-danger btn-sm anular"   title="Anular venta" id="'.mainModel::encryption($reg['venta_id']).'"><i class="fa fa-ban fa-xs"></i></button>'.' '.'<a class="btn btn-success btn-sm"   title="Imprimir '.$reg['comprobante_nombre'].'" target="_blank" href="'.$url.mainModel::encryption($reg['venta_id']).'"><i class="fa fa-print fa-xs"></i></a>'.' '.'<a class="btn btn-info btn-sm"  title="Descargar '.$reg['comprobante_nombre'].'" href="'.$url.mainModel::encryption($reg['venta_id']).'" download="'.$reg['comprobante_nombre'].'"><i class="fa fa-download fa-xs"></i></a></div>':'<div class="btn-group"><button class="btn btn-primary btn-sm edit"   title="Ver venta" id="'.mainModel::encryption($reg['venta_id']).'"><i class="fa fa-eye fa-xs"></i></button>'.' '.'<a class="btn btn-success btn-sm"   title="Imprimir '.$reg['comprobante_nombre'].'" target="_blank" href="'.$url.mainModel::encryption($reg['venta_id']).'"><i class="fa fa-print fa-xs"></i></a>'.' '.'<a class="btn btn-info btn-sm"  title="Descargar '.$reg['comprobante_nombre'].'" href="'.$url.mainModel::encryption($reg['venta_id']).'" download="'.$reg['comprobante_nombre'].'"><i class="fa fa-download fa-xs"></i></a></div>',
                    "1"=>substr($reg['venta_fecha'],0,10),
                    "2"=>$reg['cliente_nombre'],
                    "3"=>$reg['usuario_nombre'], 
                    "4"=>$reg['comprobante_nombre'], 
                    "5"=>$reg['venta_serie'].'-'.$reg['venta_numComprobante'],
                    "6"=>$_SESSION['simbolo_str'].formatMoney($reg['venta_total']),
                    "7"=>($reg['venta_estado'])?'<span class="badge badge-success">Aceptado</span>':'<span class="badge badge-danger">Anulado</span>'
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
            session_start(['name'=>'STR']);
            $query = ventasModels::list_prod_model();
            $data=Array();
            $fecha_actual = new DateTime(); 
            while ($reg=$query->fetch()) {
 
                $query2 = ventasModels::get_stock_model($reg['prod_id']);

                while ($obj=$query2->fetch()) {
                  
                    if(empty($obj['lote_stock'])){
                        $stock = '<span class="badge badge-danger">0</span>';
                        $cantidad = 0;
                    }else{
                        if($obj['lote_stock']>=15){
                            $stock = '<span class="badge badge-info">'.$obj['lote_stock'].'</span>';
                        }
                        if($obj['lote_stock']<15){
                            $stock = '<span class="badge badge-warning">'.$obj['lote_stock'].'</span>';
                        }
                        $cantidad = $obj['lote_stock'];
                    }
                }

                if($cantidad !== 0)
                {
                    $data[]=array(
                        "0"=>'<img src="'.$reg['prod_imagen'].'" width="40px">',
                        "1"=>$reg['prod_nombre'].' '.$reg['prod_concentracion'].' '.$reg['prod_adicional'],
                        "2"=>$stock,
                        "3"=>$_SESSION['simbolo_str'].formatMoney($reg['prod_precioV']),
                        "4"=>$reg['lab_nombre'], 
                        "5"=>$reg['tipo_nombre'],
                        "6"=>$reg['present_nombre'],
                        "7"=>'<button id="btnAgregarP" type="button" class="btn btn-info btn-sm" title="Aregar" name="'.$reg['prod_id'].'" onclick="addDetail('.$reg['prod_id'].',\''.$reg['prod_nombre'].'\',\''.$reg['prod_concentracion'].'\',\''.$reg['prod_adicional'].'\','.$reg['prod_precioV'].','.$cantidad.')"><span class="fa fa-plus fa-xs"></span></button>'
                    );
                } 
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function barcode_prod_controller(){ 
            $barcode = $_POST['barcode'];
            $resp = ventasModels::barcode_prod_model($barcode);
            $result = array();
            while($row = $resp->fetch())
            {
                
                $stock = ventasModels::get_stock_model($row['prod_id'])->fetch();
                
                $result[0] = array(
                    'id'=>$row['prod_id'],
                    'producto'=>$row['prod_nombre'],
                    'concentracion'=>$row['prod_concentracion'],
                    'adicional'=>$row['prod_adicional'],
                    'precio'=>$row['prod_precioV'],
                    'cantidad'=>$stock['lote_stock']
                );
            }
            echo json_encode($result);
        }
        public function cancel_sale_controller(){

            $venta_id=mainModel::decryption($_POST['venta_id']);
            $venta_id=mainModel::clean_chain($venta_id);
            $rspta=ventasModels::cancel_sale_model($venta_id);
            if ($rspta->rowCount()>=1) {                 
                $alert=[
                        "Alert"=>"simple",
                        "title"=>"Anulado",
                        "text"=>"Venta anulada exitoxamente",
                        "icon"=>"success"
                ];
            } else {
   
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido anular esta venta, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);       
        } 
        public function show_controller(){
            $venta_id=mainModel::decryption($_POST['venta_id']);
            $venta_id=mainModel::clean_chain($venta_id);
            $query=ventasModels::show_model($venta_id);
            $rspta= $query->fetch(); 
            echo json_encode($rspta);  
        }  
        public function show_detail_controller(){
            session_start(['name'=>'STR']);
            $venta_id=mainModel::decryption($_GET['id']);
            $venta_id=mainModel::clean_chain($venta_id);
            $query=ventasModels::show_detail_model($venta_id);
            $query2=ventasModels::show_model($venta_id);
            $rspta= $query2->fetch();
            $subtotal=0;

            echo ' 
            <thead class="thead-light">
                <th >DESCRIPCIÓN</th>
                <th class="text-center">CANT.</th>
                <th class="text-center">P.U.</th>
                <th class="text-center">DESCUENTO</th>
                <th class="text-center" >IMPORTE</th>
           </thead>';
           $i=1;

            while ($rows=$query->fetch()) {
                echo '
                <tr class="filas">
                    <td>'.$rows['prod_nombre'].' '.$rows['prod_concentracion'].' '.$rows['prod_adicional'].'</td>
                    <td class="text-center">'.$rows['detalleVenta_cantidad'].'</td>
                    <td class="text-center">'.$_SESSION['simbolo_str'].formatMoney($rows['detalleVenta_precioV']).'</td>
                    <td class="text-center">'.$_SESSION['simbolo_str'].formatMoney($rows['detalleVenta_descuento']).'</td>
                    <td class="text-center">'.$_SESSION['simbolo_str'].formatMoney($rows['detalleVenta_precioV']*$rows['detalleVenta_cantidad']).'</td>
                </tr>';
               
                $subtotal=$subtotal+($rows['detalleVenta_precioV']*$rows['detalleVenta_cantidad']-$rows['detalleVenta_descuento']);
                $igv =  $subtotal * $rspta['venta_impuesto']/100;
                $total = $subtotal + $igv;

            }
            echo '
            <tfoot class="thead-light">
                <th>
                    <span>SUBTOTAL</span><br>
                    <span id="valor_impuesto">IGV '.$rspta['venta_impuesto'].' %</span><br>
                    <span>TOTAL</span>
                </th><th></th><th></th><th></th>
                <th>
                    <span id="total">'.$_SESSION['simbolo_str'].formatMoney($subtotal).'</span><br>
                    <span id="most_imp" maxlength="4">'.$_SESSION['simbolo_str'].formatMoney($igv).'</span><br>
                    <span id="most_total">'.$_SESSION['simbolo_str'].formatMoney($total).'</span>
                </th>
            </tfoot>
            ';
        }   
        public function select_cliente_controller(){
            $query=ventasModels::select_cliente_model();
            $datas= $query->fetchAll();
            echo '<option value="0">Seleccionar</option>';
            foreach ($datas as $rows ) {
                echo '<option value="'.mainModel::encryption($rows['cliente_id']).'">'.$rows['cliente_nombre'].'  -    DNI:    '. $rows['cliente_dni'].'</option>';
            }
        }  
        public function select_comprobante_controller(){
            $query=ventasModels::select_comprobante_model();
            $datas= $query->fetchAll();
            foreach ($datas as $rows ) {
                echo '<option value="'.mainModel::encryption($rows['comprobante_id']).'">'.$rows['comprobante_nombre'].'</option>';
            }
        } 
        public function select_pago_controller(){
            $query=ventasModels::select_pago_model();
            $datas= $query->fetchAll();
            echo '<option >Seleccionar</option>';
            foreach ($datas as $rows ) {
                echo '<option value="'.$rows['pago_id'].'">'.$rows['pago_nombre'].'</option>';
            }
        }  

    }