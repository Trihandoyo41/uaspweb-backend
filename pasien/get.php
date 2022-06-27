<?php
include "../config/koneksi.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

$dataFinal = [];
$id_pasien = $_GET['id_pasien'] ?? '';

if(empty($id_pasien)){
    $reply['error'] = 'ID Pasien tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM pasien where id_pasien = :id_pasien";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_pasien', $id_pasien);
    $statement->execute();
    $dataPasien = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori berdasarkan kolom kategori
     */
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
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Show response
 */
if(!$dataFinal){
    $reply['error'] = 'Data tidak ditemukan dengan ID Pasien '.$id_pasien;
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Otherwise show data
 */
header('Content-Type: application/json');
$reply['status'] = true;
$reply['data'] = $dataFinal;
echo json_encode($reply);