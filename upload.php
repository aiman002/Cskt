<?php
session_start();

// Semak jika pengguna tidak sah
if (!isset($_SESSION['authenticated'])) {
    header('Location: index.php');
    exit();
}

$target_dir = "media/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Semak jika fail adalah gambar atau video
if (isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Fail bukan gambar atau video.";
        $uploadOk = 0;
    }
}

// Semak jika fail sudah ada
if (file_exists($target_file)) {
    echo "Maaf, fail sudah ada.";
    $uploadOk = 0;
}

// Semak saiz fail
if ($_FILES["fileToUpload"]["size"] > 5000000) { // 5MB limit
    echo "Maaf, fail anda terlalu besar.";
    $uploadOk = 0;
}

// Semak format fail
if ($fileType != "jpg" && $fileType != "jpeg" && $fileType != "png" && $fileType != "gif" && $fileType != "mp4" && $fileType != "avi" && $fileType != "mov") {
    echo "Maaf, hanya gambar dan video dengan format JPG, JPEG, PNG, GIF, MP4, AVI, MOV yang dibenarkan.";
    $uploadOk = 0;
}

// Semak jika $uploadOk disetkan kepada 0 oleh kesilapan
if ($uploadOk == 0) {
    echo "Maaf, fail anda tidak dimuat naik.";
// Jika semuanya ok, cuba memuat naik fail
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "Fail ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])). " telah dimuat naik.";
    } else {
        echo "Maaf, ada masalah dengan memuat naik fail anda.";
    }
}

header("Location: dashboard.php"); // Kembali ke dashboard
?>
