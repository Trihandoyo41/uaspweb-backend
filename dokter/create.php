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
$id_dokter = $_POST['id_dokter'] ?? '';
$nama = $_POST['nama'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$spesialis = $_POST['spesialis'] ?? '';
$ruangan = $_POST['ruangan'] ?? '';
$jadwal = $_POST['jadwal'] ?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$no_telepon = $_POST['no_telepon'] ?? '';

/**
 * Validation int value
 */
$idDFilter = filter_var($id_dokter, FILTER_VALIDATE_INT);


/**
 * Validation empty fields
 */
$isValidated = true;
if($idDFilter === false){
    $reply['error'] = "ID Dokter harus format INT";
    $isValidated = false;
}
if(empty($id_dokter)){
    $reply['error'] = 'ID Dokter harus diisi';
    $isValidated = false;
}
if(empty($nama)){
    $reply['error'] = 'Nama Dokter harus diisi';
    $isValidated = false;
}
if(empty($alamat)){
    $reply['error'] = 'Alamat harus diisi';
    $isValidated = false;
}
if(empty($spesialis)){
    $reply['error'] = 'Spesialis harus diisi';
    $isValidated = false;
}
if(empty($ruangan)){
    $reply['error'] = 'Ruangan harus diisi';
    $isValidated = false;
}
if(empty($jadwal)){
    $reply['error'] = 'Jadwal harus diisi';
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
    $query = "INSERT INTO dokter (id_dokter, nama, alamat, spesialis, ruangan, jadwal, jenis_kelamin, no_telepon) 
VALUES (:id_dokter, :nama, :alamat, :spesialis, :ruangan, :jadwal, :jenis_kelamin, :no_telepon)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_dokter", $id_dokter);
    $statement->bindValue(":nama", $nama);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":spesialis", $spesialis);
    $statement->bindValue(":ruangan", $ruangan);
    $statement->bindValue(":jadwal", $jadwal);
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
$getResult = "SELECT * FROM dokter WHERE id_dokter = :id_dokter";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_dokter', $id_dokter);
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