<?php
session_start();

if (!isset($_SESSION['contacts'])) {
    $_SESSION['contacts'] = [];
}

// CRUD sederhana untuk kontak
function kontak_all() {
    return $_SESSION['contacts'];
}
function kontak_get(int $id) {
    return $_SESSION['contacts'][$id] ?? null;
}
function kontak_add(array $data) {
    $_SESSION['contacts'][] = $data;
    return count($_SESSION['contacts']) - 1;
}
function kontak_update(int $id, array $data) {
    if (!isset($_SESSION['contacts'][$id])) return false;
    $_SESSION['contacts'][$id] = $data;
    return true;
}
function kontak_delete(int $id) {
    if (!isset($_SESSION['contacts'][$id])) return false;
    unset($_SESSION['contacts'][$id]);
    $_SESSION['contacts'] = array_values($_SESSION['contacts']);
    return true;
}