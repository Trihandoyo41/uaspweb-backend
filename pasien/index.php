<?php
include "../config/koneksi.php";
/**
 * @var $connection PDO
 */
try{
    /**
     * Prepare query pasien limit 50 rows
     */
    $statement = $connection->prepare("select * from pasien order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsPasien = $statement->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori
     */
    $stmDokter = $connection->prepare("select * from dokter");
    $isOk = $stmDokter->execute();
    $resultDokter = $stmDokter->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori
     */
    $stmResepsionis = $connection->prepare("select * from resepsionis");
    $isOk = $stmResepsionis->execute();
    $resultResepsionis = $stmResepsionis->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Transoform hasil query dari table buku dan kategori
     * Gabungkan data berdasarkan kolom id kategori
     * Jika id kategori tidak ditemukan, default "tidak diketahui'
     */
    $finalResults = [];
    $idsDokter = array_column($resultDokter, 'id_dokter');
    $idsResepsionis = array_column($resultResepsionis, 'id');
    foreach ($resultsPasien as $pasien){
        /*
         * Default kategori 'Tidak diketahui'
         */
        $dokter = [
            'id_dokter' => $pasien['dokter'],
            'nama' => 'Tidak diketahui'
        ];
        /*
         * Cari kategori berd id
         */
        $findByIdDokter = array_search($pasien['dokter'], $idsDokter);

        /*
         * Jika id ditemukan
         */
        if($findByIdDokter !== false){
            $findDataDokter = $resultDokter[$findByIdDokter];
            $dokter = [
                'id_dokter' => $findDataDokter['id_dokter'],
                'nama' => $findDataDokter['nama']
            ];
        }

        $resepsionis = [
            'id' => $pasien['resepsionis'],
            'nama' => 'Tidak diketahui'
        ];
        /*
         * Cari kategori berd id
         */
        $findByIdResepsionis = array_search($pasien['resepsionis'], $idsResepsionis);

        /*
         * Jika id ditemukan
         */
        if($findByIdResepsionis !== false){
            $findDataResepsionis = $resultResepsionis[$findByIdResepsionis];
            $resepsionis = [
                'id' => $findDataResepsionis['id'],
                'nama' => $findDataResepsionis['nama']
            ];
        }

        $finalResults[] = [
            'id_pasien' => $pasien['id_pasien'],
            'nama' => $pasien['nama'],
            'tempat_lahir' => $pasien['tempat_lahir'],
            'tanggal_lahir' => $pasien['tanggal_lahir'],
            'tanggal_masuk' => $pasien['tanggal_masuk'],
            'no_bpjs' => $pasien['no_bpjs'],
            'alamat' => $pasien['alamat'],
            'jenis_kelamin' => $pasien['jenis_kelamin'],
            'keluhan' => $pasien['keluhan'],
            'created_at' => $pasien['created_at'],
            'dokter' => $dokter,
            'resepsionis' => $resepsionis
        ];
    }

    $reply['data'] = $finalResults;
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
/*
 * Query OK
 * set status == true
 * Output JSON
 */
header('Content-Type: application/json');
$reply['status'] = true;
echo json_encode($reply);