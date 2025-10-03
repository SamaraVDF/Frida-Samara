<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ .'/../models/Usuario.php';

class usuarioController{
    private Usuario $model;

    public function __construct(mysqli $conn){
        $this->model=new Usuario($conn);
        header('Content-Type:application/json; charset=utf-8');
    }

    /**POST REGISTRAR USUARIO */
    public function registrarUsuario(){
        $data=$this->input();

        if(!$this->valid($data,['nombre','apellido','edad','telefono','password'])){
            http_response_code(422);
            echo json_encode(['error'=>'Campos requeridos:nombre,apellido,edad,telefono,password']);
            return;
        }

        $id = isset($data['id']) ? (int)$data['id'] : null;

        $msg = $this->model->nuevoUsuario(
            $data['nombre'],
            $data['apellido'],
            (int)$data['edad'],
            (int)$data['telefono'],
            $data['password']
        );

        http_response_code(str_starts_with((string)$msg, 'Error') ? 400 : 201);
        echo json_encode(['message' => $msg]);
    }

    /*POST LOGIN*/
    public function loginUsuario(){
        $data=$this->input();

        if(!$this->valid($data,['usuario','password'])){
            http_response_code(422);
            echo json_encode(['error: '=>'campos requeridos:usuario,password']);
        }
        $id=isset($data['id']) ? (int)$data['id']:null;
        $msg=$this->model->loginUsuario(
            $data['usuario'],
            $data['password']
        );
        
        if (isset($msg['error'])) {
            http_response_code(401); 
        } else {
            http_response_code(200);
        }

        echo json_encode($msg);
    }
    public function datosUsuario(){
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(422);
        echo json_encode(['error' =>'campos requeridos: id']);
        return;
    }

    $msg = $this->model->datosUsuario((int)$id);

    if (isset($msg['error'])) {
        http_response_code(404); 
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

        // Validaciones extra para números
        if (isset($data['edad']) && filter_var($data['edad'], FILTER_VALIDATE_INT) === false) return false;
        if (isset($data['telefono']) && filter_var($data['telefono'], FILTER_VALIDATE_INT) === false) return false;

        return true;
    }
}