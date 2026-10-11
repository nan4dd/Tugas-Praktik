<?php

declare(strict_types=1);

class GuestBook
{
    private PDO $pdo;

    public function __construct()
    {
        $host = '127.0.0.1';
        $dbname = 'perpustakaan_db';
        $username = 'root';
        $password = '';

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new PDO(
            $dsn,
            $username,
            $password,
            $options
        );
    }

    /**
     * Menyimpan pesan buku tamu.
     */
    public function tambahPesan(
        string $nama,
        string $email,
        string $pesan
    ): bool {
        $sql = "
            INSERT INTO buku_tamu (nama, email, pesan)
            VALUES (:nama, :email, :pesan)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':pesan' => $pesan,
        ]);
    }

    /**
     * Mengambil seluruh pesan buku tamu.
     */
    public function ambilSemuaPesan(): array
    {
        $sql = "
            SELECT id, nama, email, pesan, tanggal_kirim
            FROM buku_tamu
            ORDER BY tanggal_kirim DESC, id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}