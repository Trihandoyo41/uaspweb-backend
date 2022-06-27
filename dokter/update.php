<?php
include "../config/koneksi.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
/**
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$id_dokter = $formData['id_dokter'] ?? '';
$nama = $formData['nama'] ?? '';
$alamat = $formData['alamat'] ?? '';
$spesialis = $formData['spesialis'] ?? '';
$ruangan = $formData['ruangan'] ?? '';
$jadwal = $formData['jadwal'] ?? '';
$jenis_kelamin = $formData['jenis_kelamin'] ?? '';
$no_telepon = $formData['no_telepon'] ?? '';

/**
 * Validation int value
 */
$idFilter = filter_var($id_dokter, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($idFilter === false){
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM dokter where id_dokter = :id_dokter";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_dokter', $idFilter);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID '.$idFilter;
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE dokter SET nama = :nama, alamat = :alamat, spesialis = :spesialis, ruangan = :ruangan, jadwal = :jadwal, jenis_kelamin = :jenis_kelamin, no_telepon = :no_telepon WHERE id_dokter = :id_dokter";
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

/**
 * Show output to client
 */
header('Content-Type: application/json');
$reply['status'] = $isOk;
echo json_encode($reply);