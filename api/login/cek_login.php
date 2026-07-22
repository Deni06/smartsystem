<?php
	
	session_start();
	
	include "../../config/koneksi.php";

	if (!isset($_POST['email']) || !isset($_POST['password'])) {
		echo json_encode([
			'status'  => 'error',
			'message' => 'Email dan password harus diisi.'
		]);
		exit;
	}
	
	$email = trim($_POST['email']);
	$password = $_POST['password'];
	
	$sql = "SELECT id_user, email, name, password, is_admin FROM user WHERE email = ? AND 
	status = 1 LIMIT 1";
	$stmt = mysqli_prepare($koneksi, $sql);

	if ($stmt) {		
		mysqli_stmt_bind_param($stmt, "s", $email);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_assoc($result);
				
		if ($row) {		
			if (password_verify($password, $row['password'])) {				
				$_SESSION['is_login'] = "Y";
				$_SESSION['email']    = $row['email'];
				$_SESSION['name']     = $row['name'];
				$_SESSION['user_id']  = $row['id_user'];
				$_SESSION['is_admin']  = $row['is_admin'];
				
				echo json_encode([
					'status'  => 'success',
					'message' => 'Login berhasil'			
				]);
			} else {						
				echo json_encode([
					'status'  => 'error',
					'message' => 'Email atau password salah atau akun tidak aktif'
				]);
			}
		} else {			
			echo json_encode([
				'status'  => 'error',
				'message' => 'Email atau password salah atau akun tidak aktif'
			]);
		}
		mysqli_stmt_close($stmt);
	} else {		
		echo json_encode([
			'status'  => 'error',
			'message' => 'Terjadi kesalahan pada sistem.'
		]);
	}	
?>