<?php
//se incluye la libreria de Slim
require 'Slim/Slim.php';
//require 'Slim/Middleware.php';
require 'Slim/Extras/Middleware/HttpBasicAuth.php';

//se instancia un objeto de la clase slim
$app = new Slim\Slim();

$app->add(new \Slim\Extras\Middleware\HttpBasicAuth('fede','123456'));
$app->get('/usuarios','getUsuarios');
$app->get('/usuarios/:id','getUsuario');
$app->get('/usuarios/buscar/:query','findXName');
$app->post('usuarios','addUsuario');
$app->put('/usuarios/:id','updateUsuario');
$app->delete('/usuarios/:id','deleteUsuario');

$app->run();

// metodo para realizar la conexion al a base de datos
function getConnection(){
    $dbhost='localhost';
    $dbuser='root';
    $dbpass='openix';
    $db='pruebaWebService';
    $conect = new PDO("mysql:dbname=$db;host=$dbhost",$dbuser,$dbpass);
    $conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conect;
}

//A continuacion se definen los distintos metodos que van a ser consumidos desde el web service

//metodo para devolver la info de todos los usuarios cuando se aacede por medio de GET
function getUsuarios(){
    $sql = "SELECT * FROM users ORDER BY user_fullname";
    try{
            $db = getConnection();
            $stmt = $db->query($sql);
            $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db=null;
            echo '{"usuario": '.json_encode($usuarios);
    }  catch (PDOException $e){
            echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

//metodo para devolver un unico usuario cuando se hace una peticion por GET y pasando como parametro la id
function getUsuario($id){
    $sql = "SELECT * FROM users WHERE user_id=:id";
    try {
            $db= getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $usuario = $stmt->fetchObject();
            $db=null;
            echo json_encode($usuario);
    }  catch (PDOException $e){
            echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

//metodo para agregar un usuario a la base de datos por medio de peticion POST
function addUsuario(){
    $request = Slim::getInstance()->request();
    $usuario = json_decode($request->getBody());
    $sql = "INSERT INTO user (user_fullname, user_email, user_password, user_status) VALUES (:nombre, :email, :password, :estado)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("nombre", $usuario->nombre);
        $stmt->bindParam("email", $usuario->email);
        $stmt->bindParam("password", $usuario->password);
        $stmt->bindParam("estado", $usuario->estado);
        $stmt->execute();
        $usuario->id = $db->lastInsertId();
        $db = null;
        echo json_encode($usuario);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

//metodo para actualizar el registro de un usuario por medio peticion PUT
function updateUsuario($id){
    $request = Slim::getInstance()->request();
    $body = $request->getBody();
    $usuario = json_decode($body);
    $sql = "UPDATE users SET user_fullname=:nombre, user_email=:email, user_password=:password, user_status=:estado WHERE user_id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
  $stmt->bindParam("nombre", $usuario->nombre);
        $stmt->bindParam("email", $usuario->email);
        $stmt->bindParam("password", $usuario->password);
        $stmt->bindParam("estado", $usuario->estado);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($usuario);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// metodo para elimiar usuario a partir de una peticion DELETE
function deleteUsuario($id) {
    $sql = "DELETE FROM users WHERE user_id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
//metodo para devolver la info de un usuario por medio de una peticion GET
function findXName($query) {
    $sql = "SELECT * FROM users WHERE UPPER(user_fullname) LIKE :query ORDER BY user_fullname";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"usuario": ' . json_encode($usuarios) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
?>