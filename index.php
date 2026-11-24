<?php
session_start();

// Class induk
class Pemeriksaan {
    protected $tanggal, $tempat, $catatan;

    public function __construct($tanggal, $tempat, $catatan) {
        $this->tanggal = $tanggal;
        $this->tempat = $tempat;
        $this->catatan = $catatan;
    }

    public function getTanggal() { return $this->tanggal; }
    public function getTempat() { return $this->tempat; }
    public function getCatatan() { return $this->catatan; }
}

// Class turunan
class PemeriksaanMata extends Pemeriksaan {
    private $minusKanan, $minusKiri, $silinderKanan, $silinderKiri;

    public function __construct($tanggal, $tempat, $catatan, $minusKanan, $minusKiri, $silinderKanan, $silinderKiri) {
        parent::__construct($tanggal, $tempat, $catatan);
        $this->minusKanan = $minusKanan;
        $this->minusKiri = $minusKiri;
        $this->silinderKanan = $silinderKanan;
        $this->silinderKiri = $silinderKiri;
    }

    public function getMinusKanan() { return $this->minusKanan; }
    public function getMinusKiri() { return $this->minusKiri; }
    public function getSilinderKanan() { return $this->silinderKanan; }
    public function getSilinderKiri() { return $this->silinderKiri; }
}

// Class pengelola riwayat
class RiwayatMata {
    private $data = [];

    public function tambah($item) { $this->data[] = $item; }
    public function semuaData() { return $this->data; }
    public function jumlah() { return count($this->data); }
    public function update($id, $item) { $this->data[$id] = $item; }

    public function hapus($id) {
        unset($this->data[$id]);
        $this->data = array_values($this->data);
    }

    public static function formatMinus($nilai) {
        return number_format((float)$nilai, 2);
    }
}

// Menyiapkan data session
if (!isset($_SESSION['riwayat_mata'])) {
    $_SESSION['riwayat_mata'] = new RiwayatMata();
}
$riwayat = $_SESSION['riwayat_mata'];

// Tambah atau edit data
if (isset($_POST['simpan']) || isset($_POST['update'])) {
    $item = new PemeriksaanMata(
        $_POST['tanggal'], $_POST['tempat'], $_POST['catatan'] ?? '',
        $_POST['minus_kanan'], $_POST['minus_kiri'],
        $_POST['silinder_kanan'] ?? 0, $_POST['silinder_kiri'] ?? 0
    );

    if (isset($_POST['update'])) {
        $riwayat->update((int)$_POST['id'], $item);
    } else {
        $riwayat->tambah($item);
    }

    header('Location: index.php');
    exit;
}

// Hapus data
if (isset($_POST['hapus'])) {
    $riwayat->hapus((int)$_POST['hapus_id']);
    header('Location: index.php');
    exit;
}

$data = $riwayat->semuaData();
$idEdit = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$itemEdit = ($idEdit !== null && isset($data[$idEdit])) ? $data[$idEdit] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MyEye - Pemeriksaan Mata</title>
<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f8f7fc;
    color: #49445e;
}

header {
    background: linear-gradient(135deg, #d9e5ff, #eadcff);
    color: #51466d;
    padding: 32px 20px;
    text-align: center;
}

header h1 {
    margin: 0 0 8px;
    font-size: 32px;
}

header p {
    margin: 0;
    color: #6b6085;
}

.container {
    max-width: 1120px;
    margin: 32px auto;
    padding: 20px;
}

.stat {
    background: #ebe6ff;
    padding: 22px 25px;
    border-radius: 18px;
    margin-bottom: 24px;
    border: 1px solid #ddd4ff;
}

.stat p {
    margin: 0 0 8px;
}

.stat h2 {
    margin: 0;
    color: #7965c9;
}

.card {
    background: white;
    padding: 26px;
    border-radius: 20px;
    margin-bottom: 24px;
    box-shadow: 0 6px 24px rgba(105, 89, 145, .06);
    border: 1px solid #eeeaf6;
}

.card h2 {
    color: #655783;
    margin: 0 0 22px;
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #625879;
}

input, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ded8ef;
    border-radius: 11px;
    margin-bottom: 16px;
    font-family: inherit;
    color: #51466d;
    background: #fdfcff;
}

input:focus, textarea:focus {
    border-color: #b4a4e9;
    box-shadow: 0 0 0 3px #f0ebff;
}

button {
    background: #a99ae5;
    color: white;
    padding: 10px 16px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-family: inherit;
    font-weight: 600;
}

button:hover {
    background: #8e7bd1;
}

.hapus {
    background: #eea5b8;
}

.hapus:hover {
    background: #e38ba4;
}

.kembali {
    background: #c9c3da;
    text-decoration: none;
    color: #51466d;
    padding: 10px 16px;
    border-radius: 10px;
    display: inline-block;
    margin-left: 5px;
    font-weight: 600;
}

table {
    width: 100%;
    min-width: 950px;
    border-collapse: collapse;
}

th, td {
    border-bottom: 1px solid #eeeaf5;
    padding: 14px 12px;
    text-align: left;
    vertical-align: middle;
}

th {
    background: #f1edff;
    color: #655783;
    white-space: nowrap;
}

td:not(:nth-child(7)):not(:nth-child(8)) {
    white-space: nowrap;
}

td:nth-child(7) {
    min-width: 150px;
    max-width: 260px;
    overflow-wrap: anywhere;
}

td:last-child {
    min-width: 145px;
}

.table-wrap {
    overflow-x: auto;
    border-radius: 12px;
}

.aksi {
    display: flex;
    align-items: center;
    gap: 8px;
}

.aksi form {
    margin: 0;
}

.aksi a {
    text-decoration: none;
}

.aksi button {
    white-space: nowrap;
}

.catatan-bawah {
    color: #9791aa;
    font-size: 13px;
    text-align: center;
    padding-bottom: 10px;
}

@media (max-width: 650px) {
    .grid {
        grid-template-columns: 1fr;
    }

    .container {
        padding: 12px;
    }

    .card {
        padding: 20px;
    }
}
</style>
</head>
<body>
<header>
    <h1>♡ MyEye ♡</h1>
    <p>Little diary of my eye health ✿</p>
</header>

<div class="container">
    <div class="stat">
        <p>Total pemeriksaan tercatat</p>
        <h2><?= $riwayat->jumlah() ?> Catatan</h2>
    </div>

    <div class="card">
        <h2><?= $itemEdit ? 'Edit Pemeriksaan' : 'Tambah Pemeriksaan' ?></h2>
        <form method="POST">
            <?php if ($itemEdit): ?>
                <input type="hidden" name="id" value="<?= $idEdit ?>">
            <?php endif; ?>

            <div class="grid">
                <div>
                    <label>Tanggal pemeriksaan</label>
                    <input type="date" name="tanggal" value="<?= $itemEdit ? htmlspecialchars($itemEdit->getTanggal()) : '' ?>" required>
                </div>
                <div>
                    <label>Tempat pemeriksaan</label>
                    <input type="text" name="tempat" value="<?= $itemEdit ? htmlspecialchars($itemEdit->getTempat()) : '' ?>" placeholder="Optik / Klinik" required>
                </div>
            </div>

            <div class="grid">
                <div>
                    <label>Minus mata kanan (OD)</label>
                    <input type="number" step="0.25" min="0" name="minus_kanan" value="<?= $itemEdit ? htmlspecialchars((string)$itemEdit->getMinusKanan()) : '' ?>" placeholder="1.00" required>
                </div>
                <div>
                    <label>Minus mata kiri (OS)</label>
                    <input type="number" step="0.25" min="0" name="minus_kiri" value="<?= $itemEdit ? htmlspecialchars((string)$itemEdit->getMinusKiri()) : '' ?>" placeholder="1.50" required>
                </div>
            </div>

            <div class="grid">
                <div>
                    <label>Silinder kanan (opsional)</label>
                    <input type="number" step="0.25" name="silinder_kanan" value="<?= $itemEdit ? htmlspecialchars((string)$itemEdit->getSilinderKanan()) : '' ?>" placeholder="0">
                </div>
                <div>
                    <label>Silinder kiri (opsional)</label>
                    <input type="number" step="0.25" name="silinder_kiri" value="<?= $itemEdit ? htmlspecialchars((string)$itemEdit->getSilinderKiri()) : '' ?>" placeholder="0">
                </div>
            </div>

            <label>Catatan</label>
            <textarea name="catatan" rows="3" placeholder="Catatan pemeriksaan"><?= $itemEdit ? htmlspecialchars($itemEdit->getCatatan()) : '' ?></textarea>

            <button name="<?= $itemEdit ? 'update' : 'simpan' ?>"><?= $itemEdit ? 'Simpan Perubahan' : 'Simpan Pemeriksaan' ?></button>
            <?php if ($itemEdit): ?>
                <a class="kembali" href="index.php">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2>Riwayat Pemeriksaan</h2>
        <?php if (!$data): ?>
            <p>Belum ada data pemeriksaan.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Tempat</th><th>OD</th><th>OS</th>
                            <th>Silinder OD</th><th>Silinder OS</th><th>Catatan</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $i => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item->getTanggal()) ?></td>
                            <td><?= htmlspecialchars($item->getTempat()) ?></td>
                            <td>-<?= RiwayatMata::formatMinus($item->getMinusKanan()) ?></td>
                            <td>-<?= RiwayatMata::formatMinus($item->getMinusKiri()) ?></td>
                            <td><?= RiwayatMata::formatMinus($item->getSilinderKanan()) ?></td>
                            <td><?= RiwayatMata::formatMinus($item->getSilinderKiri()) ?></td>
                            <td><?= htmlspecialchars($item->getCatatan()) ?></td>
                            <td>
                                <div class="aksi">
                                    <a href="index.php?edit=<?= $i ?>"><button type="button">Edit</button></a>
                                    <form method="POST" onsubmit="return confirm('Hapus catatan ini?')">
                                        <input type="hidden" name="hapus_id" value="<?= $i ?>">
                                        <button class="hapus" name="hapus">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <p class="catatan-bawah">Catatan pribadi pemeriksaan mata, bukan diagnosis medis.</p>
</div>
</body>
</html>
