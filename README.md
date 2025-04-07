
# Weather API 🌦️

Weather API es un proyecto desarrollado en Laravel que proporciona una API REST para consultar información del clima. Incluye funcionalidades como historial de búsquedas, ciudades favoritas y autenticación por roles. Además, cuenta con documentación Swagger generada automáticamente.

Este proyecto puede ejecutarse con Docker o directamente en tu máquina usando PHP y Composer. La base de datos se encuentra en la nube, por lo que no necesitas levantar una instancia local.

---

## 🐳 Uso con Docker

Si deseas usar Docker, el proyecto incluye un entorno preconfigurado que puedes levantar fácilmente.

1. Construye y levanta los contenedores con:

```bash
make start-all --construye el proyecto
```
```bash
make down-all -- baja el proyecto
```


2. Una vez estén activos los servicios, instala las dependencias, genera la clave de la aplicación y configura la base de datos:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```

3. Accede a la documentación Swagger desde tu navegador en:

📄 [http://localhost:8001/api/documentation](http://localhost:8001/api/documentation)

---

## 💻 Uso sin Docker

También puedes ejecutar el proyecto de forma local, sin Docker. Solo necesitas tener PHP, Composer y una conexión a la base de datos en la nube.

1. Clona el repositorio:

```bash
git clone https://github.com/ronni18/weather-api.git
cd weather-api
```

2. Instala las dependencias del proyecto:

```bash
composer install
```


3. Genera la clave de la aplicación y realiza las migraciones y seeders:

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
```

4. Levanta el servidor:

```bash
php artisan serve
```

---

## ⚙️ Variables importantes en `.env`

Asegúrate de tener al menos las siguientes variables configuradas:

```dotenv
APP_NAME=WeatherAPI
APP_ENV=local
APP_KEY= # generado con artisan
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST= # host de tu base de datos en la nube
DB_PORT=3306
DB_DATABASE= # nombre de tu base de datos
DB_USERNAME= # usuario
DB_PASSWORD= # contraseña

SWAGGER_VERSION=3.0
```

---

## 🧪 Pruebas

Para ejecutar las pruebas automatizadas del proyecto:

```bash
php artisan test
```

O directamente con PHPUnit:

```bash
vendor/bin/phpunit
```

---

## 📝 Licencia

Este proyecto está licenciado bajo la Licencia MIT.
