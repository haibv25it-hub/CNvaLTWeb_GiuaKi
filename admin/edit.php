
<?php
include "../connect.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM Users WHERE admin_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Không tìm thấy người dùng!");
}


if (isset($_POST['update'])) {
    $email = $_POST['admin_email'];
    $images = $user['images'];
    $password = $_POST['admin_password'];

    // Nếu chọn ảnh mới
    if (isset($_FILES['images']) && $_FILES['images']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['images']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed) || !getimagesize($_FILES['images']['tmp_name'])) {
            die("File ảnh không hợp lệ!");
        }

        $fileName = uniqid() . "." . $ext;

        if (!move_uploaded_file(
            $_FILES['images']['tmp_name'],
            "../uploads/" . $fileName
        )) {
            die("Upload ảnh thất bại!");
        }

        $images = "uploads/" . $fileName;
    }

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "UPDATE Users SET admin_email=?, admin_password=?, images=?
             WHERE admin_id=?"
        );

        $stmt->bind_param("sssi", $email, $password, $images, $id);
    } else {
        $stmt = $conn->prepare(
            "UPDATE Users SET admin_email=?, images=?
             WHERE admin_id=?"
        );

        $stmt->bind_param("ssi", $email, $images, $id);
    }

    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Users</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">

    <h2>CẬP NHẬT NGƯỜI DÙNG</h2>

<form method="POST" enctype="multipart/form-data">

    <label>Email:</label>
    <input type="email" name="admin_email"
           value="<?= htmlspecialchars($user['admin_email']) ?>"
           required>

    <label>Mật khẩu mới:</label>
    <input type="password" name="admin_password">

    <label>Ảnh hiện tại:</label><br>

    <?php if (!empty($user['images'])) { ?>
        <img src="../<?= htmlspecialchars($user['images']) ?>"
             width="100" alt="Ảnh hiện tại">
    <?php } ?>

    <br>

    <label>Chọn ảnh mới:</label>
    <input type="file" name="images" accept="image/*">

    <button type="submit" name="update">Cập nhật</button>

    <a href="index.php" class="btn">Quay lại</a>

</form>

</div>

</body>
</html>
