# Sistema de Reservaciones Hoteleras

Este proyecto es un sistema web para la gestión de reservaciones hoteleras, clientes, habitaciones, paquetes turísticos, usuarios y reportes.

## Características principales

- Gestión de usuarios y roles (admin, gerente, recepción)
- Control de acceso y seguridad
- Gestión de clientes, habitaciones, paquetes y reservaciones
- Planes de pago y reportes gerenciales
- Panel de control (dashboard) con estadísticas

## Requisitos

- PHP >= 8.0
- MySQL >= 8.0
- Navegador web moderno

## Instalación

### Dependencias

Para instalar PHP y MySQL en sistemas basados en Debian/Ubuntu, puedes usar los siguientes comandos:

**MySQL**
```bash
sudo apt update
sudo apt install mysql-server
```

**PHP**
```bash
sudo apt update
sudo apt install php libapache2-mod-php php-mysql
```

1. **Clona el repositorio**
   ```bash
   git clone https://github.com/DavidParrillas/srht.git
   cd srht
   ```

2. **Configura la base de datos**
   - Crea una base de datos en MySQL:
     ```sql
     CREATE DATABASE hoteltorremolinos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   - Importa el script de tablas y datos semilla:
     ```bash
     mysql -u tu_usuario -p hoteltorremolinos < database/script.sql
     ```

3. **Configura la conexión a la base de datos**
   - Abre el archivo `config/database.php`.
   - Edita los valores para que coincidan con tus credenciales de MySQL.
     ```php
     'user' => 'tu_usuario', // Por ejemplo, 'root'
     'password' => 'tu_contraseña' // Puede estar vacío '' si no tienes contraseña
     ```

4. **Inicia el servidor embebido de PHP**
   - El archivo `php_error.log` se creará automáticamente la primera vez que se registre un error. Asegúrate de que el servidor web tenga permisos para escribir en la raíz del proyecto.
     ```bash
     php -S localhost:8000 -t .
     ```

   Accede a [http://localhost:8000](http://localhost:8000) en tu navegador.

## Usuarios de prueba

- **Admin:**  
  Correo: `admin@torremolinos.com`  
  Contraseña: `admin123`

- **Gerente:**  
  Correo: `gerente@torremolinos.com`  
  Contraseña: `gerente123`

- **Recepción:**  
  Correo: `recepcion@torremolinos.com`  
  Contraseña: `recepcion123`

## Estructura del proyecto

- `controllers/` — Lógica de cada módulo
- `models/` — Acceso a datos
- `views/` — Vistas y layouts
- `config/` — Configuración de base de datos
- `database/` — Scripts SQL
- `public/` — Recursos estáticos (opcional)


---