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
$id_dokter = $_GET['id_dokter'] ?? '';

if(empty($id_dokter)){
    $reply['error'] = 'ID Dokter tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM dokter where id_dokter = :id_dokter";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_dokter', $id_dokter);
    $statement->execute();
    $dataDokter = $statement->fetch(PDO::FETCH_ASSOC);


        /*
         * Transoform hasil query dari table buku dan kategori
         * Gabungkan data berdasarkan kolom id kategori
         * Jika id kategori tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id_dokter' => $dataDokter['id_dokter'],
            'nama' => $dataDokter['nama'],
            'alamat' => $dataDokter['alamat'],
            'spesialis' => $dataDokter['spesialis'],
            'ruangan' => $dataDokter['ruangan'],
            'jadwal' => $dataDokter['jadwal'],
            'jenis_kelamin' => $dataDokter['jenis_kelamin'],
            'no_telepon' => $dataDokter['no_telepon']
        ];

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
    $reply['error'] = 'Data tidak ditemukan dengan ID Dokter '.$id_dokter;
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