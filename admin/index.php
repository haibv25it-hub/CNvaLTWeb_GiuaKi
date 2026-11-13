
<?php
include "../connect.php";

if (isset($_POST['add'])) {
    $email = $_POST['admin_email'];
    $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    $images = "";

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

    $stmt = $conn->prepare(
        "INSERT INTO Users (admin_email, admin_password, images)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $email, $password, $images);
    $stmt->execute();

    header("Location: index.php");
    exit;
}


$result = $conn->query("SELECT * FROM Users");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Users</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">

    <h2>QUẢN LÝ NGƯỜI DÙNG</h2>

    <h3>Thêm người dùng</h3>

    <form method="POST" enctype="multipart/form-data">
        <input type="email" name="admin_email"
               placeholder="Nhập email" required>

        <input type="password" name="admin_password"
               placeholder="Nhập mật khẩu" required>

        <label>Chọn hình ảnh:</lable>
        <input type="file" name="images" accept="image/*">

        <button type="submit" name="add">Thêm dữ liệu</button>
    </form>

    <h3>Danh sách Users</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Hình ảnh</th>
            <th>Chức năng</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['admin_id'] ?></td>

            <td>
                <?= htmlspecialchars($row['admin_email']) ?>
            </td>

            <td>
                <?php if (!empty($row['images'])) { ?>
                    <img src="../<?= htmlspecialchars($row['images']) ?>"
                         alt="Ảnh">
                <?php } else { ?>
                    Không có ảnh
                <?php } ?>
            </td>

            <td>
                <a class="btn"
                   href="edit.php?id=<?= $row['admin_id'] ?>">
                   Sửa
                </a>

                <a class="btn delete"
                   href="delete.php?id=<?= $row['admin_id'] ?>"
                   onclick="return confirm('Bạn có chắc muốn xóa?')">
                   Xóa
                </a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <br>
    <a href="../index.php" class="btn">Xem Danh Sách</a>

</div>

</body>
</html>
