# Tugas Mandiri 6
## Perancangan ERD E-Library Kampus

## 1. Deskripsi Sistem

Sistem E-Library Kampus merupakan sistem basis data relasional yang digunakan
untuk mengelola data mahasiswa, buku, penerbit, serta transaksi peminjaman
dan pengembalian buku.

---

## 2. Perancangan Entitas

Sistem E-Library Kampus memiliki empat entitas utama, yaitu Mahasiswa, Buku,
Penerbit, dan Transaksi Peminjaman.

### 2.1 Entitas Mahasiswa

Entitas Mahasiswa menyimpan data identitas mahasiswa yang menggunakan layanan
perpustakaan.

| Atribut | Keterangan | Key |
|---|---|---|
| nim | Nomor Induk Mahasiswa | PK |
| nama_mahasiswa | Nama lengkap mahasiswa | - |
| program_studi | Program studi mahasiswa | - |
| fakultas | Fakultas mahasiswa | - |
| email | Alamat email mahasiswa | - |
| no_telepon | Nomor telepon mahasiswa | - |

**Primary Key:** `nim`

---

### 2.2 Entitas Buku

Entitas Buku menyimpan informasi mengenai koleksi buku yang tersedia di
perpustakaan.

| Atribut | Keterangan | Key |
|---|---|---|
| id_buku | Identitas unik buku | PK |
| isbn | Nomor ISBN buku | - |
| judul_buku | Judul buku | - |
| tahun_terbit | Tahun terbit buku | - |
| kategori | Kategori buku | - |
| stok | Jumlah stok buku | - |
| id_penerbit | Identitas penerbit buku | FK |

**Primary Key:** `id_buku`

**Foreign Key:** `id_penerbit` → `Penerbit.id_penerbit`

---

### 2.3 Entitas Penerbit

Entitas Penerbit menyimpan informasi mengenai penerbit yang menerbitkan
buku-buku dalam sistem.

| Atribut | Keterangan | Key |
|---|---|---|
| id_penerbit | Identitas unik penerbit | PK |
| nama_penerbit | Nama penerbit | - |
| alamat | Alamat penerbit | - |
| email | Alamat email penerbit | - |
| no_telepon | Nomor telepon penerbit | - |

**Primary Key:** `id_penerbit`

---

### 2.4 Entitas Transaksi Peminjaman

Entitas Transaksi Peminjaman menyimpan riwayat peminjaman dan pengembalian
buku yang dilakukan oleh mahasiswa.

| Atribut | Keterangan | Key |
|---|---|---|
| id_peminjaman | Identitas unik transaksi peminjaman | PK |
| nim | Mahasiswa yang melakukan peminjaman | FK |
| id_buku | Buku yang dipinjam | FK |
| tanggal_peminjaman | Tanggal buku dipinjam | - |
| tanggal_jatuh_tempo | Batas waktu pengembalian buku | - |
| tanggal_pengembalian | Tanggal buku dikembalikan | - |
| status_peminjaman | Status transaksi peminjaman | - |

**Primary Key:** `id_peminjaman`

**Foreign Key:**
- `nim` → `Mahasiswa.nim`
- `id_buku` → `Buku.id_buku`

---

### 2.5 Hubungan Antar Entitas

Hubungan antar entitas dalam sistem adalah sebagai berikut:

1. Satu **Penerbit** dapat menerbitkan banyak **Buku**.
2. Satu **Buku** diterbitkan oleh satu **Penerbit**.
3. Satu **Mahasiswa** dapat melakukan banyak **Transaksi Peminjaman**.
4. Satu **Transaksi Peminjaman** dilakukan oleh satu **Mahasiswa**.
5. Satu **Buku** dapat muncul dalam banyak **Transaksi Peminjaman**.
6. Satu **Transaksi Peminjaman** hanya berkaitan dengan satu **Buku**.

Dengan demikian, kardinalitas hubungan antar entitas adalah:

- `Penerbit 1 : N Buku`
- `Mahasiswa 1 : N Transaksi Peminjaman`
- `Buku 1 : N Transaksi Peminjaman`
---

## 3. Normalisasi

### 3.1 Unnormalized Form (UNF)

### 3.2 First Normal Form (1NF)

### 3.3 Second Normal Form (2NF)

### 3.4 Third Normal Form (3NF)

---

## 4. Rancangan Tabel Akhir

---

## 5. Relasi Antar Entitas

```mermaid
erDiagram