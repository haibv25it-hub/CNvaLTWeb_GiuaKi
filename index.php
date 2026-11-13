
<?php
include "connect.php";

$sql = "SELECT * FROM Users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách người dùng</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>DANH SÁCH NGƯỜI DÙNG</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Hình ảnh</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['admin_id'] ?></td>

            <td>
                <?= htmlspecialchars($row['admin_email']) ?>
            </td>

            <td>
                <?php if (!empty($row['images'])) { ?>
                    <img src="<?= htmlspecialchars($row['images']) ?>" alt="Ảnh">
                <?php } else { ?>
                    Không có ảnh
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>

    <p>
        <a href="admin/index.php" class="btn">Quản lý Users</a>
    </p>
</div>

</body>
</html>
