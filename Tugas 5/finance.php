<?php

declare(strict_types=1);

session_start();

require_once 'Transaction.php';

if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 100000.0;
}

if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $amount = (float) ($_POST['amount'] ?? 0);

    $transaction = new Transaction(
        count($_SESSION['transactions']) + 1,
        $type,
        $amount
    );

    if ($transaction->process($_SESSION['balance'])) {
        $_SESSION['transactions'][] = [
            'id' => $transaction->getId(),
            'type' => $transaction->getType(),
            'amount' => $transaction->getAmount(),
        ];

        $message = 'Transaksi berhasil diproses.';
    } else {
        $message = 'Transaksi gagal diproses.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Keuangan Sederhana</title>
</head>
<body>

    <h1>Sistem Manajemen Keuangan Sederhana</h1>

    <p>
        Saldo saat ini:
        Rp <?= number_format($_SESSION['balance'], 2, ',', '.') ?>
    </p>

    <?php if ($message !== ''): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="type">Jenis Transaksi:</label>

        <select name="type" id="type" required>
            <option value="deposit">Deposit</option>
            <option value="withdraw">Penarikan</option>
        </select>

        <br><br>

        <label for="amount">Jumlah:</label>

        <input
            type="number"
            name="amount"
            id="amount"
            min="0.01"
            step="0.01"
            required
        >

        <br><br>

        <button type="submit">Proses Transaksi</button>
    </form>

    <h2>Riwayat Transaksi</h2>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Jenis</th>
                <th>Jumlah</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($_SESSION['transactions'] as $transaction): ?>
                <tr>
                    <td><?= $transaction['id'] ?></td>
                    <td><?= $transaction['type'] ?></td>
                    <td>
                        Rp <?= number_format(
                            $transaction['amount'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>