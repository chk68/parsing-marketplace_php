
1. **Путь:**

    ```bash
    cd docker
    ```

2. **Docker:**

    ```bash
    docker-compose up --build -d
    ```

3. **БД http://localhost:8000:**

    ```bash
    mysql, user1, s123
    ```

4. **Cron:**

    ```bash
    crontab -e
    ```

4. **Cron: перевірка ціни кожну хвилину. **

    ```bash
    * * * * * docker exec tools_php-php /usr/bin/env php /var/www/public/update-prices.php >> <путь_до_проекту>/olx/cron.log 2>&1
    ```

