<?php


class Productos{
    private mysqli $conn;

    public function __construct($conexion){
        $this->conn=$conexion;
    }

    /**MOSTRAR PRODUCTOS */
    public function mostrarProductos(){
        $sql="SELECT 
        nombreProducto as nombre,
        Categoria as categoria,
        precioProducto as precio,
        Cantidad as cantidad,
        idProducto as Id
        FROM productos";
        $result=$this->conn->query($sql);
        $productos=[];

        if($result){
            while($row=$result->fetch_assoc()){
                $productos[]=$row;
            }
        }
        else{
            "error" .$this->conn->error;
        }
        return $productos;
    }
    
    /**ELIMINAR PRODUCTO */
    public function borrarProducto(string $nombre){
    $sql = "DELETE FROM productos WHERE nombreProducto=?";
    $stm = $this->conn->prepare($sql);

    if(!$stm) {
        return ["error" => "Error de preparación: " .$this->conn->error];
    }

    $stm->bind_param('s', $nombre);

    if(!$stm->execute()){
        return ["error" => "Error al borrar producto: " .$stm->error];
    }

    if($stm->affected_rows > 0){
        return ["ok" => true, "message" => "Producto borrado correctamente"];
    } else {
        return ["error" => "Producto no encontrado"];
    }
}

    /**INGRESAR PRODUCTO ----------*/
    public function nuevoProducto(string $nombre,float $precio,string $categoria,int $cantidad){
        $sql="INSERT INTO productos(nombreProducto,precioProducto,Categoria,Cantidad)
        VALUES(?,?,?,?)";
        $stm=$this->conn->prepare($sql);
        if(!$stm) return "Error: " .$this->conn->error;

        $stm->bind_param('sdsi',$nombre,$precio,$categoria,$cantidad);
        if (!$stm->execute()) {
            return "Error al registrar un producto: ".$stm->error;
        }

        return "Producto registrado";

    }

    /** ACTUALIZAR PRODUCTO--PRECIO */
    public function actualizarPrecio(string $nombre, float $precio) {
    $sql = "UPDATE productos SET precioProducto = ? WHERE nombreProducto = ?";
    $stm = $this->conn->prepare($sql);
    if (!$stm) return ["error" => "Error: " . $this->conn->error];

    $stm->bind_param('ds', $precio, $nombre);

    if (!$stm->execute()) {
        return ["error" => "Error al modificar el precio: " . $stm->error];
    }

    if ($stm->affected_rows > 0) {
        return ["ok" => true, "message" => "Precio modificado con éxito"];
    } else {
        return ["error" => "Producto no encontrado"];
    }
}


    /**ACTUALIZAR PRODUCTO--CANTIDAD */
    public function actualizarCantidad(string $nombre, int $cantidad) {
    $sql = "UPDATE productos SET Cantidad = ? WHERE nombreProducto = ?";
    $stm = $this->conn->prepare($sql);

    if (!$stm) {
        return ["error" => "Error en prepare: " . $this->conn->error];
    }

    $stm->bind_param('is', $cantidad, $nombre);

    if (!$stm->execute()) {
        return ["error" => "Error al modificar la cantidad: " . $stm->error];
    }

    if ($stm->affected_rows > 0) {
        return ["ok" => true, "message" => "Cantidad modificada con éxito"];
    } else {
        return ["error" => "Producto no encontrado"];
    }
}



    /**BUSQUEDA DE PRODUCTOS POR NOMBRE */
    public function buscarPorNombre(string $nombre){
        $sql="SELECT 
        nombreProducto as nombre,
        precioProducto as precio,
        Categoria as categoria
        FROM productos WHERE nombreProducto=?";

        $stm=$this->conn->prepare($sql);
        if(!$stm) return "Error" .$this->conn->error;

        $stm->bind_param('s',$nombre);
        if (!$stm->execute()) {
        return ["error" => "Error en la ejecución: " . $stm->error];
        }

        $res=$stm->get_result();
        $row=$res->fetch_assoc();

        if(!$row){
            return ['Error'=>'Producto no encontrado' ];
        }

        return["ok"=>true,"producto"=>$row];

    }
    /**BUSQUEDA DE PRODUCTOS POR CATEGORIA */
    public function buscarPorCategoria(string $categoria){
        $sql="SELECT 
        nombreProducto as nombre,
        precioProducto as precio
        FROM productos WHERE Categoria=?";

        $stm=$this->conn->prepare($sql);
        if(!$stm) return "Error" .$this->conn->error;

        $stm->bind_param('s',$categoria);
        if (!$stm->execute()) {
        return ["error" => "Error en la ejecución: " . $stm->error];
        }

        $res = $stm->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        if (!$rows || count($rows) === 0) {
            return ['Error' => 'Categoria no encontrada'];
        }

        return ["ok" => true, "productos" => $rows];


    }
    /**FILTRADO POR PRECIO  */
    public function filtradoPorPrecio(float $precio){
        $sql="SELECT 
        nombreProducto as nombre, 
        precioProducto as precio,
        Categoria as categoria
        FROM productos
        WHERE precioProducto <=?";

        
        $stm=$this->conn->prepare($sql);
        if(!$stm) return "Error" .$this->conn->error;

        $stm->bind_param('d',$precio);
        if (!$stm->execute()) {
        return ["error" => "Error en la ejecución: " . $stm->error];
        }

        $res = $stm->get_result();
        $rows = $res->fetch_all(MYSQLI_ASSOC);

        if (!$rows || count($rows) === 0) {
            return ['Error' => 'no encontrada'];
        }

        return ["ok" => true, "productos" => $rows];

    }
}