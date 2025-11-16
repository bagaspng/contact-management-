<?php
require_once __DIR__ . '/kontak_store.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($id === null) {
    $_SESSION['flash_errors'] = ['ID tidak valid.'];
    header('Location: kontak_list.php'); exit;
}

if (kontak_delete($id)) {
    $_SESSION['flash_success'] = 'Kontak berhasil dihapus.';
} else {
    $_SESSION['flash_errors'] = ['Kontak tidak ditemukan.'];
}
header('Location: kontak_list.php');
exit;