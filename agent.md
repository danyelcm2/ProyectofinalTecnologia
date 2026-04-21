Actúa como un desarrollador full stack senior especializado en aplicaciones web seguras.

Necesito que generes una aplicación web completa con las siguientes características:

STACK TECNOLÓGICO:
- Frontend: HTML, CSS, JavaScript
- Framework CSS: Bootstrap 5 (diseño moderno y responsivo)
- Backend: PHP (estructurado tipo MVC simple)
- Base de datos: MySQL (principal), pero con soporte para múltiples conexiones (simulación de SQL Server, Google Cloud SQL y Azure SQL)

----------------------------------------

1. LOGIN (UI + SEGURIDAD)

Diseña una pantalla de login moderna, centrada, responsiva usando Bootstrap 5.

Requisitos:
- Formulario con email y password
- Diseño atractivo (card, sombras, botones modernos)
- Validación en frontend y backend
- Contraseñas encriptadas usando password_hash() y password_verify()

Implementar autenticación de doble factor (2FA):
- Después del login correcto, mostrar pantalla para ingresar código
- Simular envío de código (puede ser generado en backend y mostrado en consola o variable)
- Validar código antes de permitir acceso

----------------------------------------

2. DASHBOARD PRINCIPAL (DESPUÉS DEL LOGIN)

Al iniciar sesión correctamente:

Mostrar una interfaz tipo dashboard con:
- Navbar superior
- Sidebar con menú:
  - Conexiones
  - Dashboard KPI
  - Formularios dinámicos
  - Logout

----------------------------------------

3. MÓDULO DE CONEXIONES MULTI-BASE DE DATOS

Pantalla donde el usuario puede seleccionar una conexión:

Opciones:
- MySQL
- SQL Server (simulado)
- Google Cloud SQL (simulado)
- Azure SQL (simulado)

Requisitos:
- Mostrar lista de conexiones en cards o tabla
- Al seleccionar una conexión, guardar en sesión el tipo de base de datos
- Crear un sistema de conexión dinámica en PHP (PDO)

----------------------------------------

4. DASHBOARD KPI

Mostrar datos provenientes de la base de datos seleccionada.

Requisitos:
- Consultar datos desde la BD
- Mostrar:
  - Total de registros
  - Indicadores KPI
  - Gráficas usando Chart.js

Ejemplos:
- Conteo de registros
- Promedios
- Datos agrupados

Las gráficas deben actualizarse automáticamente cuando se inserten nuevos datos.

----------------------------------------

5. FORMULARIO DINÁMICO (MUY IMPORTANTE)

Crear una sección donde:

- Se liste automáticamente las tablas de la base de datos
- Al seleccionar una tabla:
    - Obtener sus columnas dinámicamente
    - Generar un formulario automáticamente con inputs según los campos

Ejemplo:
- Si la tabla tiene:
    id, nombre, edad

Generar inputs:
- nombre (text)
- edad (number)

Requisitos:
- Insertar datos en la tabla seleccionada
- Usar prepared statements (PDO)
- Después de insertar:
    → actualizar automáticamente el dashboard KPI

----------------------------------------

6. SEGURIDAD

Implementar:
- Prepared statements (evitar SQL Injection)
- Manejo de sesiones seguro
- Validación de inputs
- Separación de lógica y vistas

----------------------------------------

7. ESTRUCTURA DEL PROYECTO

Organizar en carpetas:

/app
  /controllers
  /models
  /views
/config
/public
/assets

----------------------------------------

8. EXPERIENCIA DE USUARIO

- Diseño limpio y moderno
- Responsive (funcione en móvil y PC)
- Mensajes claros (login, errores, éxito)
- Transiciones suaves

----------------------------------------

9. EXTRA (SI ES POSIBLE)

- Sistema de logout seguro
- Protección de rutas (no acceder sin login)
- Uso de AJAX para actualizar dashboard sin recargar

----------------------------------------

Genera el código completo por módulos:
1. Login
2. 2FA
3. Dashboard
4. Conexión dinámica
5. Formularios dinámicos
6. KPIs con gráficas

Incluye comentarios explicativos en el código.