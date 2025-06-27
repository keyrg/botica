<?php
    if($ajaxRequest){
        require_once "../Core/mainModel.php";
    }else{
        require_once "./Core/mainModel.php";
    }

    class reporteVentasModels extends mainModel{

        protected function count_day_model(){
            $sql = mainModel::connect()->prepare("
                SELECT COUNT(venta_id) as total 
                FROM venta 
                WHERE DATE(venta_fecha) = CURRENT_DATE
            ");
            $sql->execute();
            return $sql; 
        }

        protected function count_month_model(){
            $query = mainModel::connect()->prepare("
                SELECT COUNT(venta_id) as total 
                FROM venta 
                WHERE EXTRACT(MONTH FROM venta_fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
                AND EXTRACT(YEAR FROM venta_fecha) = EXTRACT(YEAR FROM CURRENT_DATE)
            ");
            $query->execute();
            return $query;
        }

        protected function count_year_model(){ 
            $sql = mainModel::connect()->prepare("
                SELECT COUNT(venta_id) as total 
                FROM venta 
                WHERE EXTRACT(YEAR FROM venta_fecha) = EXTRACT(YEAR FROM CURRENT_DATE)
            ");
            $sql->execute();
            return $sql;
        }

        protected function count_productos_model(){
            $sql = mainModel::connect()->prepare("SELECT COUNT(prod_id) as productos FROM producto");
            $sql->execute();
            return $sql;
        }

        protected function sales_statistics_model(){ 
            $sql = mainModel::connect()->prepare("
                SELECT * 
                FROM venta 
                WHERE venta_estado='1' 
                AND EXTRACT(YEAR FROM venta_fecha) = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY venta_id ASC
            ");
            $sql->execute();
            return $sql;
        }

        protected function show_sale_model(){ 
            $sql = mainModel::connect()->prepare("
                SELECT * 
                FROM venta 
                WHERE venta_estado='1' 
                AND EXTRACT(YEAR FROM venta_fecha) = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY venta_id ASC
            ");
            $sql->execute();
            return $sql;
        }  

        protected function show_users_model(){ 
            $sql = mainModel::connect()->prepare("SELECT * FROM usuario");
            $sql->execute();
            return $sql;
        }  

        protected function sales_seller_model(){ 
            $sql = mainModel::connect()->prepare("
                SELECT usuario_perfil, usuario_nombre, usuario_cargo,
                       COUNT(venta_id) as ventas,
                       SUM(venta_total) as ganancias 
                FROM venta
                JOIN usuario ON venta_id_usuario = usuario_id 
                WHERE DATE(venta_fecha) = CURRENT_DATE 
                GROUP BY usuario_perfil, usuario_nombre, usuario_cargo
            ");
            $sql->execute();
            return $sql;
        } 
    }
