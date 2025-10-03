<?php
class Venta {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /* CREAR VENTA */
    public function crearVenta(array $items) {
        $this->conn->begin_transaction(); 

        try {
            $total = 0;

            // Insertar venta en blanco
            $sqlVenta = "INSERT INTO venta (total) VALUES (0)";
            $this->conn->query($sqlVenta);
            $ventaId = $this->conn->insert_id;

            foreach ($items as $item) {
                $idProd = (int)$item['id'];
                $cantidad = (int)$item['cantidad'];

                // Buscar precio del producto
                $sqlProd = "SELECT precioProducto FROM productos WHERE idProducto=?";
                $stm = $this->conn->prepare($sqlProd);
                $stm->bind_param("i", $idProd);
                $stm->execute();
                $res = $stm->get_result();
                $prod = $res->fetch_assoc();

                if (!$prod) {
                    throw new Exception("Producto con id $idProd no encontrado");
                }

                $precio = $prod['precioProducto'];
                $subtotal = $precio * $cantidad;
                $total += $subtotal;

                // Insertar detalle de la venta
                $sqlDet = "INSERT INTO detalle_venta (venta_id, producto_id, cantidad, subtotal) 
                           VALUES (?,?,?,?)";
                $stmDet = $this->conn->prepare($sqlDet);
                $stmDet->bind_param("iiid", $ventaId, $idProd, $cantidad, $subtotal);
                $stmDet->execute();
            }

            // Actualizar total en la venta
            $sqlUpdate = "UPDATE venta SET total=? WHERE idVenta=?";
            $stmUp = $this->conn->prepare($sqlUpdate);
            $stmUp->bind_param("di", $total, $ventaId);
            $stmUp->execute();

            $this->conn->commit(); 

            return ["ok" => true, "venta_id" => $ventaId, "total" => $total];

        } catch (Exception $e) {
            $this->conn->rollback(); 
            return ["error" => $e->getMessage()];
        }
    }

    /* MOSTRAR VENTA */
    public function mostrarVENTA(){
        $sql = "SELECT 
                    v.idVenta AS idVenta,
                    v.total AS Total,
                    d.producto_id AS IdProducto,
                    d.cantidad AS Cantidad,
                    p.nombreProducto AS Nombre,
                    d.subtotal AS Subtotal
                FROM venta v
                JOIN detalle_venta d ON d.venta_id = v.idVenta
                JOIN productos p ON p.idProducto = d.producto_id";

        $result = $this->conn->query($sql);
        $venta = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $venta[] = $row;
            }
        } else {
            return ["error" => $this->conn->error];
        }
        return $venta;
    }
}
?>

