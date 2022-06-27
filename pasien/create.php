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
$id_pasien = $_POST['id_pasien'] ?? '';
$nama = $_POST['nama'] ?? '';
$tempat_lahir = $_POST['tempat_lahir'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? date('Y-m-d');
$tanggal_masuk = $_POST['tanggal_masuk'] ?? date('Y-m-d');
$no_bpjs = $_POST['no_bpjs'] ?? 'Tidak Punya';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$keluhan = $_POST['keluhan'] ?? '';
$dokter = $_POST['dokter'] ?? '';
$resepsionis = $_POST['resepsionis'] ?? '';

/**
 * Validation int value
 */
$idPFilter = filter_var($id_pasien, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($idPFilter === false){
    $reply['error'] = "ID Pasien harus format INT";
    $isValidated = false;
}
if(empty($id_pasien)){
    $reply['error'] = 'ID Pasien harus diisi';
    $isValidated = false;
}
if(empty($nama)){
    $reply['error'] = 'Nama harus diisi';
    $isValidated = false;
}
if(empty($tempat_lahir)){
    $reply['error'] = 'Tempat Lahir harus diisi';
    $isValidated = false;
}
if(empty($tanggal_lahir)){
    $reply['error'] = 'Tanggal Lahir harus diisi';
    $isValidated = false;
}
if(empty($tanggal_masuk)){
    $reply['error'] = 'Tanggal Masuk harus diisi';
    $isValidated = false;
}
if(empty($jenis_kelamin)){
    $reply['error'] = 'Jenis Kelamin harus diisi';
    $isValidated = false;
}
if(empty($alamat)){
    $reply['error'] = 'Alamat harus diisi';
    $isValidated = false;
}
if(empty($keluhan)){
    $reply['error'] = 'Keluhan harus diisi';
    $isValidated = false;
}
if(empty($dokter)){
    $reply['error'] = 'Dokter harus diisi';
    $isValidated = false;
}
if(empty($resepsionis)){
    $reply['error'] = 'Resepsionis harus diisi';
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
    $query = "INSERT INTO pasien (id_pasien, nama, tempat_lahir, tanggal_lahir, tanggal_masuk, no_bpjs, alamat, jenis_kelamin, keluhan, dokter, resepsionis) 
VALUES (:id_pasien, :nama, :tempat_lahir, :tanggal_lahir, :tanggal_masuk, :no_bpjs, :alamat, :jenis_kelamin, :keluhan, :dokter, :resepsionis)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_pasien", $id_pasien);
    $statement->bindValue(":nama", $nama);
    $statement->bindValue(":tempat_lahir", $tempat_lahir);
    $statement->bindValue(":tanggal_lahir", $tanggal_lahir);
    $statement->bindValue(":tanggal_masuk", $tanggal_masuk);
    $statement->bindValue(":no_bpjs", $no_bpjs);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":jenis_kelamin", $jenis_kelamin);
    $statement->bindValue(":keluhan", $keluhan);
    $statement->bindValue(":dokter", $dokter);
    $statement->bindValue(":resepsionis", $resepsionis);

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
$getResult = "SELECT * FROM pasien WHERE id_pasien = :id_pasien";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_pasien', $id_pasien);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/*
 * Get kategori
 */
$stmDokter = $connection->prepare("SELECT * FROM dokter where id_dokter = :id_dokter");
$stmDokter->bindValue(':id_dokter', $result['dokter']);
$stmDokter->execute();
$resultDokter = $stmDokter->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat kategori 'Tidak diketahui'
 */
$dokter = [
    'id' => $result['dokter'],
    'nama' => 'Tidak diketahui'
];
if ($resultDokter) {
    $dokter = [
        'id_dokter' => $resultDokter['id_dokter'],
        'nama' => $resultDokter['nama']
    ];
}

/*
 * Get kategori
 */
$stmResepsionis = $connection->prepare("SELECT * FROM resepsionis where id = :id");
$stmResepsionis->bindValue(':id', $result['resepsionis']);
$stmResepsionis->execute();
$resultResepsionis = $stmResepsionis->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat kategori 'Tidak diketahui'
 */
$resepsionis = [
    'id' => $result['resepsionis'],
    'nama' => 'Tidak diketahui'
];
if ($resultResepsionis) {
    $resepsionis = [
        'id' => $resultResepsionis['id'],
        'nama' => $resultResepsionis['nama']
    ];
}

/*
 * Transform result
 */
$dataFinal = [
    'id_pasien' => $result['id_pasien'],
    'nama' => $result['nama'],
    'tempat_lahir' => $result['tempat_lahir'],
    'tanggal_lahir' => $result['tanggal_lahir'],
    'tanggal_masuk' => $result['tanggal_masuk'],
    'no_bpjs' => $result['no_bpjs'],
    'alamat' => $result['alamat'],
    'jenis_kelamin' => $result['jenis_kelamin'],
    'keluhan' => $result['keluhan'],
    'created_at' => $result['created_at'],
    'dokter' => $dokter,
    'resepsionis' => $resepsionis
];

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);