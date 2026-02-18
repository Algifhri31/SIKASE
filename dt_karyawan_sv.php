<?php
session_start(); 
include 'koneksi.php';
if (isset($_POST['simpan'])) {
	
	$id_karyawan = $_POST['id_karyawan'];
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	$nama = $_POST['nama'];
	$tmp_tgl_lahir = $_POST['tmp_tgl_lahir'];
	$jenkel = $_POST['jenkel'];
	$agama = $_POST['agama'];
	$alamat = $_POST['alamat'];
	$no_tel = $_POST['no_tel'];
	$jabatan = $_POST['jabatan'];


	//untuk gambar
	$foto     = $_FILES['foto']['name'];
	$tmp      = $_FILES['foto']['tmp_name'];
	$fotobaru = date('dmYHis').$foto;
	$path     = "images/".$fotobaru;
}

// Cek apakah ID sudah ada
$sql = "SELECT * FROM tb_karyawan WHERE id_karyawan = '".$id_karyawan."'";
$tambah = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($tambah) > 0) {
	echo "<script>alert('Data Dengan No.KSE = ".$id_karyawan." sudah ada') </script>";
	echo "<script>window.location.href = \"datakaryawan.php\" </script>";
	exit();
}

// Upload file
if (move_uploaded_file($tmp, $path)) {
	$query = "INSERT INTO tb_karyawan set id_karyawan='$id_karyawan', username='$username', password='$password', nama='$nama', tmp_tgl_lahir='$tmp_tgl_lahir', jenkel='$jenkel', agama='$agama', alamat='$alamat', no_tel='$no_tel', jabatan='$jabatan', foto='$fotobaru'";
	$result = mysqli_query($koneksi, $query);

	if ($result) {
		header("location: datakaryawan.php");
	} else {
		echo "gagal menyimpan data";
	}
} else {
	echo "gagal upload file";
}

 ?>