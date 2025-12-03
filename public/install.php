<?php
/**
 * Веб-установщик для Expert Bridge
 * Использовать только для первоначальной установки!
 * После установки УДАЛИТЕ этот файл!
 */

// Проверяем, что находимся в public/
if (!file_exists(__DIR__ . '/../.env.example')) {
    die('Error: Файл должен находиться в папке public/');
}

$baseDir = dirname(__DIR__);
$errors = [];
$success = [];

// Функция для выполнения artisan команд
function runArtisan($command) {
    global $baseDir;
    $output = [];
    $returnCode = 0;
    
    chdir($baseDir);
    exec("php artisan {$command} 2>&1", $output, $returnCode);
    
    return [
        'success' => $returnCode === 0,
        'output' => implode("\n", $output),
        'code' => $returnCode
    ];
}

// Проверка POST запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_env') {
        // Создание .env файла
        $envContent = $_POST['env_content'] ?? '';
        if (file_put_contents($baseDir . '/.env', $envContent)) {
            $success[] = "✅ Файл .env создан успешно";
        } else {
            $errors[] = "❌ Ошибка создания файла .env";
        }
    }
    
    if ($action === 'generate_key') {
        $result = runArtisan('key:generate --force');
        if ($result['success']) {
            $success[] = "✅ APP_KEY сгенерирован";
        } else {
            $errors[] = "❌ Ошибка генерации APP_KEY: " . $result['output'];
        }
    }
    
    if ($action === 'migrate') {
        $result = runArtisan('migrate --force');
        if ($result['success']) {
            $success[] = "✅ Миграции выполнены успешно";
        } else {
            $errors[] = "❌ Ошибка миграций: " . $result['output'];
        }
    }
    
    if ($action === 'optimize') {
        $commands = ['config:cache', 'route:cache', 'view:cache', 'optimize'];
        foreach ($commands as $cmd) {
            $result = runArtisan($cmd);
            if ($result['success']) {
                $success[] = "✅ Выполнено: {$cmd}";
            } else {
                $errors[] = "❌ Ошибка {$cmd}: " . $result['output'];
            }
        }
    }
    
    if ($action === 'create_admin') {
        $name = $_POST['admin_name'] ?? 'Admin';
        $email = $_POST['admin_email'] ?? '';
        $password = $_POST['admin_password'] ?? '';
        
        if ($email && $password) {
            $code = <<<CODE
\$user = new \App\Models\User();
\$user->name = '{$name}';
\$user->email = '{$email}';
\$user->password = bcrypt('{$password}');
\$user->email_verified_at = now();
\$user->save();
echo 'User created with ID: ' . \$user->id;
CODE;
            
            $tempFile = $baseDir . '/temp_create_user.php';
            file_put_contents($tempFile, "<?php\nrequire __DIR__ . '/vendor/autoload.php';\n\$app = require_once __DIR__ . '/bootstrap/app.php';\n\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();\n{$code}");
            
            exec("php {$tempFile} 2>&1", $output, $returnCode);
            unlink($tempFile);
            
            if ($returnCode === 0) {
                $success[] = "✅ Администратор создан: {$email}";
            } else {
                $errors[] = "❌ Ошибка создания администратора: " . implode("\n", $output);
            }
        } else {
            $errors[] = "❌ Укажите email и пароль администратора";
        }
    }
}

// Проверки окружения
$checks = [
    'PHP Version >= 8.2' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'Composer dependencies' => file_exists($baseDir . '/vendor/autoload.php'),
    'Storage writable' => is_writable($baseDir . '/storage'),
    'Bootstrap/cache writable' => is_writable($baseDir . '/bootstrap/cache'),
    '.env exists' => file_exists($baseDir . '/.env'),
];

$envExample = file_exists($baseDir . '/.env.production.example') 
    ? file_get_contents($baseDir . '/.env.production.example')
    : file_get_contents($baseDir . '/.env.example');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expert Bridge - Установка</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .section { margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 6px; }
        .section h2 { color: #444; margin-bottom: 15px; font-size: 18px; }
        .check { padding: 8px 0; display: flex; justify-content: space-between; border-bottom: 1px solid #eee; }
        .check:last-child { border-bottom: none; }
        .status { font-weight: bold; }
        .status.ok { color: #22c55e; }
        .status.error { color: #ef4444; }
        .alert { padding: 15px; margin: 10px 0; border-radius: 6px; }
        .alert.success { background: #dcfce7; color: #166534; border: 1px solid #22c55e; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
        .alert.warning { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        button { background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; }
        button:hover { background: #2563eb; }
        button:disabled { background: #9ca3af; cursor: not-allowed; }
        button.danger { background: #ef4444; }
        button.danger:hover { background: #dc2626; }
        textarea { width: 100%; min-height: 200px; font-family: monospace; font-size: 12px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; }
        .actions { display: flex; gap: 10px; margin-top: 15px; }
        .warning-box { background: #fef3c7; border: 2px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .warning-box h3 { color: #92400e; margin-bottom: 10px; }
        .warning-box p { color: #78350f; line-height: 1.6; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Expert Bridge</h1>
        <p class="subtitle">Веб-установщик для Beget хостинга</p>

        <div class="warning-box">
            <h3>⚠️ ВАЖНО!</h3>
            <p><strong>После завершения установки обязательно удалите этот файл (install.php) с сервера!</strong></p>
            <p>Этот скрипт предназначен только для первоначальной установки и представляет угрозу безопасности, если остается доступным.</p>
        </div>

        <?php foreach ($success as $msg): ?>
            <div class="alert success"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>

        <?php foreach ($errors as $msg): ?>
            <div class="alert error"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>

        <!-- Проверка окружения -->
        <div class="section">
            <h2>1. Проверка окружения</h2>
            <?php foreach ($checks as $name => $status): ?>
                <div class="check">
                    <span><?= $name ?></span>
                    <span class="status <?= $status ? 'ok' : 'error' ?>">
                        <?= $status ? '✓ OK' : '✗ FAIL' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Создание .env -->
        <?php if (!$checks['.env exists']): ?>
        <div class="section">
            <h2>2. Создание файла .env</h2>
            <p style="margin-bottom: 10px;">Отредактируйте конфигурацию и нажмите "Создать .env":</p>
            <form method="POST">
                <textarea name="env_content"><?= htmlspecialchars($envExample) ?></textarea>
                <div class="actions">
                    <button type="submit" name="action" value="create_env">Создать .env файл</button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="section">
            <h2>2. Файл .env</h2>
            <div class="alert success">✅ Файл .env уже существует</div>
        </div>
        <?php endif; ?>

        <!-- Генерация APP_KEY -->
        <div class="section">
            <h2>3. Генерация APP_KEY</h2>
            <form method="POST">
                <button type="submit" name="action" value="generate_key">Генерировать APP_KEY</button>
            </form>
        </div>

        <!-- Миграции -->
        <div class="section">
            <h2>4. Миграции базы данных</h2>
            <div class="alert warning">
                <strong>Внимание!</strong> Убедитесь, что настройки базы данных в .env правильные:
                <br><code>DB_DATABASE=rfb7925n_bridge</code>
                <br><code>DB_USERNAME=rfb7925n_bridge</code>
                <br><code>DB_PASSWORD=@Avi197350</code>
            </div>
            <form method="POST">
                <button type="submit" name="action" value="migrate">Запустить миграции</button>
            </form>
        </div>

        <!-- Создание администратора -->
        <div class="section">
            <h2>5. Создание администратора</h2>
            <form method="POST">
                <input type="text" name="admin_name" placeholder="Имя администратора" value="Admin" required>
                <input type="email" name="admin_email" placeholder="Email администратора" required>
                <input type="password" name="admin_password" placeholder="Пароль" required>
                <button type="submit" name="action" value="create_admin">Создать администратора</button>
            </form>
        </div>

        <!-- Оптимизация -->
        <div class="section">
            <h2>6. Оптимизация (финальный шаг)</h2>
            <p style="margin-bottom: 10px;">Кеширование конфигурации, маршрутов и представлений для production:</p>
            <form method="POST">
                <button type="submit" name="action" value="optimize">Оптимизировать для production</button>
            </form>
        </div>

        <!-- Финал -->
        <div class="section">
            <h2>7. Завершение установки</h2>
            <div class="alert warning">
                <strong>После завершения всех шагов:</strong>
                <ol style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                    <li>Удалите файл <code>public/install.php</code></li>
                    <li>Проверьте работу сайта: <a href="/" target="_blank">Главная страница</a></li>
                    <li>Войдите в админку: <a href="/login" target="_blank">Вход</a></li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
