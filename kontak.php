<?php
session_start();

// Inisialisasi array kontak di session
if (!isset($_SESSION['contacts'])) {
    $_SESSION['contacts'] = [];
}

$errors = [];
$success = '';
$editingIndex = null;

// Data default untuk form
$formData = [
    'name'    => '',
    'phone'   => '',
    'email'   => '',
    'address' => ''
];

// ---------------------------
// HAPUS KONTAK (action=delete)
// ---------------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if (isset($_SESSION['contacts'][$id])) {
        unset($_SESSION['contacts'][$id]);
        // rapikan index array
        $_SESSION['contacts'] = array_values($_SESSION['contacts']);
        $success = 'Kontak berhasil dihapus.';
    }
}

// ---------------------------
// PROSES FORM (Tambah / Edit)
// ---------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $mode    = $_POST['mode'] ?? 'create';
    $idx     = isset($_POST['index']) ? (int) $_POST['index'] : null;

    // Validasi sederhana
    if ($name === '') {
        $errors['name'] = 'Nama wajib diisi.';
    }

    if ($phone === '') {
        $errors['phone'] = 'No. HP wajib diisi.';
    } elseif (!preg_match('/^[0-9+ ]+$/', $phone)) {
        $errors['phone'] = 'No. HP hanya boleh angka, spasi, dan tanda +.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    // Jika tidak ada error, simpan ke session
    if (empty($errors)) {
        $data = [
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email,
            'address' => $address
        ];

        if ($mode === 'edit' && $idx !== null && isset($_SESSION['contacts'][$idx])) {
            $_SESSION['contacts'][$idx] = $data;
            $success = 'Kontak berhasil diperbarui.';
        } else {
            $_SESSION['contacts'][] = $data;
            $success = 'Kontak baru berhasil ditambahkan.';
        }

        // reset form setelah berhasil
        $formData = [
            'name'    => '',
            'phone'   => '',
            'email'   => '',
            'address' => ''
        ];
        $editingIndex = null;

        // (Opsional) Untuk menghindari resubmit saat refresh:
        // header('Location: kontak.php');
        // exit;
    } else {
        // jika ada error, isi ulang form dengan data yang tadi dikirim
        $formData = [
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email,
            'address' => $address
        ];
        if ($mode === 'edit') {
            $editingIndex = $idx;
        }
    }
}

// ---------------------------
// MODE EDIT (prefill form)
// ---------------------------
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    if (isset($_SESSION['contacts'][$id])) {
        $editingIndex = $id;
        $formData = $_SESSION['contacts'][$id];
    }
}

$isEditing = $editingIndex !== null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Manajemen Kontak</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        h1, h2 {
            margin-top: 0;
        }
        .alert {
            padding: 10px 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .alert.success {
            background: #e0f7e9;
            color: #256029;
            border: 1px solid #a8e6b0;
        }
        .alert.error {
            background: #fdecea;
            color: #b71c1c;
            border: 1px solid #f5c6cb;
        }
        form .form-group {
            margin-bottom: 10px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 4px;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 7px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 60px;
        }
        .error-text {
            color: #b71c1c;
            font-size: 0.85rem;
        }
        button {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background: #1d4ed8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            font-size: 0.95rem;
        }
        th {
            background: #f3f4f6;
        }
        .actions a {
            margin-right: 8px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .edit-link { color: #2563eb; }
        .delete-link { color: #b91c1c; }
        .session-info {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Sistem Manajemen Kontak Sederhana</h1>
    <p class="session-info">
        Data kontak disimpan di <strong>session</strong> PHP (tanpa database).  
        Total kontak saat ini: <strong><?= count($_SESSION['contacts']); ?></strong>
    </p>

    <?php if ($success): ?>
        <div class="alert success">
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $msg): ?>
                    <li><?= htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- FORM TAMBAH / EDIT KONTAK -->
    <h2><?= $isEditing ? 'Edit Kontak' : 'Tambah Kontak Baru'; ?></h2>
    <form method="post" action="">
        <input type="hidden" name="mode" value="<?= $isEditing ? 'edit' : 'create'; ?>">
        <?php if ($isEditing): ?>
            <input type="hidden" name="index" value="<?= (int)$editingIndex; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="name">Nama *</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($formData['name']); ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="error-text"><?= htmlspecialchars($errors['name']); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">No. HP *</label>
            <input type="text" id="phone" name="phone"
                   value="<?= htmlspecialchars($formData['phone']); ?>">
            <?php if (isset($errors['phone'])): ?>
                <div class="error-text"><?= htmlspecialchars($errors['phone']); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email (opsional)</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($formData['email']); ?>">
            <?php if (isset($errors['email'])): ?>
                <div class="error-text"><?= htmlspecialchars($errors['email']); ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="address">Alamat (opsional)</label>
            <textarea id="address" name="address"><?= htmlspecialchars($formData['address']); ?></textarea>
        </div>

        <button type="submit">
            <?= $isEditing ? 'Simpan Perubahan' : 'Tambah Kontak'; ?>
        </button>
        <?php if ($isEditing): ?>
            <a href="kontak.php" style="margin-left:10px; font-size:0.9rem;">Batal Edit</a>
        <?php endif; ?>
    </form>

    <!-- DAFTAR KONTAK -->
    <h2 style="margin-top:30px;">Daftar Kontak</h2>

    <?php if (empty($_SESSION['contacts'])): ?>
        <p>Belum ada data kontak.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>No. HP</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($_SESSION['contacts'] as $i => $c): ?>
                <tr>
                    <td><?= $i + 1; ?></td>
                    <td><?= htmlspecialchars($c['name']); ?></td>
                    <td><?= htmlspecialchars($c['phone']); ?></td>
                    <td><?= htmlspecialchars($c['email']); ?></td>
                    <td><?= nl2br(htmlspecialchars($c['address'])); ?></td>
                    <td class="actions">
                        <a class="edit-link" href="?action=edit&id=<?= $i; ?>">Edit</a>
                        <a class="delete-link"
                           href="?action=delete&id=<?= $i; ?>"
                           onclick="return confirm('Yakin ingin menghapus kontak ini?');">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
