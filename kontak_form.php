<?php
require_once __DIR__ . '/kontak_store.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$contact = null;
$mode = 'create';
if ($id !== null) {
    $contact = kontak_get($id);
    if ($contact) $mode = 'edit';
}

// Jika ada flash old input/errors (dari proses), gunakan itu
$old = $_SESSION['old_input'] ?? null;
$errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['old_input'], $_SESSION['flash_errors']);
if ($old) {
    $contact = array_merge($contact ?? ['name'=>'','phone'=>'','email'=>'','address'=>''], $old);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= $mode === 'edit' ? 'Edit' : 'Tambah' ?> Kontak</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-2xl mx-auto py-12 px-4">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h1 class="text-xl font-semibold text-gray-800"><?= $mode === 'edit' ? 'Edit' : 'Tambah' ?> Kontak</h1>
        <p class="text-sm text-gray-500 mt-1"><?= $mode === 'edit' ? 'Perbarui data kontak di sini.' : 'Isi form untuk menambahkan kontak baru.' ?></p>
      </div>

      <div class="p-6">
        <?php if (!empty($errors)): ?>
          <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-800 p-4">
            <ul class="list-disc pl-5">
              <?php foreach ($errors as $e) : ?>
                <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="kontak_process.php" method="post" novalidate class="space-y-4">
          <input type="hidden" name="mode" value="<?= $mode ?>">
          <?php if ($mode === 'edit'): ?><input type="hidden" name="index" value="<?= $id ?>"><?php endif; ?>

          <div>
            <label class="block text-sm font-medium text-gray-700">Nama*</label>
            <input name="name" value="<?= htmlspecialchars($contact['name'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none" required>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">No. HP*</label>
            <input name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none" required>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input name="email" type="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Alamat</label>
            <textarea name="address" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none"><?= htmlspecialchars($contact['address'] ?? '') ?></textarea>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
              <?= $mode === 'edit' ? 'Simpan' : 'Tambah' ?>
            </button>
            <a href="kontak_list.php" class="text-sm text-gray-600 hover:underline">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>