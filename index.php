<?php
require __DIR__.'/vendor/autoload.php';
include 'excel_reader2.php';

use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel</title>
</head>
<body>
    <h1>Import Excel to Firebase</h1><br>
    <form method="post" enctype="multipart/form-data">
        <p>Pilih file excel yang akan di import</p>
        <input type="file" name="fileimport">
        <br><br>
        <input type="submit" name="btnimport" value="Import Excel">
    </form>

    <?php 
        if (isset($_POST['btnimport'])) {
            if (isset($_FILES['fileimport'])) {

                //Tentukan alamat firebase realtime database
                $factory = (new Factory())

                //Isi dengan alamat Firebase realtime database anda
                ->withDatabaseUri('https://alamat/firebase/anda');
                
                //Initialisasi variable database
                $database = $factory->createDatabase();

                // upload file xls
                $target = basename($_FILES['fileimport']['name']) ;
                move_uploaded_file($_FILES['fileimport']['tmp_name'], $target);
                
                // beri permisi agar file xls dapat di baca
                chmod($_FILES['fileimport']['name'],0777);
                
                // mengambil isi file xls
                $data = new Spreadsheet_Excel_Reader($_FILES['fileimport']['name'],false);
                
                // menghitung jumlah baris data sheet1
                $jumlah_baris_sheet1 = $data->rowcount(0);
                //Import sheet mahasiswa
                for ($i=2; $i<=$jumlah_baris_sheet1; $i++) {
                    // menangkap data dan memasukkan ke variabel sesuai dengan kolumnya masing-masing
                    $nimMahasiswa    = $data->val($i, 1, 0);
                    $namaMahasiswa   = $data->val($i, 2, 0);
                    $kelasMahasiswa  = $data->val($i, 3, 0);
                    $prodiMahasiswa  = $data->val($i, 4, 0);
                    
                    //Masukkan data hasil import ke firebase
                    $database->getReference('mahasiswa')->push([
                        'nim' => $nimMahasiswa,
                        'nama' => $namaMahasiswa,
                        'kelas' => $kelasMahasiswa,
                        'prodi' => $prodiMahasiswa,
                        ]
                    );
                }
    
                unlink($_FILES['fileimport']['name']);
            }
        }
    ?>
</body>
</html>