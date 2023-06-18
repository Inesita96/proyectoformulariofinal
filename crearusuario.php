<?php

  header ('Content-Type: application/json');
  $data = array();

  $nombre = $_POST['nombre'];
  $primerapellido = $_POST['primerApellido'];
  $segundoapellido = $_POST['segundoApellido'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  $data = validateData($data, $nombre, $primerapellido, $segundoapellido, $email, $password);

  if($data['error'] ==''){
    $data = insertarEnBD($data, $nombre, $primerapellido, $segundoapellido, $email, $password);
  }

  echo json_encode($data);

  function validarNombre($texto) {
    $regexNames = "/^(([a-zA-Z]+(\s[a-zA-Z]+)*$))/i";
    if(empty($texto) || !preg_match($regexNames,$texto)){
      return  "Error NOMBRE/APELLIDOS: Formato de nombre o apellido inválido, no puede contener caracteres numéricos\n";
    }
    return '';
  }

  function validarEmail($email) {
    $regexEmail = "/^(([A-Za-z0-9_!#$%&'*+\/=?`{|}~^.-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,3}))/i";
    if(empty($email) || !preg_match($regexEmail, $email)){
      return  "Error EMAIL: Formato de email inválido debe seguir el siguiente formato xxxxxxx@xxxxxx.xxx\n";
    }
    return '';
  }

  function validarPassword($password){
    $passwordlength = strlen($password);
    if($passwordlength < 4 || $passwordlength  > 8){
      return  "Error CLAVE: La clave tiene que tener al menos 8 caracteres.\n";
    }
    return '';
  }

  function validateData($data, $nombre, $primerapellido, $segundoapellido, $email, $password){
    $data['error']  = validarNombre($nombre);
    $data['error'] .= validarNombre($primerapellido);
    $data['error'] .= validarNombre($segundoapellido);
    $data['error'] .= validarEmail($email);
    $data['error'] .= validarPassword($password);
    return $data;
  }

  function insertarEnBD($data, $nombre, $primerapellido, $segundoapellido, $email, $password){
    $servername = "localhost";
    $username = "root";
    $passworddb = "";
    $dbname = "cursosql";
    try {
      $conn = new mysqli($servername, $username, $passworddb, $dbname);
      if($conn->connect_error){
        $data['error'] .= "Error: connection failed: " . $conn->connect_error;
        $conn->close();
      }else{
        $selectByEmail = "SELECT * FROM usuario WHERE email LIKE '$email'";
        $resultEmail = $conn->query($selectByEmail);
  
        $insert ="INSERT INTO `usuario` (`nombre`, `primerapellido`, `segundoapellido`, `email`, `password`) 
        VALUES ('$nombre', '$primerapellido', '$segundoapellido', '$email', '$password')";
        if($resultEmail->num_rows > 0){
          $data['error'] .= "Error: El email indicado ya se encuentra registrado en el sistema\n";
        }
        elseif($conn->query($insert) === TRUE) {
          $data['success'] = "Ha sido registrado exitosamente\n";
        } else{
          $data['error'] .= "Error: " . $insert . "<br>" . $conn->error;
        }
        $conn->close();
      }
    } catch (Exception $e) {
      $data['error'] .= "Error: No se puede conectar a base de datos";
    }
    
    return $data;
  }
?>