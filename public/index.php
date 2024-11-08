<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "symfony_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Обработка отправки формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $customer = $_POST['customer'];

    if (empty($name) || empty($customer)) {
        $error_message = "Все поля должны быть заполнены.";
    } else {
        $stmt = $conn->prepare("INSERT INTO project (name, customer) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $customer);

        if ($stmt->execute()) {
            $stmt->close();
            // Перенаправление на ту же страницу, чтобы избежать повторного POST-запроса
            header("Location: index.php?success=1");
            exit();
        } else {
            $error_message = "Ошибка при добавлении проекта: " . $conn->error;
        }
    }
}

// Удаление проекта
if (isset($_GET['delete'])) {
    $project_id = (int)$_GET['delete'];

    if ($project_id > 0) {
        $stmt = $conn->prepare("DELETE FROM project WHERE id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $stmt->close();

        // Перенаправление для обновления списка
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестирование формы</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Форма для добавления проекта</h1>
    <a href="add_developer.php" class="btn btn-success mb-3">Добавить разработчика</a>
    <a href="developer.php" class="btn btn-success mb-3">Разработчики</a>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Новый проект добавлен!</div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Название проекта</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="customer" class="form-label">Заказчик</label>
            <input type="text" class="form-control" id="customer" name="customer" required>
        </div>
        <button type="submit" class="btn btn-primary">Добавить проект</button>
    </form>

    <hr>

    <h2>Список проектов</h2>
    <?php
    // Получаем проекты из базы данных
    $result = $conn->query("SELECT id, name, customer FROM project");

    if ($result && $result->num_rows > 0) {
        echo "<ul class='list-group'>";
        while($row = $result->fetch_assoc()) {
            echo "<li class='list-group-item'>"
                . htmlspecialchars($row["name"]) . " - " . htmlspecialchars($row["customer"])
                . " <a href='?delete=" . (int)$row["id"] . "' class='btn btn-danger btn-sm'>Удалить</a>"
                . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Нет проектов для отображения.</p>";
    }

    $conn->close();
    ?>
</div>
</body>
</html>
