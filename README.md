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

1. **Clona el repositorio**
   ```bash
   git clone https://github.com/DavidParrillas/srht.git
   cd srht
   ```

2. **Configura la base de datos**
   - Crea una base de datos en MySQL:
     ```sql
     CREATE DATABASE srht CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   - Importa el script de tablas y datos semilla:
     ```bash
     mysql -u tu_usuario -p srht < database/script.sql
     ```

3. **Configura la conexión a la base de datos**
   - Copia el archivo de ejemplo `.env.example` a un nuevo archivo llamado `.env`:
     ```bash
     cp .env.example .env
     ```
   - Edita el archivo `.env` y coloca tus credenciales de base de datos. Este archivo es ignorado por Git para proteger tu información sensible.
     ```dotenv
     DB_HOST=127.0.0.1
     DB_NAME=srht
     DB_USER=tu_usuario
     DB_PASSWORD=tu_contraseña
     ```

4. **Inicia el servidor embebido de PHP**
   - El archivo `php_error.log` se creará automáticamente la primera vez que se registre un error. Asegúrate de que el servidor web tenga permisos para escribir en la raíz del proyecto.
     ```bash
     php -S localhost:8000 -t .
     ```

   Accede a [http://localhost:8000](http://localhost:8000) en tu navegador.

## Usuarios de prueba

- **Admin:**  
  Usuario: `admin`  
  Contraseña: `admin123`

- **Gerente:**  
  Usuario: `gerente`  
  Contraseña: `gerente123`

- **Recepción:**  
  Usuario: `recepcion`  
  Contraseña: `recepcion123`

## Estructura del proyecto

- `controllers/` — Lógica de cada módulo
- `models/` — Acceso a datos
- `views/` — Vistas y layouts
- `config/` — Configuración de base de datos
- `database/` — Scripts SQL
- `public/` — Recursos estáticos (opcional)


---
