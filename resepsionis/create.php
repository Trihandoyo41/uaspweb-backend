<?php
include "../config/koneksi.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(400);
    $reply['error'] = 'POST method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data POST
 */
$id = $_POST['id'] ?? '';
$nama = $_POST['nama'] ?? '';
$no_telepon = $_POST['no_telepon'] ?? '';
$jam = $_POST['jam'] ?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$alamat = $_POST['alamat'] ?? '';

/**
 * Validation int value
 */
$idDFilter = filter_var($id, FILTER_VALIDATE_INT);


/**
 * Validation empty fields
 */
$isValidated = true;
if($idDFilter === false){
    $reply['error'] = "ID harus format INT";
    $isValidated = false;
}
if(empty($id)){
    $reply['error'] = 'ID harus diisi';
    $isValidated = false;
}
if(empty($nama)){
    $reply['error'] = 'Nama resepsionis harus diisi';
    $isValidated = false;
}
if(empty($alamat)){
    $reply['error'] = 'Alamat harus diisi';
    $isValidated = false;
}
if(empty($jam)){
    $reply['error'] = 'Jam harus diisi';
    $isValidated = false;
}
if(empty($jenis_kelamin)){
    $reply['error'] = 'Jenis kelamin harus diisi';
    $isValidated = false;
}
if(empty($no_telepon)){
    $reply['error'] = 'Nomor telepon harus diisi';
    $isValidated = false;
}
/*
 * Jika filter gagal
 */
if(!$isValidated){
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $query = "INSERT INTO resepsionis (id, nama, alamat, jam, jenis_kelamin, no_telepon) 
VALUES (:id, :nama, :alamat, :jam, :jenis_kelamin, :no_telepon)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id", $id);
    $statement->bindValue(":nama", $nama);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":jam", $jam);
    $statement->bindValue(":jenis_kelamin", $jenis_kelamin);
    $statement->bindValue(":no_telepon", $no_telepon);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/*
 * Get last data
 */
$getResult = "SELECT * FROM resepsionis WHERE id = :id";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id', $id);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);


/**
 * Show output to client
 * Set status info true
 */
header('Content-Type: application/json');
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);