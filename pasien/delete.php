<?php
include "../config/koneksi.php";
/**
 * @var $connection PDO
 */

/*
 * Validate http method
 */


/**
 * Get input data from RAW data
 */
$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$id_pasien = $res['id_pasien'] ?? '';

/**
 *
 * Cek apakah ISBN tersedia
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
 * Hapus data
 */
try{
    $queryCheck = "DELETE FROM pasien where id_pasien = :id_pasien";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_pasien', $id_pasien);
    $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Send output
 */
header('Content-Type: application/json');
$reply['status'] = true;
echo json_encode($reply);