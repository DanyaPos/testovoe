<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "symfony_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение списка проектов для выпадающего списка
$projectQuery = "SELECT id, name FROM project";
$projects = $conn->query($projectQuery);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $position = $_POST['position'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $projectId = $_POST['project_id']; // Получаем ID проекта из формы

    // Проверка, существует ли указанный project_id в таблице project
    $projectCheckQuery = $conn->prepare("SELECT COUNT(*) FROM project WHERE id = ?");
    $projectCheckQuery->bind_param("i", $projectId);
    $projectCheckQuery->execute();
    $projectCheckQuery->bind_result($projectExists);
    $projectCheckQuery->fetch();
    $projectCheckQuery->close();

    if ($projectExists) { // Если проект существует, продолжаем
        $stmt = $conn->prepare("INSERT INTO developer (full_name, position, email, phone, project_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $fullName, $position, $email, $phone, $projectId);

        if ($stmt->execute()) {
            header("Location: developer.php");
            exit();
        } else {
            echo "Ошибка: " . $stmt->error;
        }
    } else {
        echo "Ошибка: Указанный проект не существует.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить программиста</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Добавить программиста</h1>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="full_name" class="form-label">Имя:</label>
            <input type="text" name="full_name" id="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="position" class="form-label">Должность:</label>
            <input type="text" name="position" id="position" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Телефон:</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label for="project_id" class="form-label">Проект:</label>
            <select name="project_id" id="project_id" class="form-select" required>
                <option value="">Выберите проект</option>
                <?php while ($project = $projects->fetch_assoc()): ?>
                    <option value="<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Добавить</button>
        <a href="developer.php" class="btn btn-secondary">Назад к списку разработчиков</a>
    </form>
</div>
</body>
</html>
