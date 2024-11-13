<?php
session_start();
include_once("koneksi.php");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: loginUser.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap Online -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    
    <title>Pasien</title>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Sistem Informasi Poliklinik</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Data Master
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="dokter.php">Dokter</a></li>
                            <li><a class="dropdown-item" href="pasien.php">Pasien</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="periksa.php">Periksa</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <span class="navbar-text text-light me-3">
                                Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-outline-light me-2" href="loginUser.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="registrasiUser.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3>Data Pasien</h3>
        <hr>

        <?php
        // Proses Tambah/Update Data Pasien
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = htmlspecialchars($_POST['nama']);
            $alamat = htmlspecialchars($_POST['alamat']);
            $no_hp = htmlspecialchars($_POST['no_hp']);
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update data pasien jika ID ada
                $id = (int)$_POST['id'];
                $sql = "UPDATE pasien SET nama='$nama', alamat='$alamat', no_hp='$no_hp' WHERE id=$id";
                if (mysqli_query($mysqli, $sql)) {
                    echo "<div class='alert alert-success'>Data pasien berhasil diperbarui!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . mysqli_error($mysqli) . "</div>";
                }
            } else {
                // Tambah data pasien baru jika ID tidak ada
                $sql = "INSERT INTO pasien (nama, alamat, no_hp) VALUES ('$nama', '$alamat', '$no_hp')";
                if (mysqli_query($mysqli, $sql)) {
                    echo "<div class='alert alert-success'>Data pasien berhasil ditambahkan!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . mysqli_error($mysqli) . "</div>";
                }
            }
        }

        // Proses Hapus Data Pasien
        if (isset($_GET['hapus'])) {
            $id = (int)$_GET['hapus'];
            $sql = "DELETE FROM pasien WHERE id=$id";
            if (mysqli_query($mysqli, $sql)) {
                echo "<div class='alert alert-success'>Data pasien berhasil dihapus!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . mysqli_error($mysqli) . "</div>";
            }
        }

        // Ambil data pasien untuk diubah
        $editData = null;
        if (isset($_GET['edit'])) {
            $id = (int)$_GET['edit'];
            $result = mysqli_query($mysqli, "SELECT * FROM pasien WHERE id=$id");
            $editData = mysqli_fetch_assoc($result);
        }
        ?>

        <!-- Form Tambah/Update Pasien -->
        <form method="POST" action="pasien.php">
            <input type="hidden" name="id" value="<?= isset($editData['id']) ? htmlspecialchars($editData['id']) : '' ?>">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" required value="<?= isset($editData['nama']) ? htmlspecialchars($editData['nama']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" required value="<?= isset($editData['alamat']) ? htmlspecialchars($editData['alamat']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="no_hp" class="form-label">No HP</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="No HP" required value="<?= isset($editData['no_hp']) ? htmlspecialchars($editData['no_hp']) : '' ?>">
            </div>
            <button type="submit" class="btn btn-primary"><?= isset($editData) ? 'Update' : 'Simpan' ?></button>
        </form>

        <!-- Tabel Data Pasien -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil data pasien dari database
                $result = mysqli_query($mysqli, "SELECT * FROM pasien ORDER BY id ASC");
                $no = 1;
                while ($data = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>".$no++."</td>";
                    echo "<td>".htmlspecialchars($data['nama'])."</td>";
                    echo "<td>".htmlspecialchars($data['alamat'])."</td>";
                    echo "<td>".htmlspecialchars($data['no_hp'])."</td>";
                    echo "<td>";
                    echo "<a href='pasien.php?edit=".$data['id']."' class='btn btn-success btn-sm'>Ubah</a> ";
                    echo "<a href='pasien.php?hapus=".$data['id']."' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS Online -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
