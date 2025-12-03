# Инструкция по деплою на Beget хостинг

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

## Подготовка проекта к деплою

### 1. Инициализация Git репозитория

```bash
# Инициализируем репозиторий
git init

# Добавляем remote
git remote add origin https://github.com/Hellavion/expert-bridge.git

# Создаем ветку develop
git checkout -b develop

# Добавляем все файлы
git add .

# Первый коммит
git commit -m "Initial commit: Expert Bridge project"

# Пушим develop
git push -u origin develop
```

### 2. Сборка фронтенда для продакшена

```bash
# Собираем production build
npm run build

# Проверяем что появилась папка public/build
ls -la public/build
```

### 3. Создание ветки main для продакшена

```bash
# Создаем ветку main
git checkout -b main

# Пушим main
git push -u origin main
```

---

## Деплой на Beget хостинг

### Шаг 1: Подключение по SSH

```bash
ssh username@expert-bridge.ru
```

### Шаг 2: Клонирование репозитория

```bash
# Переходим в директорию сайта
cd ~/expert-bridge.ru

# Клонируем main ветку
git clone -b main https://github.com/Hellavion/expert-bridge.git .
```

### Шаг 3: Настройка окружения

```bash
# Копируем production конфиг
cp .env.production.example .env

# Генерируем APP_KEY
php artisan key:generate

# Редактируем .env если нужно
nano .env
```

### Шаг 4: Установка зависимостей

```bash
# Устанавливаем composer зависимости (production only)
composer install --no-dev --optimize-autoloader

# Права доступа
chmod -R 755 storage bootstrap/cache
```

### Шаг 5: Миграция базы данных

```bash
# Запускаем миграции
php artisan migrate --force

# Опционально: заполняем начальными данными
php artisan db:seed
```

### Шаг 6: Оптимизация для продакшена

```bash
# Кешируем конфигурацию
php artisan config:cache

# Кешируем роуты
php artisan route:cache

# Кешируем представления
php artisan view:cache

# Оптимизируем автозагрузку
php artisan optimize
```

### Шаг 7: Настройка веб-сервера на Beget

1. В панели Beget перейдите в **Сайты** → **Управление сайтами**
2. Выберите домен `expert-bridge.ru`
3. Установите **Корневую директорию**: `~/expert-bridge.ru/public`
4. Включите **PHP 8.2+**
5. Настройте `.htaccess` (должен быть автоматически):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Обновление проекта (будущие деплои)

### На локальной машине

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

### На хостинге

```bash
# Подключаемся по SSH
ssh username@expert-bridge.ru

# Переходим в директорию
cd ~/expert-bridge.ru

# Получаем обновления
git pull origin main

# Устанавливаем новые зависимости (если были изменения)
composer install --no-dev --optimize-autoloader

# Запускаем миграции (если есть новые)
php artisan migrate --force

# Очищаем и пересоздаем кеш
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache
php artisan optimize
```

---

## Важные замечания

1. **Никогда не работайте напрямую с main веткой** - используйте develop для разработки
2. **Файл .env** не попадает в git (в .gitignore), настраивайте его вручную на хостинге
3. **База данных** - используйте только MySQL на хостинге (SQLite только для локальной разработки)
4. **Фронтенд** - всегда собирайте перед мержем в main (`npm run build`)
5. **Права доступа** - storage и bootstrap/cache должны быть доступны для записи
6. **APP_DEBUG=false** - обязательно в production для безопасности
7. **Индексация** - сайт защищен от поисковых роботов (robots.txt + meta tags)

---

## Проверка работоспособности

После деплоя проверьте:

1. ✅ Главная страница открывается: `https://expert-bridge.ru`
2. ✅ Форма регистрации клиентов работает
3. ✅ Админка доступна: `https://expert-bridge.ru/admin/dashboard`
4. ✅ Логин через Laravel Fortify работает
5. ✅ База данных подключена (данные сохраняются)

---

## Создание первого администратора

```bash
# Через SSH на хостинге
php artisan tinker

# В tinker выполните:
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@expert-bridge.ru';
$user->password = bcrypt('your-secure-password');
$user->email_verified_at = now();
$user->save();
```

---

## Troubleshooting

### Ошибка 500
- Проверьте `storage/logs/laravel.log`
- Убедитесь что `storage/` имеет права 755

### База данных не подключается
- Проверьте учетные данные в `.env`
- Убедитесь что база создана в панели Beget

### Страницы не загружаются
- Проверьте что корневая директория указывает на `public/`
- Проверьте `.htaccess` в `public/`

### Стили не применяются
- Убедитесь что `npm run build` был выполнен
- Проверьте наличие `public/build/manifest.json`
- Очистите кеш: `php artisan view:clear`

---

## Контакты для поддержки

- Хостинг: support@beget.com
- GitHub: https://github.com/Hellavion/expert-bridge
