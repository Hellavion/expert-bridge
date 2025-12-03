# Инструкция по деплою на Beget хостинг (FTP)

## Информация о проекте

- **Домен**: expert-bridge.ru
- **Репозиторий**: https://github.com/Hellavion/expert-bridge.git
- **База данных**: MySQL
  - Database: `rfb7925n_bridge`
  - Username: `rfb7925n_bridge`
  - Password: `@Avi197350`
  - Host: `localhost`

## Стратегия веток

- **main** - продакшен ветка (только собранный код для деплоя)
- **develop** - разработка (весь исходный код)

---

## ⚠️ Важно: Деплой через FTP (без SSH)

Так как на бесплатном тарифе Beget нет доступа по SSH, используется **веб-установщик** `install.php` для настройки проекта через браузер.

---

## Деплой на Beget хостинг

### Шаг 1: Скачать проект с GitHub

1. Перейдите на https://github.com/Hellavion/expert-bridge
2. Выберите ветку **`main`**
3. Нажмите **Code → Download ZIP**
4. Распакуйте архив на вашем компьютере

### Шаг 2: Подключиться к хостингу по FTP

Используйте FTP-клиент (например, FileZilla):

- **Host**: ftp.expert-bridge.ru (или IP от Beget)
- **Username**: ваш FTP логин от Beget
- **Password**: ваш FTP пароль от Beget
- **Port**: 21

### Шаг 3: Загрузить файлы на хостинг

1. **Откройте директорию вашего сайта** на хостинге (обычно `expert-bridge.ru/public_html` или просто `expert-bridge.ru`)

2. **Удалите все стандартные файлы** Beget из этой папки (index.html и т.д.)

3. **Загрузите ВСЕ файлы** из распакованного архива в корень директории сайта:
   ```
   expert-bridge.ru/
   ├── app/
   ├── bootstrap/
   ├── config/
   ├── database/
   ├── public/
   ├── resources/
   ├── routes/
   ├── storage/
   ├── vendor/
   ├── .env.production.example
   ├── .htaccess
   ├── artisan
   ├── composer.json
   └── ...
   ```

⚠️ **ВАЖНО**: Загружайте файлы в корень, НЕ создавайте дополнительную папку!

### Шаг 4: Настроить корневую директорию в панели Beget

1. Зайдите в **Панель управления Beget**
2. Откройте раздел **Сайты → Управление сайтами**
3. Найдите домен `expert-bridge.ru`
4. В поле **"Корневая директория"** укажите: `public`
   - Было: `/expert-bridge.ru/public_html`
   - Стало: `/expert-bridge.ru/public_html/public` (или просто `/public` относительно корня)
5. Сохраните изменения

### Шаг 5: Установить права доступа (CHMOD)

Через FTP-клиент установите права на папки:

- **storage/** → 755 (рекурсивно, включая все подпапки)
- **bootstrap/cache/** → 755

В FileZilla: ПКМ на папке → File Permissions → установить 755

### Шаг 6: Запустить веб-установщик

1. Откройте в браузере: **https://expert-bridge.ru/install.php**

2. **Следуйте шагам установщика**:

   **Шаг 1**: Проверка окружения (должны быть все ✓ зеленые)

   **Шаг 2**: Создание файла `.env`
   - Отредактируйте конфигурацию (особенно `APP_URL` и `DB_*`)
   - Нажмите "Создать .env файл"

   **Шаг 3**: Генерация APP_KEY
   - Нажмите "Генерировать APP_KEY"

   **Шаг 4**: Миграции базы данных
   - Убедитесь что данные БД правильные
   - Нажмите "Запустить миграции"

   **Шаг 5**: Создание администратора
   - Заполните форму (email и пароль)
   - Нажмите "Создать администратора"

   **Шаг 6**: Оптимизация
   - Нажмите "Оптимизировать для production"

3. **ОБЯЗАТЕЛЬНО удалите файл `install.php`** после завершения установки!
   - Через FTP удалите `public/install.php`

### Шаг 7: Проверка работоспособности

Откройте сайт и проверьте:

1. ✅ Главная страница: https://expert-bridge.ru
2. ✅ Форма регистрации клиентов работает
3. ✅ Админка: https://expert-bridge.ru/login
4. ✅ Вход с созданными credentials

---

## Обновление проекта (будущие деплои)

### На локальной машине (разработка):

```bash
# Работаем в develop
git checkout develop

# Делаем изменения и коммитим
git add .
git commit -m "Описание изменений"
git push origin develop

# Собираем production build
npm run build

# Мержим в main
git checkout main
git merge develop
git push origin main
```

### На хостинге (через FTP):

1. Скачайте обновленный проект с GitHub (ветка `main`)
2. Через FTP загрузите **измененные файлы**:
   - `app/` - если изменения в контроллерах/моделях
   - `public/build/` - если пересобран фронтенд
   - `database/migrations/` - если есть новые миграции
   - `routes/` - если изменились маршруты

3. **Если были новые миграции**, временно создайте файл `public/migrate.php`:

```php
<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Artisan::call('migrate', ['--force' => true]);
echo "Migrations completed!\n";
echo Artisan::output();

// УДАЛИТЕ ЭТОТ ФАЙЛ ПОСЛЕ ВЫПОЛНЕНИЯ!
```

   Откройте `https://expert-bridge.ru/migrate.php`, затем **удалите файл**!

4. **Очистите кеш**, создайте временно `public/clear-cache.php`:

```php
<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Artisan::call('config:clear');
Artisan::call('route:clear');
Artisan::call('view:clear');
Artisan::call('cache:clear');

Artisan::call('config:cache');
Artisan::call('route:cache');
Artisan::call('view:cache');
Artisan::call('optimize');

echo "Cache cleared and optimized!\n";

// УДАЛИТЕ ЭТОТ ФАЙЛ ПОСЛЕ ВЫПОЛНЕНИЯ!
```

   Откройте `https://expert-bridge.ru/clear-cache.php`, затем **удалите файл**!

---

## Безопасность

### Важные файлы, которые НЕЛЬЗЯ открывать публично:

- `.env` - защищен через `.htaccess`
- `storage/` - защищен через `.htaccess`
- `vendor/` - защищен через `.htaccess`
- `install.php` - **УДАЛИТЕ после установки!**
- `migrate.php` - **удаляйте после использования!**
- `clear-cache.php` - **удаляйте после использования!**

### Файл `.htaccess` в корне проекта:

Убедитесь что загружен `.htaccess` файл в **корень проекта** (не в public/), который защищает важные директории.

---

## Troubleshooting

### Ошибка 500

1. Проверьте права доступа: `storage/` и `bootstrap/cache/` должны быть 755
2. Проверьте что `.env` файл создан и `APP_KEY` сгенерирован
3. Проверьте логи в `storage/logs/laravel.log` (скачайте через FTP)

### База данных не подключается

1. Проверьте credentials в `.env`:
   ```
   DB_HOST=localhost
   DB_DATABASE=rfb7925n_bridge
   DB_USERNAME=rfb7925n_bridge
   DB_PASSWORD=@Avi197350
   ```
2. Убедитесь что база создана в панели Beget

### Страницы не загружаются

1. Проверьте что корневая директория указывает на `public/`
2. Проверьте наличие `public/.htaccess` с правилами rewrite

### Стили не применяются

1. Убедитесь что папка `public/build/` загружена полностью
2. Проверьте наличие `public/build/manifest.json`
3. Очистите кеш через `clear-cache.php`

---

## Контакты для поддержки

- Хостинг: support@beget.com
- GitHub: https://github.com/Hellavion/expert-bridge
- Домен: https://expert-bridge.ru
