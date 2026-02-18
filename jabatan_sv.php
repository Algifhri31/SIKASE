<?php 
include 'koneksi.php';
if (isset($_POST['simpan'])) {
	$jabatan = $_POST['jabatan'];

}

$save = "INSERT INTO tb_jabatan SET jabatan='$jabatan'";
$result = mysqli_query($koneksi, $save);

if ($result) {
	header("location: datajabatan.php");
}else{
	echo "gagal disimpan";
}

 ?>