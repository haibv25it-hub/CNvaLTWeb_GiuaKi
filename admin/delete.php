
<?php
include "../connect.php";

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM Users WHERE admin_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$conn->query("
    SELECT @next_id := IFNULL(MAX(admin_id), 0) + 1
    FROM Users
");

$conn->query("
    ALTER TABLE Users AUTO_INCREMENT = 1
");

header("Location: index.php");
exit;
?>
