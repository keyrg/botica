<?php
if ($ajaxRequest) {
    require_once "../Core/mainModel.php";
} else {
    require_once "./Core/mainModel.php";
}

class inicioModels extends mainModel {

    protected function count_ventas_model() {
        $sql = mainModel::connect()->prepare("
            SELECT COALESCE(SUM(venta_total), 0) as total 
            FROM venta 
            WHERE DATE(venta_fecha) = CURRENT_DATE
        ");
        $sql->execute();
        return $sql;
    }

    protected function count_compras_model() {
        $query = mainModel::connect()->prepare("
            SELECT COALESCE(SUM(compra_total), 0) as total 
            FROM compra 
            WHERE DATE(compra_fecha) = CURRENT_DATE 
            AND compra_estado = TRUE
        ");
        $query->execute();
        return $query;
    }

    protected function count_usuarios_model() {
        $sql = mainModel::connect()->prepare("
            SELECT COUNT(usuario_id) as usuarios 
            FROM usuario
        ");
        $sql->execute();
        return $sql;
    }

    protected function count_productos_model() {
        $sql = mainModel::connect()->prepare("
            SELECT COUNT(prod_id) as productos 
            FROM producto
        ");
        $sql->execute();
        return $sql;
    }

    protected function sales_statistics_model() {
        $sql = mainModel::connect()->prepare("
            SELECT * 
            FROM venta 
            WHERE venta_estado = TRUE 
            AND EXTRACT(YEAR FROM venta_fecha) = EXTRACT(YEAR FROM CURRENT_DATE) 
            ORDER BY venta_id ASC
        ");
        $sql->execute();
        return $sql;
    }

    protected function recently_product_model() {
        $sql = mainModel::connect()->prepare("
            SELECT * 
            FROM producto 
            JOIN presentacion ON prod_id_present = present_id  
            ORDER BY prod_id DESC 
            LIMIT 6
        ");
        $sql->execute();
        return $sql;
    }

   protected function purchase_statistics_model() {
    $sql = mainModel::connect()->prepare("
        SELECT TO_CHAR(compra_fecha::date, 'Month') AS fecha, 
               SUM(compra_total) AS total 
        FROM compra 
        WHERE EXTRACT(YEAR FROM compra_fecha::date) = EXTRACT(YEAR FROM CURRENT_DATE)
        GROUP BY EXTRACT(MONTH FROM compra_fecha::date), TO_CHAR(compra_fecha::date, 'Month') 
        ORDER BY EXTRACT(MONTH FROM compra_fecha::date) DESC 
        LIMIT 12
    ");
    $sql->execute();
    return $sql;
}

}
