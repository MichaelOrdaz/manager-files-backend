<p align="center"><a href="https://puller.mx" target="_blank"><img src="http://puller.mx/statics/school-logo.png" width="400"></a></p>

## Puller Backend - API template

---
> [!WARNING]
> Antes de agregar codigo a este proyecto deberas leer y seguir las buenas practicas aqui [Buenas practicas Laravel](https://github.com/alexeymezenin/laravel-best-practices)
---
Use los siguientes comandos para correr el proyecto

- Instalar mysql y phpmyadmin con docker-compose

    ```bash
        docker-compose up
    ```
- Detener mysql y phpmyadmin con docker-compose

    ```bash
        docker-compose stop
    ```

- Crear Base de datos y ejecutar semillas

    ```bash
        php artisan migrate:fresh --seed
    ```

- Generar las llaves del proyecto, usar el siguiente comando

    ```bash
        php artisan passport:install --force
    ```

- Ver documentación del API

    ```bash
        npm install
        npm run preview
        // Ver en el puerto 8000
    ```

<br>

**Herramientas de desarrollo**

- [Docker en windows](https://enmilocalfunciona.io/instalando-y-probando-docker-en-windows-10/)
- [Docker en linux](https://diarioinforme.com/como-instalar-docker-y-docker-compose-en-linux/)

**Este proyecto hace uso de las siguientes librerias**

- [Laravel Auditing](https://github.com/owen-it/laravel-auditing) : Guarda registro de las acciones de los usuarios
- [Laravel Permissions](https://github.com/spatie/laravel-permission) : Ayuda a la gestion de permisos de cada usuario
- [Laravel Intervention image](https://github.com/Intervention/image) : Ayuda a la refactorizacion de tama;o de imagenes para optimizar el almacenamiento
- [Laravel Auth JWT](https://github.com/tymondesigns/jwt-auth) : Genera Web tokens a partir de la informacion del usuario
- [Laravel CORS](https://github.com/fruitcake/laravel-cors) : Ayuda en el manejo de Origenes cruzados y cabezeras de peticiones
- [Laravel Slugify](https://github.com/cocur/slugify) : Ayuda a el manejo compleo de cadenas, limpiar cadenas de caracteres no deseados mediante reglas
- [Laravel Soft cascade](https://github.com/Askedio/laravel-soft-cascade) : Ayuda a que los registros con softDelete se vean reflejados en cadena

**Manuales del proyecto**

- [Manual para crear permisos](./Permisos.md)
- [Manual para escribir controladores](./Controladores.md)
- [Teoria que necesitas saber antes de escribir end points](./Generacion-api.md)
- [Manual para documentar end points](./Documentación-swagger.md)
- [Manual y teoría para pruebas](./Pruebas.md)



# despliegue en produccion

para levantar los servicios de docker ejecutar
docker compose -f docker-compose.prod.yml up -d

luego entrar al contenedor que tiene el servicio de app-laravel
docker compose exec -it app-laravel bash

ya dentro del contenedor instalar dependencias de composer
composer install

generar llave de laravel
php artisan key:generate

- creat tablas de Base de datos y ejecutar semillas

    ```bash
        php artisan migrate:fresh --seed
    ```

- Generar las llaves del proyecto, usar el siguiente comando

    ```bash
        php artisan passport:install --force
    ```

- Ver documentación del API

    ```bash
        npm install
        npm run preview
        // Ver en el puerto 8000
    ```

recuerda agregar los accessos de passport al archivo .env

docker compose exec app-laravel php artisan key:generate
docker compose exec app-laravel php artisan migrate:fresh --seed
docker compose exec app-laravel php artisan passport:install
docker compose exec app-laravel sh -c 'echo "PASSPORT_PERSONAL_ACCESS_CLIENT_ID=\"1\"" >> .env'
docker compose exec app-laravel sh -c 'echo "PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=\"S56JQ4bQTGaHQmItGhashpxuTQFubcj49q0ByoVH\"" >> .env'
