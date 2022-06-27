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

$id_pasien = $formData['id_pasien'] ?? '';
$nama = $formData['nama'] ?? '';
$tempat_lahir = $formData['tempat_lahir'] ?? '';
$tanggal_lahir = $formData['tanggal_lahir'] ?? date('Y-m-d');
$tanggal_masuk = $formData['tanggal_masuk'] ?? date('Y-m-d');
$no_bpjs = $formData['no_bpjs'] ?? 'Tidak Punya';
$jenis_kelamin = $formData['jenis_kelamin'] ?? '';
$alamat = $formData['alamat'] ?? '';
$keluhan = $formData['keluhan'] ?? '';
$dokter = $formData['dokter'] ?? '';
$resepsionis = $formData['resepsionis'] ?? '';

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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM pasien where id_pasien = :id_pasien";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_pasien', $id_pasien);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan dengan ID Pasien '.$id_pasien;
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
    $query = "UPDATE pasien SET nama = :nama, tempat_lahir = :tempat_lahir, tanggal_lahir = :tanggal_lahir, tanggal_masuk = :tanggal_masuk, no_bpjs = :no_bpjs, alamat = :alamat, jenis_kelamin = :jenis_kelamin, keluhan = :keluhan, dokter = :dokter, resepsionis = :resepsionis 
WHERE id_pasien = :id_pasien";
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
 * Get data
 */
$stmSelect = $connection->prepare("SELECT * FROM pasien where id_pasien = :id_pasien");
$stmSelect->bindValue(':id_pasien', $id_pasien);
$stmSelect->execute();
$dataPasien = $stmSelect->fetch(PDO::FETCH_ASSOC);

/*
 * Ambil data kategori berdasarkan kolom kategori
 */
$dataFinal = [];
if($dataPasien) {
    $stmDokter = $connection->prepare("select * from dokter where id_dokter = :id_dokter");
    $stmDokter->bindValue(':id_dokter', $dataPasien['dokter']);
    $stmDokter->execute();
    $resultDokter = $stmDokter->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat kategori 'Tidak diketahui'
     */
    $dokter = [
        'id_dokter' => $dataPasien['dokter'],
        'nama' => 'Tidak diketahui'
    ];
    if ($resultDokter) {
        $dokter = [
            'id_dokter' => $resultDokter['id_dokter'],
            'nama' => $resultDokter['nama']
        ];
    }

    $stmResepsionis = $connection->prepare("select * from resepsionis where id = :id");
    $stmResepsionis->bindValue(':id', $dataPasien['resepsionis']);
    $stmResepsionis->execute();
    $resultResepsionis = $stmResepsionis->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat kategori 'Tidak diketahui'
     */
    $resepsionis = [
        'id' => $dataPasien['resepsionis'],
        'nama' => 'Tidak diketahui'
    ];
    if ($resultResepsionis) {
        $resepsionis = [
            'id' => $resultResepsionis['id'],
            'nama' => $resultResepsionis['nama']
        ];
    }

    /*
     * Transoform hasil query dari table buku dan kategori
     * Gabungkan data berdasarkan kolom id kategori
     * Jika id kategori tidak ditemukan, default "tidak diketahui'
     */
    $dataFinal = [
        'id_pasien' => $dataPasien['id_pasien'],
        'nama' => $dataPasien['nama'],
        'tempat_lahir' => $dataPasien['tempat_lahir'],
        'tanggal_lahir' => $dataPasien['tanggal_lahir'],
        'tanggal_masuk' => $dataPasien['tanggal_masuk'],
        'no_bpjs' => $dataPasien['no_bpjs'],
        'alamat' => $dataPasien['alamat'],
        'jenis_kelamin' => $dataPasien['jenis_kelamin'],
        'keluhan' => $dataPasien['keluhan'],
        'created_at' => $dataPasien['created_at'],
        'dokter' => $dokter,
        'resepsionis' => $resepsionis
    ];
}

/**
 * Show output to client
 */
header('Content-Type: application/json');
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);