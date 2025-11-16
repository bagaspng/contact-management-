<?php
require_once __DIR__ . '/kontak_store.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kontak_list.php'); exit;
}

$mode = $_POST['mode'] ?? 'create';
$idx  = isset($_POST['index']) ? (int)$_POST['index'] : null;

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'Nama wajib diisi.';
if ($phone === '') $errors[] = 'No. HP wajib diisi.';
elseif (!preg_match('/^[0-9+ ]+$/', $phone)) $errors[] = 'No. HP hanya boleh angka, spasi, dan +.';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';

if (!empty($errors)) {
    // simpan error dan old input ke session lalu redirect kembali ke form
    $_SESSION['flash_errors'] = $errors;
    $_SESSION['old_input'] = ['name'=>$name,'phone'=>$phone,'email'=>$email,'address'=>$address];
    $redirect = $mode === 'edit' ? "kontak_form.php?id={$idx}" : "kontak_form.php";
    header("Location: {$redirect}");
    exit;
}

$data = ['name'=>$name,'phone'=>$phone,'email'=>$email,'address'=>$address];

if ($mode === 'edit' && $idx !== null) {
    if (kontak_update($idx, $data)) {
        $_SESSION['flash_success'] = 'Kontak berhasil diperbarui.';
    } else {
        $_SESSION['flash_errors'] = ['Kontak tidak ditemukan.'];
    }
} else {
    kontak_add($data);
    $_SESSION['flash_success'] = 'Kontak baru berhasil ditambahkan.';
}

header('Location: kontak_list.php');
exit;