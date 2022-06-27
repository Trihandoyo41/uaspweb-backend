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

$id = $formData['id'] ?? '';
$nama = $formData['nama'] ?? '';
$alamat = $formData['alamat'] ?? '';
$jam = $formData['jam'] ?? '';
$jenis_kelamin = $formData['jenis_kelamin'] ?? '';
$no_telepon = $formData['no_telepon'] ?? '';

/**
 * Validation int value
 */
$idFilter = filter_var($id, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($idFilter === false){
    $reply['error'] = "ID resepsionis harus format INT";
    $isValidated = false;
}
if(empty($id)){
    $reply['error'] = 'ID resepsionis harus diisi';
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM resepsionis where id = :id";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id', $idFilter);
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
    $query = "UPDATE resepsionis SET nama = :nama, alamat = :alamat, jam = :jam, jenis_kelamin = :jenis_kelamin, no_telepon = :no_telepon WHERE id = :id";
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

/**
 * Show output to client
 */
header('Content-Type: application/json');
$reply['status'] = $isOk;
echo json_encode($reply);