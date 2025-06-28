<?php
if ($ajaxRequest) {
    require_once "../Models/ventasModels.php";
} else {
    require_once "./Models/ventasModels.php";
}

class ventasControllers extends ventasModels
{
    public function add_controller()
    {
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
        $saveVenta = ventasModels::add_model($dataVenta);

        if ($saveVenta->rowCount() >= 1) {
            $obtainID = mainModel::run_simple_query("SELECT MAX(venta_id) as id FROM venta");
            $vent = $obtainID->fetch();
            $idvc = $vent['id'];

            $p = $_POST['prod_id'] ?? [];
            $c = $_POST['cantidad'] ?? [];
            $pc = $_POST['precio_venta'] ?? [];
            $des = $_POST['descuento'] ?? [];

            if (empty($p) || empty($c) || empty($pc) || empty($des)) {
                $alert = [
                    "Alert" => "simple",
                    "title" => "Datos incompletos",
                    "text" => "Debe agregar al menos un producto antes de registrar la venta.",
                    "icon" => "warning"
                ];
                return json_encode(["alert" => mainModel::sweet_alert($alert)]);
            }

            $num_elementos = 0;
            while ($num_elementos < count($p)) {
                for ($i = 0; $i < $c[$num_elementos]; $i++) {
                    $lote = mainModel::run_simple_query("SELECT lote_cantUnitario, lote_id FROM lote WHERE lote_id_producto = {$p[$num_elementos]} AND lote_cantUnitario > 0 LIMIT 1");
                    $loteCant = $lote->fetch();
                    if ($loteCant) {
                        $loteCantO = $loteCant['lote_cantUnitario'] - 1;
                        $loteId = $loteCant['lote_id'];
                        ventasModels::update_lote($loteCantO, $loteId);
                    }
                }

                $saveDetalle = ventasModels::add_detail_model(
                    $c[$num_elementos],
                    $pc[$num_elementos],
                    $des[$num_elementos],
                    $vent['id'],
                    $p[$num_elementos]
                );

                $num_elementos++;
            }

            if (isset($saveDetalle) && $saveDetalle->rowCount() >= 1) {
                $alert = [
                    "Alert" => "sales",
                    "title" => "Operación exitosa",
                    "text" => "Venta registrada exitosamente",
                    "icon" => "success"
                ];
            } else {
                $alert = [
                    "Alert" => "simple",
                    "title" => "Ocurrió un error inesperado",
                    "text" => "¡Error! No hemos podido registrar venta en este momento",
                    "icon" => "error"
                ];
            }
        } else {
            $alert = [
                "Alert" => "simple",
                "title" => "Ocurrió un error inesperado",
                "text" => "¡Error! No hemos podido registrar venta en este momento",
                "icon" => "error"
            ];
        }

        return json_encode([
            "alert" => mainModel::sweet_alert($alert),
            "id" => mainModel::encryption($idvc ?? 0),
            "comprobante" => $tipo_comprobante
        ]);
    }

    public function list_controller()
    {
        session_start(['name' => 'STR']);
        $query = ventasModels::list_model();
        $data = [];

        while ($reg = $query->fetch()) {
            $url = ($reg['comprobante_nombre'] == 'Ticket')
                ? '../Reports/ticket.php?id='
                : '../Reports/voucher.php?id=';

            $buttons = '
                <div class="btn-group">
                    <button class="btn btn-primary btn-sm edit" title="Ver venta" id="' . mainModel::encryption($reg['venta_id']) . '"><i class="fa fa-eye fa-xs"></i></button>
                    ' . ($reg['venta_estado']
                    ? '<button class="btn btn-danger btn-sm anular" title="Anular venta" id="' . mainModel::encryption($reg['venta_id']) . '"><i class="fa fa-ban fa-xs"></i></button>'
                    : '') . '
                    <a class="btn btn-success btn-sm" title="Imprimir" target="_blank" href="' . $url . mainModel::encryption($reg['venta_id']) . '"><i class="fa fa-print fa-xs"></i></a>
                    <a class="btn btn-info btn-sm" title="Descargar" href="' . $url . mainModel::encryption($reg['venta_id']) . '" download="' . $reg['comprobante_nombre'] . '"><i class="fa fa-download fa-xs"></i></a>
                </div>';

            $data[] = [
                "0" => $buttons,
                "1" => substr($reg['venta_fecha'], 0, 10),
                "2" => $reg['cliente_nombre'],
                "3" => $reg['usuario_nombre'],
                "4" => $reg['comprobante_nombre'],
                "5" => $reg['venta_serie'] . '-' . $reg['venta_numComprobante'],
                "6" => $_SESSION['simbolo_str'] . formatMoney($reg['venta_total']),
                "7" => $reg['venta_estado']
                    ? '<span class="badge badge-success">Aceptado</span>'
                    : '<span class="badge badge-danger">Anulado</span>'
            ];
        }

        echo json_encode([
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ]);
    }

    public function cancel_sale_controller()
    {
        $venta_id = mainModel::decryption($_POST['venta_id']);
        $venta_id = mainModel::clean_chain($venta_id);
        $rspta = ventasModels::cancel_sale_model($venta_id);

        $alert = ($rspta->rowCount() >= 1) ? [
            "Alert" => "simple",
            "title" => "Anulado",
            "text" => "Venta anulada exitosamente",
            "icon" => "success"
        ] : [
            "Alert" => "simple",
            "title" => "Ocurrió un error inesperado",
            "text" => "¡Error! No hemos podido anular esta venta, en este momento",
            "icon" => "error"
        ];

        return mainModel::sweet_alert($alert);
    }

    public function show_controller()
    {
        $venta_id = mainModel::decryption($_POST['venta_id']);
        $venta_id = mainModel::clean_chain($venta_id);
        $query = ventasModels::show_model($venta_id);
        echo json_encode($query->fetch());
    }

    public function select_cliente_controller()
    {
        $query = ventasModels::select_cliente_model();
        echo '<option value="0">Seleccionar</option>';
        foreach ($query->fetchAll() as $rows) {
            echo '<option value="' . mainModel::encryption($rows['cliente_id']) . '">' . $rows['cliente_nombre'] . ' - DNI: ' . $rows['cliente_dni'] . '</option>';
        }
    }

    public function select_comprobante_controller()
    {
        $query = ventasModels::select_comprobante_model();
        foreach ($query->fetchAll() as $rows) {
            echo '<option value="' . mainModel::encryption($rows['comprobante_id']) . '">' . $rows['comprobante_nombre'] . '</option>';
        }
    }

    public function select_pago_controller()
    {
        $query = ventasModels::select_pago_model();
        echo '<option>Seleccionar</option>';
        foreach ($query->fetchAll() as $rows) {
            echo '<option value="' . $rows['pago_id'] . '">' . $rows['pago_nombre'] . '</option>';
        }
    }
}
