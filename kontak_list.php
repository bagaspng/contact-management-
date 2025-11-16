<?php
require_once __DIR__ . '/kontak_store.php';

// Ambil flash message jika ada
$success = $_SESSION['flash_success'] ?? '';
$errors  = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_errors']);

$contacts = kontak_all();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Kontak</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="max-w-5xl mx-auto py-12 px-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h1 class="text-2xl font-semibold text-gray-800">Daftar Kontak</h1>
        <a href="kontak_form.php" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm">
          + Tambah Kontak
        </a>
      </div>

      <div class="p-6">
        <?php if ($success): ?>
          <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200 text-green-800">
            <?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200 text-red-800">
            <ul class="list-disc pl-5">
              <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (empty($contacts)): ?>
          <div class="text-center py-8 text-gray-600">
            <svg class="mx-auto mb-3 w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7a4 4 0 108 0 4 4 0 00-8 0zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
            </svg>
            <p class="text-lg">Belum ada data kontak.</p>
            <p class="mt-3"><a href="kontak_form.php" class="text-indigo-600 hover:underline">Tambah kontak baru</a></p>
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <?php foreach ($contacts as $i => $c): ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $i+1 ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800"><?= htmlspecialchars($c['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($c['phone']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($c['email']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= nl2br(htmlspecialchars($c['address'])) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                      <a href="kontak_form.php?id=<?= $i ?>" class="inline-block mr-2 px-3 py-1 rounded-md bg-yellow-100 text-yellow-800 hover:bg-yellow-200 text-sm">Edit</a>
                      <a href="kontak_delete.php?id=<?= $i ?>" class="inline-block px-3 py-1 rounded-md bg-red-100 text-red-800 hover:bg-red-200 text-sm" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>