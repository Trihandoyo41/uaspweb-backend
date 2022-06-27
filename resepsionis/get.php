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
$id = $_GET['id'] ?? '';

if(empty($id)){
    $reply['error'] = 'ID tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM resepsionis where id = :id";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id', $id);
    $statement->execute();
    $dataResepsionis = $statement->fetch(PDO::FETCH_ASSOC);


        /*
         * Transoform hasil query dari table buku dan kategori
         * Gabungkan data berdasarkan kolom id kategori
         * Jika id kategori tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id' => $dataResepsionis['id'],
            'nama' => $dataResepsionis['nama'],
            'alamat' => $dataResepsionis['alamat'],
            'jam' => $dataResepsionis['jam'],
            'jenis_kelamin' => $dataResepsionis['jenis_kelamin'],
            'no_telepon' => $dataResepsionis['no_telepon']
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
    $reply['error'] = 'Data tidak ditemukan dengan ID Dokter '.$id;
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