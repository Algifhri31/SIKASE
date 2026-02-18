<?php 

include 'koneksi.php';

if (isset($_POST['simpan'])) {
	
	$username = $_POST['username'];
	$password = $_POST['password'];
}

$save = "INSERT INTO tb_daftar SET username='$username', password='$password'";
$result = mysqli_query($koneksi, $save);

if ($result) {
	header("location: datauser.php");
}else{
	echo "gagal disimpan";
}

 ?>