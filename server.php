<?php
session_start();

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'perpustakaan';
$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fungsi untuk mendapatkan riwayat transaksi
function getTransactions() {
    global $conn;
    $sql = "SELECT * FROM transaksi";
    $result = $conn->query($sql);
    $transactions = [];

    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    return $transactions;
}

// Cek apakah form dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Jika ada data untuk login
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Proses login sederhana
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['username'] = $username;
            header("Location: dashboard.html");
            exit();
        } else {
            echo "Username atau password salah.";
            exit();
        }
    }

    // Jika ada data untuk transaksi (peminjaman atau pengembalian)
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $member_id = $_POST['member_id'];
        $book_id = $_POST['book_id'];

        if ($action === 'borrow') {
            $borrow_date = $_POST['borrow_date'];
            
            $sql = "INSERT INTO transaksi (member_id, book_id, borrow_date, status) 
                    VALUES ('$member_id', '$book_id', '$borrow_date', 'Dipinjam')";

            if ($conn->query($sql) === TRUE) {
                echo "Peminjaman berhasil diproses.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }

        } elseif ($action === 'return') {
            $return_date = $_POST['return_date'];

            $sql = "UPDATE transaksi 
                    SET return_date='$return_date', status='Dikembalikan' 
                    WHERE member_id='$member_id' AND book_id='$book_id' AND status='Dipinjam'";

            if ($conn->query($sql) === TRUE) {
                echo "Pengembalian berhasil diproses.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

$conn->close();
?>
