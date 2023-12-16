
1. **Путь:**

    ```bash
    cd docker
    ```

2. **Docker:**

    ```bash
    docker-compose up --build -d
    ```

3. **Налаштування БД:**

    ```bash
    1. docker exec -it tools_php-mysql sh
    2. mysql -uroot -p  
       password:s123123
   
    3. CREATE DATABASE `test`;
    4. CREATE USER 'user1'@'%' IDENTIFIED BY 's123';
    5. GRANT ALL PRIVILEGES ON `test` . * TO 'user1'@'%';

    ```


4. **БД http://localhost:8000:**

    ```bash
    mysql, user1, s123
    ```

5. **Cron:**

    ```bash
    crontab -e
    ```

6. **Cron: перевірка ціни кожну хвилину. **

    ```bash
    * * * * * docker exec tools_php-php /usr/bin/env php /var/www/public/update-prices.php >> <путь_до_проекту>/olx/cron.log 2>&1
    ```



