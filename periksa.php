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
    
    <title>Periksa</title>
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
        <h3>Periksa</h3>
        <hr>

        <?php
        // Proses Tambah/Update Data Periksa
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_pasien = htmlspecialchars($_POST['id_pasien']);
            $id_dokter = htmlspecialchars($_POST['id_dokter']);
            $tgl_periksa = htmlspecialchars($_POST['tgl_periksa']);
            $catatan = htmlspecialchars($_POST['catatan']);
            $obat = htmlspecialchars($_POST['obat']);

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Update data periksa jika ID ada
                $id = (int)$_POST['id'];
                $sql = "UPDATE periksa SET id_pasien='$id_pasien', id_dokter='$id_dokter', tgl_periksa='$tgl_periksa', catatan='$catatan', obat='$obat' WHERE id=$id";
                if (mysqli_query($mysqli, $sql)) {
                    echo "<div class='alert alert-success'>Data periksa berhasil diperbarui!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . mysqli_error($mysqli) . "</div>";
                }
            } else {
                // Tambah data periksa baru jika ID tidak ada
                $sql = "INSERT INTO periksa (id_pasien, id_dokter, tgl_periksa, catatan, obat) VALUES ('$id_pasien', '$id_dokter', '$tgl_periksa', '$catatan', '$obat')";
                if (mysqli_query($mysqli, $sql)) {
                    echo "<div class='alert alert-success'>Data periksa berhasil ditambahkan!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . mysqli_error($mysqli) . "</div>";
                }
            }
        }

        // Proses Hapus Data Periksa
        if (isset($_GET['hapus'])) {
            $id = (int)$_GET['hapus'];
            $sql = "DELETE FROM periksa WHERE id=$id";
            if (mysqli_query($mysqli, $sql)) {
                echo "<div class='alert alert-success'>Data periksa berhasil dihapus!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . mysqli_error($mysqli) . "</div>";
            }
        }

        // Ambil data pasien dan dokter untuk dropdown
        $pasienResult = mysqli_query($mysqli, "SELECT id, nama FROM pasien");
        $dokterResult = mysqli_query($mysqli, "SELECT id, nama FROM dokter");

        // Ambil data periksa untuk edit jika diperlukan
        $editData = null;
        if (isset($_GET['edit'])) {
            $id = (int)$_GET['edit'];
            $result = mysqli_query($mysqli, "SELECT * FROM periksa WHERE id=$id");
            $editData = mysqli_fetch_assoc($result);
        }
        ?>

        <!-- Form Tambah/Update Periksa -->
        <form method="POST" action="periksa.php">
            <input type="hidden" name="id" value="<?= isset($editData['id']) ? htmlspecialchars($editData['id']) : '' ?>">
            
            <div class="mb-3">
                <label for="id_pasien" class="form-label">Pasien</label>
                <select class="form-select" id="id_pasien" name="id_pasien" required>
                    <option value="">Pilih Pasien</option>
                    <?php while ($pasien = mysqli_fetch_assoc($pasienResult)) { ?>
                        <option value="<?= $pasien['id'] ?>" <?= isset($editData['id_pasien']) && $editData['id_pasien'] == $pasien['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pasien['nama']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_dokter" class="form-label">Dokter</label>
                <select class="form-select" id="id_dokter" name="id_dokter" required>
                    <option value="">Pilih Dokter</option>
                    <?php while ($dokter = mysqli_fetch_assoc($dokterResult)) { ?>
                        <option value="<?= $dokter['id'] ?>" <?= isset($editData['id_dokter']) && $editData['id_dokter'] == $dokter['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dokter['nama']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="tgl_periksa" class="form-label">Tanggal Periksa</label>
                <input type="datetime-local" class="form-control" id="tgl_periksa" name="tgl_periksa" required value="<?= isset($editData['tgl_periksa']) ? date('Y-m-d\TH:i', strtotime($editData['tgl_periksa'])) : '' ?>">
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <input type="text" class="form-control" id="catatan" name="catatan" placeholder="Catatan" value="<?= isset($editData['catatan']) ? htmlspecialchars($editData['catatan']) : '' ?>">
            </div>

            <div class="mb-3">
                <label for="obat" class="form-label">Obat</label>
                <input type="text" class="form-control" id="obat" name="obat" placeholder="Obat" value="<?= isset($editData['obat']) ? htmlspecialchars($editData['obat']) : '' ?>">
            </div>

            <button type="submit" class="btn btn-primary"><?= isset($editData) ? 'Update' : 'Simpan' ?></button>
        </form>

        <!-- Tabel Data Periksa -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pasien</th>
                    <th>Nama Dokter</th>
                    <th>Tanggal Periksa</th>
                    <th>Catatan</th>
                    <th>Obat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($mysqli, "SELECT periksa.*, pasien.nama AS nama_pasien, dokter.nama AS nama_dokter FROM periksa JOIN pasien ON periksa.id_pasien = pasien.id JOIN dokter ON periksa.id_dokter = dokter.id ORDER BY periksa.id ASC");
                $no = 1;
                while ($data = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>".$no++."</td>";
                    echo "<td>".htmlspecialchars($data['nama_pasien'])."</td>";
                    echo "<td>".htmlspecialchars($data['nama_dokter'])."</td>";
                    echo "<td>".date('d-m-Y H:i', strtotime($data['tgl_periksa']))."</td>";
                    echo "<td>".htmlspecialchars($data['catatan'])."</td>";
                    echo "<td>".htmlspecialchars($data['obat'])."</td>";
                    echo "<td>";
                    echo "<a href='periksa.php?edit=".$data['id']."' class='btn btn-success btn-sm'>Ubah</a> ";
                    echo "<a href='periksa.php?hapus=".$data['id']."' class='btn btn-danger btn-sm'>Hapus</a>";
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
