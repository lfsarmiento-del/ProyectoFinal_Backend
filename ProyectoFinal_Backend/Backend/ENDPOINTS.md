---

# Información técnica del proyecto

## Nombre del proyecto

Sistema Web de Reservas y Pedidos para Restaurante.

## Descripción técnica

El proyecto corresponde a una aplicación web distribuida orientada a la administración operativa de un restaurante. La solución permite gestionar usuarios, mesas, reservas, productos del menú, pedidos y detalles de pedidos mediante una arquitectura basada en microservicios.

Cada microservicio se encarga de una responsabilidad específica del sistema y expone endpoints REST que son consumidos por el frontend mediante peticiones HTTP usando `fetch`.

El sistema está dividido en dos repositorios principales:

```txt
Repositorio Backend
Repositorio Frontend
```

El backend contiene los microservicios desarrollados en PHP con Slim Framework y Eloquent ORM.
El frontend contiene la interfaz de usuario desarrollada con HTML5, CSS3 y JavaScript Vanilla.

---

# Arquitectura general

La arquitectura del proyecto está organizada bajo el principio de separación de responsabilidades. Cada microservicio tiene su propia lógica, sus propios modelos, controladores, rutas, configuración y conexión a base de datos.

La comunicación del sistema se realiza de la siguiente manera:

```txt
Frontend → ms-auth
Frontend → ms-reservas
Frontend → ms-productos
Frontend → ms-pedidos
```

No se implementa comunicación directa entre microservicios.
No se utiliza API Gateway.
No se utiliza mensajería, colas ni eventos.
Cada servicio es consumido directamente desde el frontend.

---

# Microservicios implementados

## ms-auth

Responsable de la autenticación de usuarios.

Funciones principales:

```txt
Inicio de sesión
Generación de token simple
Validación de sesión activa
Cierre de sesión
Control básico de acceso
```

Base de datos asociada:

```txt
ms_auth
```

Tabla principal:

```txt
usuarios
```

---

## ms-reservas

Responsable de la administración de mesas y reservas.

Funciones principales:

```txt
Crear mesas
Listar mesas
Editar mesas
Cambiar estado de mesas
Eliminar mesas
Crear reservas
Listar reservas
Editar reservas
Cancelar reservas
Cambiar estado de reservas
Validar disponibilidad de mesas
```

Base de datos asociada:

```txt
ms_reservas
```

Tablas principales:

```txt
mesas
reservas
```

---

## ms-productos

Responsable de la administración del menú del restaurante.

Funciones principales:

```txt
Crear categorías
Listar categorías
Editar categorías
Eliminar categorías
Crear productos
Listar productos
Filtrar productos por categoría
Consultar productos disponibles
Editar productos
Eliminar productos
```

Base de datos asociada:

```txt
ms_productos
```

Tablas principales:

```txt
categorias
productos
```

---

## ms-pedidos

Responsable de la gestión de pedidos realizados por las mesas.

Funciones principales:

```txt
Crear pedidos
Agregar productos al pedido
Listar pedidos
Consultar detalle del pedido
Editar datos generales del pedido
Cambiar estado del pedido
Eliminar pedidos
Agregar detalles
Editar detalles
Eliminar detalles
Recalcular subtotal y total
```

Base de datos asociada:

```txt
ms_pedidos
```

Tablas principales:

```txt
pedidos
detalles_pedidos
```

---

# Tecnologías utilizadas

## Backend

```txt
PHP 8+
Slim Framework
Eloquent ORM
Composer
MySQL
JSON
API REST
```

## Frontend

```txt
HTML5
CSS3
JavaScript Vanilla
Fetch API
LocalStorage
```

## Herramientas de apoyo

```txt
XAMPP
phpMyAdmin
Git
GitHub
Visual Studio Code
Composer
```

---

# Estructura del backend

```txt
Backend
├── ms-auth
│   ├── app
│   │   ├── Config
│   │   ├── Controllers
│   │   ├── Helpers
│   │   ├── Middleware
│   │   ├── Models
│   │   └── Routes
│   ├── public
│   │   └── index.php
│   ├── composer.json
│   ├── .env.example
│   └── .env
│
├── ms-reservas
│   ├── app
│   │   ├── Config
│   │   ├── Controllers
│   │   ├── Helpers
│   │   ├── Middleware
│   │   ├── Models
│   │   └── Routes
│   ├── public
│   │   └── index.php
│   ├── composer.json
│   ├── .env.example
│   └── .env
│
├── ms-productos
│   ├── app
│   │   ├── Config
│   │   ├── Controllers
│   │   ├── Helpers
│   │   ├── Middleware
│   │   ├── Models
│   │   └── Routes
│   ├── public
│   │   └── index.php
│   ├── composer.json
│   ├── .env.example
│   └── .env
│
├── ms-pedidos
│   ├── app
│   │   ├── Config
│   │   ├── Controllers
│   │   ├── Helpers
│   │   ├── Middleware
│   │   ├── Models
│   │   └── Routes
│   ├── public
│   │   └── index.php
│   ├── composer.json
│   ├── .env.example
│   └── .env
│
├── database
│   └── base_datos_restaurante.sql
│
├── .gitignore
└── ENDPOINTS.md
```

---

# Estructura del frontend

```txt
Frontend
├── index.html
├── login.html
├── pages
│   ├── dashboard.html
│   ├── mesas.html
│   ├── reservas.html
│   ├── productos.html
│   └── pedidos.html
│
├── css
│   └── styles.css
│
└── js
    ├── config.js
    ├── auth.js
    ├── main.js
    ├── dashboard.js
    ├── mesas.js
    ├── reservas.js
    ├── productos.js
    └── pedidos.js
```

---

# Archivo de base de datos

Para facilitar la instalación del proyecto en otros computadores, se agregó una carpeta llamada `database` en el repositorio backend.

Dentro de esta carpeta se encuentra el archivo:

```txt
database/base_datos_restaurante.sql
```

Este archivo contiene la estructura completa de las bases de datos necesarias para el funcionamiento del sistema.

El archivo SQL permite crear automáticamente:

```txt
ms_auth
ms_reservas
ms_productos
ms_pedidos
```

También crea las tablas principales e inserta datos iniciales de prueba.

---

# Importación de la base de datos

Para importar la base de datos en un computador externo se debe realizar el siguiente procedimiento:

1. Abrir XAMPP.
2. Iniciar Apache.
3. Iniciar MySQL.
4. Abrir phpMyAdmin desde el navegador.

```txt
http://localhost/phpmyadmin
```

5. Entrar a la pestaña **Importar**.
6. Seleccionar el archivo:

```txt
database/base_datos_restaurante.sql
```

7. Presionar **Continuar**.
8. Verificar que se hayan creado las bases de datos.

Bases esperadas:

```txt
ms_auth
ms_reservas
ms_productos
ms_pedidos
```

---

# Variables de entorno

Cada microservicio utiliza un archivo `.env` para definir la conexión con MySQL.

El archivo `.env` no se sube al repositorio porque contiene configuración local del computador.

Para facilitar la instalación, se agregó un archivo `.env.example` en cada microservicio.

El archivo `.env.example` sirve como plantilla para crear el `.env`.

---

## Creación rápida de archivos .env

Desde la raíz del backend se pueden ejecutar los siguientes comandos:

```cmd
copy ms-auth\.env.example ms-auth\.env
copy ms-reservas\.env.example ms-reservas\.env
copy ms-productos\.env.example ms-productos\.env
copy ms-pedidos\.env.example ms-pedidos\.env
```

Después de ejecutar estos comandos, cada microservicio queda con su archivo `.env` correspondiente.

---

## Configuración esperada de ms-auth

```env
DB_HOST=localhost
DB_NAME=ms_auth
DB_USER=root
DB_PASS=
DB_PORT=3306
```

## Configuración esperada de ms-reservas

```env
DB_HOST=localhost
DB_NAME=ms_reservas
DB_USER=root
DB_PASS=
DB_PORT=3306
```

## Configuración esperada de ms-productos

```env
DB_HOST=localhost
DB_NAME=ms_productos
DB_USER=root
DB_PASS=
DB_PORT=3306
```

## Configuración esperada de ms-pedidos

```env
DB_HOST=localhost
DB_NAME=ms_pedidos
DB_USER=root
DB_PASS=
DB_PORT=3306
```

---

# Instalación de dependencias

La carpeta `vendor` no se incluye en el repositorio.
Por esta razón, después de clonar el proyecto, se deben instalar las dependencias con Composer.

Desde cada microservicio se ejecuta:

```cmd
composer install
```

También se puede realizar desde la raíz del backend con los siguientes comandos:

```cmd
cd ms-auth && composer install && cd ..
cd ms-reservas && composer install && cd ..
cd ms-productos && composer install && cd ..
cd ms-pedidos && composer install && cd ..
```

---

# Ejecución de microservicios

Cada microservicio se ejecuta con el servidor embebido de PHP en un puerto diferente.

## ms-auth

```cmd
cd ms-auth
php -S 127.0.0.1:8001 -t public
```

## ms-reservas

```cmd
cd ms-reservas
php -S 127.0.0.1:8002 -t public
```

## ms-productos

```cmd
cd ms-productos
php -S 127.0.0.1:8003 -t public
```

## ms-pedidos

```cmd
cd ms-pedidos
php -S 127.0.0.1:8004 -t public
```

---

# Ejecución del frontend

Desde la carpeta del frontend:

```cmd
php -S 127.0.0.1:5500
```

Luego se abre el navegador en:

```txt
http://127.0.0.1:5500/index.html
```

---

# Puertos utilizados

```txt
ms-auth       → 8001
ms-reservas   → 8002
ms-productos  → 8003
ms-pedidos    → 8004
frontend      → 5500
```

---

# Flujo general de funcionamiento

El usuario accede al sistema desde el frontend.

El frontend muestra la pantalla de login.

El usuario ingresa sus credenciales.

El frontend envía una petición al microservicio `ms-auth`.

Si las credenciales son correctas, el backend genera un token simple.

El token se almacena en `localStorage`.

El usuario accede al dashboard principal.

Desde el dashboard puede ingresar a los módulos de mesas, reservas, productos y pedidos.

Cada módulo consume su respectivo microservicio mediante `fetch`.

Las respuestas del backend se muestran en la interfaz en formato de tablas, formularios y mensajes.

---

# Funcionalidades implementadas en frontend

## Autenticación

```txt
Formulario de login
Validación de campos vacíos
Consumo del endpoint POST /login
Almacenamiento de token en localStorage
Almacenamiento de información del usuario
Redirección al dashboard
Cierre de sesión
Eliminación del token
Protección de páginas internas
```

## Dashboard

```txt
Resumen general del sistema
Total de mesas
Total de reservas
Total de productos
Total de pedidos
Consumo de varios microservicios desde una sola vista
```

## Mesas

```txt
Listado de mesas
Registro de mesas
Eliminación de mesas
Visualización de estado
Visualización de capacidad
Consumo del microservicio ms-reservas
```

## Reservas

```txt
Listado de reservas
Registro de reservas
Edición de reservas
Cancelación de reservas
Selección de mesa
Control de estado de reserva
Mensajes de éxito y error
```

## Productos

```txt
Listado de productos
Registro de productos
Edición de productos
Eliminación de productos
Selección de categoría
Control de disponibilidad
Mensajes de éxito y error
```

## Pedidos

```txt
Listado de pedidos
Registro de pedidos
Agregado de productos al pedido
Cálculo de subtotal
Cálculo de total
Edición de datos generales del pedido
Eliminación de pedidos
Control de estado del pedido
Mensajes de éxito y error
```

---

# Funcionalidades adicionales agregadas

Durante el desarrollo se añadieron mejoras adicionales al frontend para que el sistema tenga mayor funcionalidad desde la interfaz.

Se agregaron botones de edición en:

```txt
Reservas
Productos
Pedidos
```

También se agregó un botón para cancelar la edición y regresar el formulario a modo de registro.

Los módulos actualizados fueron:

```txt
js/reservas.js
js/productos.js
js/pedidos.js
css/styles.css
```

---

# Edición de productos desde frontend

El módulo de productos permite editar la información de un producto existente.

Campos editables:

```txt
Nombre
Descripción
Precio
Categoría
Disponibilidad
```

Endpoint utilizado:

```txt
PUT /productos/{id}
```

El formulario cambia el botón de registro por el botón de actualización cuando se selecciona un producto para editar.

---

# Edición de reservas desde frontend

El módulo de reservas permite editar los datos principales de una reserva.

Campos editables:

```txt
Nombre del cliente
Teléfono
Cantidad de personas
Fecha
Hora
Mesa asignada
Estado
Observaciones
```

Endpoint utilizado:

```txt
PUT /reservas/{id}
```

También se conserva la opción de cancelar reserva mediante:

```txt
PATCH /reservas/{id}/cancelar
```

---

# Edición de pedidos desde frontend

El módulo de pedidos permite editar los datos generales del pedido.

Campos editables:

```txt
Mesa
Fecha
Hora
Estado
```

Endpoint utilizado:

```txt
PUT /pedidos/{id}
```

En modo edición, el formulario bloquea la adición de nuevos productos para evitar inconsistencias en los detalles del pedido.

---

# Estilos y diseño

El archivo `css/styles.css` contiene el diseño general del sistema.

Se implementaron estilos para:

```txt
Login
Header
Navegación
Dashboard
Tarjetas resumen
Formularios
Tablas
Botones de acción
Botones de edición
Botones de eliminación
Mensajes del sistema
Diseño responsive
```

El diseño utiliza colores sobrios, sombras, bordes redondeados y distribución adaptable a pantallas pequeñas.

---

# Validaciones principales

## Validaciones en autenticación

```txt
Usuario requerido
Contraseña requerida
Sesión activa
Token válido
Usuario activo
```

## Validaciones en mesas

```txt
Número de mesa requerido
Capacidad mayor a cero
Estado válido
No permitir mesas duplicadas
```

## Validaciones en reservas

```txt
Nombre del cliente requerido
Teléfono requerido
Cantidad de personas mayor a cero
Fecha requerida
Hora requerida
Mesa existente
Capacidad suficiente
No reservar mesas fuera de servicio
No duplicar reserva en la misma mesa y horario
```

## Validaciones en productos

```txt
Nombre requerido
Precio mayor a cero
Categoría existente
Disponibilidad válida
No permitir productos duplicados
```

## Validaciones en pedidos

```txt
Mesa requerida
Fecha requerida
Hora requerida
Estado válido
Pedido con al menos un producto
Cantidad mayor a cero
Cálculo automático de subtotal
Cálculo automático de total
```

---

# Archivos que sí se suben al repositorio

```txt
Código fuente de app/
Código fuente de public/
composer.json
.env.example
ENDPOINTS.md
database/base_datos_restaurante.sql
.gitignore
```

---

# Archivos que no se suben al repositorio

```txt
.env
vendor
composer.lock
```

Estos archivos están excluidos mediante `.gitignore`.

---

# Verificación rápida

Para comprobar el backend:

```txt
http://127.0.0.1:8001/health
http://127.0.0.1:8002/health
http://127.0.0.1:8003/health
http://127.0.0.1:8004/health
```

Para comprobar el frontend:

```txt
http://127.0.0.1:5500/index.html
```

Credenciales de prueba:

```txt
admin / admin123
empleado / empleado123
```

---

# Orden para presentar y ejecutar el proyecto

```txt
1. Abrir XAMPP como administrador.
2. Iniciar Apache y MySQL.
3. Importar database/base_datos_restaurante.sql.
4. Crear archivos .env desde .env.example.
5. Ejecutar composer install en cada microservicio.
6. Iniciar ms-auth en puerto 8001.
7. Iniciar ms-reservas en puerto 8002.
8. Iniciar ms-productos en puerto 8003.
9. Iniciar ms-pedidos en puerto 8004.
10. Iniciar frontend en puerto 5500.
11. Abrir http://127.0.0.1:5500/index.html.
12. Iniciar sesión con admin / admin123.
```

