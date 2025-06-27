<?php
    if($ajaxRequest){
        require_once "../Models/reporteVentasModels.php";
    }else{
        require_once "./Models/reporteVentasModels.php";
    }
 
    class reporteVentasControllers extends reporteVentasModels{

        public function count_items_controller(){
            session_start(['name'=>'STR']);
            $day=reporteVentasModels::count_day_model();
            $d= $day->fetch();
            $count_day = $d['total'];

            $month=reporteVentasModels::count_month_model();
            $m= $month->fetch();
            $count_month = $m['total'];

            $year=reporteVentasModels::count_year_model();
            $y= $year->fetch();
            $count_year = $y['total'];

            echo 
            '
            <div class="col-12 col-sm-12 col-md-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>'.$count_day.'</h3>
                        <p>Ventas diarias</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-4">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>'.$count_month.'</h3>
                        <p>Ventas mensuales</p>
                    </div>
                    <div class="icon ">
                        <i class="ion ion-calendar"></i>
                    </div>
                </div>
            </div>
                <!-- ./col -->
            <div class="col-12 col-sm-12 col-md-4">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>'.$count_year.'</h3>
                        <p>Ventas Anuales</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-clipboard"></i>
                    </div>
                </div>
            </div>
            ';
        }
        public function sales_statistics_controller(){
            session_start(['name'=>'STR']);
            error_reporting(0);
            $statistics = reporteVentasModels::sales_statistics_model();
            $respuesta = $statistics->fetchAll();

            $arrayFechas = array();
            $arrayVentas = array();
            $sumaPagosMes = array();
            
            foreach ($respuesta as $key => $value) {
            
                #Capturamos sólo el año y el mes
                $fecha = substr($value["venta_fecha"],0,7);
            
                #Introducir las fechas en arrayFechas
                array_push($arrayFechas, $fecha);
            
                #Capturamos las ventas
                $arrayVentas = array($fecha => $value["venta_total"]);
            
                #Sumamos los pagos que ocurrieron el mismo mes
                foreach ($arrayVentas as $key => $value) {
                    
                    $sumaPagosMes[$key] += $value;
                }
            
            }
            
            $noRepetirFechas = array_unique($arrayFechas);

            echo 
            '
            <section class="col-lg-12 connectedSortable ui-sortable">
                <div class="card bg-white">
                <div class="card-header  ui-sortable-handle" style="cursor: move;">
                    <h3 class="card-title text-secundary">
                        <i class="fas fa-chart-bar mr-1"></i>Gráfico de ventas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart" id="line-chart-ventas" style="height: 250px;"></div>
                </div>
                </div>
            </section>
            <script>
                var line = new Morris.Line({
                    element          : "line-chart-ventas",
                    resize           : true,
                    data             : [
            ';
            if($noRepetirFechas != null){

                foreach($noRepetirFechas as $key){
        
                    echo "{ y: '".$key."', ventas: ".$sumaPagosMes[$key]." },";
        
        
                }
        
                echo "{y: '".$key."', ventas: ".$sumaPagosMes[$key]." }";
        
            }else{
        
               echo "{ y: '0', ventas: '0' }";
        
            }
         echo '
                    ],
                    xkey             : "y",
                    ykeys            : ["ventas"],
                    labels           : ["ventas"],
                    lineColors       : ["#25D5F2"],
                    lineWidth        : 2,
                    hideHover        : "auto",
                    gridTextColor    : "#537375",
                    gridStrokeWidth  : 0.4,
                    pointSize        : 4,
                    pointStrokeColors: ["#efefef"],
                    gridLineColor    : "#AAAAAA",
                    gridTextFamily   : "Open Sans",
                    preUnits         : "'.$_SESSION['simbolo_str'].'",
                    gridTextSize     : 10
                });
            </script>

            ';
        }
        public function seller_controller(){
            session_start(['name'=>'STR']);
            error_reporting(0);
            $venta = reporteVentasModels::show_sale_model();
            $ventas = $venta->fetchAll();
            $usuario = reporteVentasModels::show_users_model();
            $usuarios = $usuario->fetchAll();

        

            $arrayVendedores = array();
$arraylistaVendedores = array();
$sumaTotalVendedores = array(); // <-- ESTA LÍNEA ES LA CLAVE


            foreach ($ventas as $key => $valueVentas) { 

                foreach ($usuarios as $key => $valueUsuarios) {

                    if($valueUsuarios["usuario_id"] == $valueVentas["venta_id_usuario"]){

                        #Capturamos los vendedores en un array
                        array_push($arrayVendedores, $valueUsuarios["usuario_nombre"]);

                        #Capturamos las nombres y los valores netos en un mismo array
                        $arraylistaVendedores = array($valueUsuarios["usuario_nombre"] => $valueVentas["venta_total"]);

                        #Sumamos los netos de cada vendedor

                        foreach ($arraylistaVendedores as $key => $value) {

                            $sumaTotalVendedores[$key] += $value;

                        }

                    }
                
                }

            }

            $noRepetirNombres = array_unique($arrayVendedores);

            echo '
  
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-dollar-sign mr-1"></i>Ganancias por vendedor</h3>
                </div>
                <div class="card-body">
                    <div id="bar-chart" style="height: 300px;"></div>
                </div>
            </div>
            <script>
                var line = new Morris.Bar({
                element          : "bar-chart",
                resize           : true,
                data             : [
            ';
                if($noRepetirNombres != null){
                    foreach($noRepetirNombres as $value){
                        echo "{y: '".$value."', a: '".$sumaTotalVendedores[$value]."'},";
                    }
                }else{
                    echo "{ y: 'Vendedor', a: '0' }";
                }
            echo ' ],
                    barColors: ["#A88BE0"],
                    xkey: "y",
                    ykeys: ["a"],
                    labels: ["ganancias"],
                    preUnits: "'.$_SESSION['simbolo_str'].'",
                    hideHover: "auto"
                });
            </script>
            ';
        }  
        public function sale_seller_controller(){
            session_start(['name'=>'STR']);
            $query=reporteVentasModels::sales_seller_model();
            $data=Array();
            while ($reg=$query->fetch()) {
 
                $data[]=array(
                    "perfil"=>$reg['usuario_perfil'],
                    "vendedor"=>$reg['usuario_nombre'],
                    "cargo"=>$reg['usuario_cargo'],
                    "ventas"=>$reg['ventas'],
                    "ganancias"=>$_SESSION['simbolo_str'].formatMoney($reg['ganancias'])
                );
            }
            $jasonstring = json_encode($data);
            echo $jasonstring;
        }
    } 