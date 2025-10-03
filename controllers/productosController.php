<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ .'/../models/Productos.php';

class productosController{
    private Productos $model;

    public function __construct(mysqli $conn)
    {
        $this->model=new Productos($conn);
        header('Content-Type:application/json; charset=utf-8');
    }

    /*GET MOSTRAR DATOS */ 
    public function mostrarProductos() {
    $productos = $this->model->mostrarProductos();

    if (empty($productos)) {
        http_response_code(404);
        echo json_encode(["error" => "No hay productos disponibles"]);
    } else {
        http_response_code(200);
        echo json_encode(["ok" => true, "productos" => $productos]);
        }
    }


    /**DELETE BORRAR PRODUCTO */
    public function borrarProducto(){
    $data = $this->input();

    if(!$this->valid($data, ['nombre'])){
        http_response_code(422);
        echo json_encode(['error' => 'Campos requeridos: nombre']);
        return;
    }

    $msg = $this->model->borrarProducto($data['nombre']);

    if(isset($msg['error'])){
        http_response_code(400);
    } else {
        http_response_code(200);
    }

    echo json_encode($msg); 
}


    /*POST REGISTRAR PRODUCTO*/
    public function RegistraProducto() {
    $data = $this->input();

    if (!$this->valid($data, ['nombre','precio','categoria','cantidad'])) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'Campos requeridos: nombre, precio, categoria, cantidad']);
        return;
    }

    $msg = $this->model->nuevoProducto(
        $data['nombre'],
        (float)$data['precio'],
        $data['categoria'],
        (int)$data['cantidad']
    );

    if (str_starts_with((string)$msg, 'Error')) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $msg]);
    } else {
        http_response_code(201);
        echo json_encode([
            'ok' => true,
            'message' => $msg
        ]);
    }
}


    /** PUT MODIFICAR EL PRECIO */
public function modificarPrecioProducto(){
    $data = $this->input();

    if (!$this->valid($data, ['nombre','precio'])) {
        http_response_code(422);
        echo json_encode(['error' => 'Campos requeridos: nombre, precio']);
        return;
    }

    $msg = $this->model->actualizarPrecio(
        $data['nombre'],
        (float)$data['precio']
    );

    if (isset($msg['error'])) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }

    echo json_encode($msg); 
}


    /**PUT MODIFICAR CANTIDAD */
    public function modificarCantidadProducto(){
        $data=$this->input();

        if(!$this->valid($data,['nombre','cantidad'])){
            http_response_code(422);
            echo json_encode(['error'=>'Campos requeridos:nombre,cantidad']);
            return;
        }
            $id = isset($data['id']) ? (int)$data['id'] : null;

        $msg = $this->model->actualizarCantidad(
            $data['nombre'],
            (int)$data['cantidad'],
        );

        if (isset($msg['error'])) {
            http_response_code(400);
        } else {
            http_response_code(200);
        }

        echo json_encode($msg); 
    }

    

    /**GET BUSCAR POR NOMBRE */
    public function buscarProductoPorNombre(){
    $data = $this->input();
    
    if(!$this->valid($data,['nombre'])){
        http_response_code(422);
        echo json_encode(['error'=>'Campos requeridos: nombre']);
        return;
    }

    $msg = $this->model->buscarPorNombre($data['nombre']);

    if (isset($msg['error'])) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }

    echo json_encode($msg);
}
    /**GET BUSCAR POR CATEGORIA */

    public function buscarProductoPorCategoria(){
        $data = $this->input();
        
        if(!$this->valid($data, ['categoria'])){
            http_response_code(422);
            echo json_encode(['error' => 'Campos requeridos: categoria']);
            return;
        }

        $msg = $this->model->buscarPorCategoria($data['categoria']);

        if (isset($msg['error']) || isset($msg['Error'])) {
            http_response_code(400);
        } else {
            http_response_code(200);
        }

            echo json_encode($msg); 
}


    /*GET FILTRADO POR PRECIO*/
    public function buscarPorPrecio(){
    $data = $this->input();

    if(!$this->valid($data, ['precio'])){
        http_response_code(422);
        echo json_encode(['error'=>'Campos requeridos: precio']);
        return;
    }

    $msg = $this->model->filtradoPorPrecio((float)$data['precio']); 

    if (isset($msg['error']) || isset($msg['Error'])) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }

    echo json_encode($msg);
}


    private function input(): array {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $raw = file_get_contents('php://input');

        if (stripos($contentType, 'application/json') !== false) {
            $data = json_decode($raw, true);
            return is_array($data) ? $data : [];
        }

    
        if (!empty($_POST)) {
            return $_POST;
        }

    
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function valid(array &$data, array $required): bool {
        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) return false;

            if (is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
            if ($data[$field] === '' || $data[$field] === null) return false;
        }
        return true;
    }
}