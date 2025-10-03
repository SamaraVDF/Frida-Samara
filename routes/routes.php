<?php
require_once __DIR__ . '/../controllers/usuarioController.php';
require_once __DIR__ . '/../controllers/empleadoController.php';
require_once __DIR__ . '/../controllers/productosController.php';
require_once __DIR__ . '/../controllers/ventaController.php';
require_once __DIR__ . '/../config/conexion.php';

$controller = new usuarioController($conn);
$controllerEmpleado=new UsuarioEmpleadoController($conn);
$controllerProductos=new ProductosController($conn);
$controllerVenta=new ventaController($conn);


$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/api/index.php/usuario/registrar' && $method === 'POST') {
        $controller->registrarUsuario();
} elseif ($uri === '/api/index.php/empleado/registrar' && $method === 'POST') {
        $controllerEmpleado->registrarEmpleado();
} elseif($uri==='/api/index.php/empleado/login' && $method==='POST' ){
        $controllerEmpleado->loginEmpleado();
} elseif($uri==='/api/index.php/usuario/login' && $method==='POST' ){
        $controller->loginUsuario();
}
elseif($uri==='/api/index.php/usuario/datos' && $method==='GET' ){
        $controller->datosUsuario();
}
elseif($uri==='/api/index.php/empleado/datos' && $method==='GET' ){
        $controllerEmpleado->datosEmpleado();
}elseif($uri==='/api/index.php/productos/datos' && $method==='GET' ){
        $controllerProductos->mostrarProductos();
}elseif($uri==='/api/index.php/productos/borrar' && $method==='DELETE' ){
        $controllerProductos->borrarProducto();
}elseif($uri==='/api/index.php/productos/registrar' && $method==='POST' ){
        $controllerProductos->RegistraProducto();
}elseif($uri==='/api/index.php/productos/actualizarPrecio' && $method==='PUT' ){
        $controllerProductos->modificarPrecioProducto();
}elseif($uri==='/api/index.php/productos/actualizarCantidad' && $method==='PUT' ){
        $controllerProductos->modificarCantidadProducto();
}elseif($uri==='/api/index.php/productos/buscarPorNombre' && $method==='POST' ){
        $controllerProductos->buscarProductoPorNombre();
}elseif($uri==='/api/index.php/productos/buscarPorCategoria' && $method==='POST' ){
        $controllerProductos->buscarProductoPorCategoria();
}elseif($uri==='/api/index.php/productos/filtradoDePrecio' && $method==='POST' ){
        $controllerProductos->buscarPorPrecio();
}elseif($uri==='/api/index.php/venta/registrar' && $method==='POST' ){
        $controllerVenta->crearVenta();
}elseif($uri==='/api/index.php/venta/mostrar' && $method==='GET' ){
        $controllerVenta->mostrarVenta();
}else {
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
}

