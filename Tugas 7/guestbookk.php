<?php

declare(strict_types=1);

// Menampilkan kesalahan selama pengembangan.
// Jangan aktifkan display_errors di lingkungan produksi.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/GuestBook.php';

/**
 * Mengamankan keluaran HTML untuk mencegah XSS.
 */
function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Membuat token CSRF untuk sesi saat ini.
if (
    !isset($_SESSION['csrf_token'])
    || !is_string($_SESSION['csrf_token'])
) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$nama = '';
$email = '';
$pesan = '';

$errors = [];
$success = '';
$messages = [];

try {
    $guestBook = new GuestBook();

    // Memproses formulir ketika dikirim.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pesan = trim($_POST['pesan'] ?? '');
        $csrfToken = $_POST['csrf_token'] ?? '';

        // Validasi token CSRF.
        if (
            !is_string($csrfToken)
            || !hash_equals(
                $_SESSION['csrf_token'],
                $csrfToken
            )
        ) {
            $errors[] = 'Token keamanan tidak valid. Muat ulang halaman dan coba kembali.';
        }

        // Validasi nama.
        if ($nama === '') {
            $errors[] = 'Nama tidak boleh kosong.';
        } elseif (mb_strlen($nama) > 100) {
            $errors[] = 'Nama maksimal 100 karakter.';
        }

        // Validasi email.
        if (
            $email === ''
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            $errors[] = 'Masukkan alamat email yang valid.';
        } elseif (strlen($email) > 150) {
            $errors[] = 'Email maksimal 150 karakter.';
        }

        // Validasi pesan.
        if (mb_strlen($pesan) < 5) {
            $errors[] = 'Pesan harus terdiri dari minimal 5 karakter.';
        }

        // Simpan hanya jika semua validasi berhasil.
        if (empty($errors)) {
            $guestBook->tambahPesan(
                $nama,
                $email,
                $pesan
            );

            // Buat token baru setelah pengiriman berhasil.
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            // Redirect ke halaman yang benar.
            header('Location: guestbookk.php?status=success');
            exit;
        }
    }

    // Mengambil daftar pesan.
    $messages = $guestBook->ambilSemuaPesan();

    // Pesan keberhasilan setelah redirect.
    if (
        $_SERVER['REQUEST_METHOD'] === 'GET'
        && ($_GET['status'] ?? '') === 'success'
    ) {
        $success = 'Pesan berhasil dikirim.';
    }
} catch (Throwable $exception) {
    // Catat detail kesalahan di log server.
    error_log(
        'GuestBook error: ' . $exception->getMessage()
    );

    // Tampilkan pesan umum kepada pengguna.
    http_response_code(500);

    $errors[] = 'Terjadi kesalahan saat memproses buku tamu. Silakan periksa log server.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buku Tamu Digital Perpustakaan</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 15px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6fb;
            color: #263238;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            margin-bottom: 5px;
            color: #24516f;
        }

        .subtitle {
            margin-top: 0;
            margin-bottom: 25px;
            color: #555;
        }

        .card {
            margin-bottom: 24px;
            padding: 24px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        }

        h2 {
            margin-top: 0;
            color: #24516f;
            font-size: 21px;
        }

        label {
            display: block;
            margin-top: 12px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        textarea {
            display: block;
            width: 100%;
            padding: 11px;
            border: 1px solid #d5dce3;
            border-radius: 5px;
            font: inherit;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
            outline: 2px solid #b7d9e5;
            border-color: #24758a;
        }

        button {
            margin-top: 16px;
            padding: 11px 18px;
            border: none;
            border-radius: 5px;
            background: #24758a;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #19586a;
        }

        .alert {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 6px;
        }

        .success {
            background: #e5f6eb;
            color: #176534;
        }

        .error {
            background: #fdeaea;
            color: #922525;
        }

        .error ul {
            margin-bottom: 0;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e9ed;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        th {
            background: #edf3f7;
            color: #24516f;
        }

        .empty {
            padding: 20px;
            text-align: center;
            color: #66727d;
        }

        .date {
            white-space: nowrap;
        }

        @media (max-width: 600px) {
            body {
                padding: 18px 10px;
            }

            .card {
                padding: 16px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <header>
        <h1>Buku Tamu Digital Perpustakaan</h1>
        <p class="subtitle">
            Silakan tinggalkan pesan, saran, atau masukan untuk perpustakaan.
        </p>
    </header>

    <?php if ($success !== ''): ?>
        <div class="alert success" role="status">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert error" role="alert">
            <strong>Periksa kembali isian kamu:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>Tulis Pesan</h2>

        <form method="POST" action="guestbookk.php">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token']) ?>"
            >

            <label for="nama">Nama</label>
            <input
                type="text"
                id="nama"
                name="nama"
                maxlength="100"
                value="<?= e($nama) ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                value="<?= e($email) ?>"
                required
            >

            <label for="pesan">Pesan</label>
            <textarea
                id="pesan"
                name="pesan"
                minlength="5"
                required
            ><?= e($pesan) ?></textarea>

            <button type="submit">Kirim Pesan</button>
        </form>
    </section>

    <section class="card">
        <h2>Daftar Pesan Pengunjung</h2>

        <?php if (!empty($messages)): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Pesan</th>
                            <th>Tanggal Kirim</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($messages as $index => $message): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= e($message['nama']) ?></td>
                                <td><?= e($message['email']) ?></td>
                                <td><?= nl2br(e($message['pesan'])) ?></td>
                                <td class="date">
                                    <?= e($message['tanggal_kirim']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty">Belum ada pesan yang dikirim.</p>
        <?php endif; ?>
    </section>

</div>
</body>
</html>