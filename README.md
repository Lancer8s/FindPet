# 🐾 FindPet  

**FindPet** es una plataforma web desarrollada en PHP que permite publicar y consultar avisos de mascotas perdidas o en adopción en el departamento de Cochabamba. Los usuarios pueden crear cuentas, subir imágenes y contactar al publicante vía WhatsApp.  

---

## ⚙️ Requisitos  

- PHP 8.x  
- [XAMPP](https://www.apachefriends.org/es/index.html) (con Apache y MySQL/MariaDB)  
- Extensión `php_pdo_sqlsrv` habilitada  
- SQL Server (local o remoto)  
- Navegador web moderno (Chrome, Firefox, Edge, etc.)  

---

## 🛠️ Instalación  

### 1. Clonar el repositorio  
- git clone https://github.com/tu-usuario/findpet.git
- cd findpet

### 2. Crear el usuario en SQL Server
-- Crear el login y el usuario
CREATE LOGIN pet WITH PASSWORD = 'publica';
CREATE USER pet FOR LOGIN pet;

-- Asignar permisos
ALTER ROLE db_owner ADD MEMBER pet;

### 4. Habilitar extensiones de SQL Server en PHP
- Abre el archivo php.ini (desde el panel de XAMPP haz clic en “Config” > “php.ini”)
- Asegúrate de que esten las siguientes líneas (quita el ; si lo tiene):
    extension=php_pdo_sqlsrv
    extension=php_sqlsrv

### 5. Cómo ejecutar el sistema
- Ejecuta los scripts sql de la carpeta FindPet/sqcripts_sql en SQLServer
- Abre XAMPP y enciende el módulo Apache.
- En tu navegador, accede al archivo principal de la aplicación:
    http://localhost/FindPet/views/home.php