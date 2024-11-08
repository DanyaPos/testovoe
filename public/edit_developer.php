<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "symfony_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение текущих данных разработчика по id из URL
if (!isset($_GET['id'])) {
    die("ID разработчика не указан.");
}

$developerId = (int)$_GET['id'];
$developerQuery = $conn->prepare("SELECT * FROM developer WHERE id = ?");
$developerQuery->bind_param("i", $developerId);
$developerQuery->execute();
$developerResult = $developerQuery->get_result();

if ($developerResult->num_rows === 0) {
    die("Разработчик не найден.");
}

$developer = $developerResult->fetch_assoc();

// Получение списка проектов для выпадающего списка
$projectQuery = "SELECT id, name FROM project";
$projects = $conn->query($projectQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position = $_POST['position'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $projectId = $_POST['project_id']; // Новый ID проекта

    // Обновление данных разработчика (имя не изменяется)
    $stmt = $conn->prepare("UPDATE developer SET position = ?, email = ?, phone = ?, project_id = ? WHERE id = ?");
    $stmt->bind_param("sssii", $position, $email, $phone, $projectId, $developerId);

    if ($stmt->execute()) {
        header("Location: developer.php");
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать разработчика</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Редактировать разработчика</h1>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="full_name" class="form-label">Имя:</label>
            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($developer['full_name']); ?>" disabled>
        </div>

        <div class="mb-3">
            <label for="position" class="form-label">Должность:</label>
            <input type="text" name="position" id="position" class="form-control" value="<?php echo htmlspecialchars($developer['position']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($developer['email']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Телефон:</label>
            <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($developer['phone']); ?>">
        </div>

        <div class="mb-3">
            <label for="project_id" class="form-label">Проект:</label>
            <select name="project_id" id="project_id" class="form-select" required>
                <option value="">Выберите проект</option>
                <?php while ($project = $projects->fetch_assoc()): ?>
                    <option value="<?php echo $project['id']; ?>" <?php if ($project['id'] == $developer['project_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($project['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        <a href="developer.php" class="btn btn-secondary">Назад к списку разработчиков</a>
    </form>
</div>
</body>
</html>
