<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ .'/../models/venta.php';

class ventaController{
    private Venta $model;

    public function __construct(mysqli $conn){
        $this->model=new Venta($conn);
        header('Content-Type:application/json; charset=utf-8');
    }
    public function crearVenta() {

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if ($input === null) {
            http_response_code(400);
            echo json_encode([
                'error' => 'JSON inválido o vacío',
                'raw' => $raw
            ]);
            return;
        }
        $data = $input['items'] ?? $input ?? null;

        if (!$data || !is_array($data)) {
            http_response_code(422);
            echo json_encode([
                'error' => 'Debes enviar al menos un producto',
                'parsed_input' => $input
            ]);
            return;
        }

        $msg = $this->model->crearVenta($data);

        if (isset($msg['error'])) {
            http_response_code(400);
            echo json_encode(['error' => $msg['error']]);
        } else {
            http_response_code(201);
            echo json_encode([
                'ok' => true,
                'venta_id' => $msg['venta_id'],
                'total' => $msg['total']
            ]);
        }
    }

    /*GET MOSTRAR DATOS */ 
    public function mostrarVenta(){
        echo json_encode($this->model->mostrarVENTA());
    }

}


?>