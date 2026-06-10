# Documentación de Endpoints - Backend Restaurante XYZ

## Descripción general

Este documento contiene los endpoints del backend del sistema web de reservas y pedidos para restaurante.

El backend está dividido en 4 microservicios:

* `ms-auth`: autenticación de usuarios.
* `ms-reservas`: gestión de mesas y reservas.
* `ms-productos`: gestión de categorías y productos.
* `ms-pedidos`: gestión de pedidos y detalles.

Cada microservicio responde en formato JSON y se ejecuta con el servidor embebido de PHP.

---

# 1. Microservicio ms-auth

## URL base

```txt
http://127.0.0.1:8001
```

## GET /health

Verifica que el microservicio esté funcionando.

## POST /login

Permite iniciar sesión con usuario o correo y contraseña.

Body:

```json
{
  "usuario": "admin",
  "contrasena": "admin123"
}
```

Respuesta esperada:

```json
{
  "status": true,
  "message": "Inicio de sesión correcto.",
  "data": {
    "token": "TOKEN_GENERADO",
    "usuario": {
      "id": 1,
      "nombre": "Administrador General",
      "correo": "admin@restaurante.com",
      "rol": "administrador"
    }
  }
}
```

## POST /logout

Cierra la sesión del usuario.

Headers:

```txt
Authorization: Bearer TOKEN_GENERADO
```

## GET /validar-token

Valida si el token enviado existe y la sesión está activa.

Headers:

```txt
Authorization: Bearer TOKEN_GENERADO
```

---

# 2. Microservicio ms-reservas

## URL base

```txt
http://127.0.0.1:8002
```

## GET /health

Verifica que el microservicio esté funcionando.

---

## Mesas

## GET /mesas

Lista todas las mesas.

## GET /mesas/{id}

Consulta una mesa por ID.

Ejemplo:

```txt
GET /mesas/1
```

## POST /mesas

Crea una nueva mesa.

Body:

```json
{
  "numero": "MESA-5",
  "capacidad": 4,
  "estado": "disponible"
}
```

Estados permitidos:

```txt
disponible
reservada
ocupada
fuera_servicio
```

## PUT /mesas/{id}

Actualiza una mesa.

Body:

```json
{
  "numero": "MESA-5",
  "capacidad": 6,
  "estado": "ocupada"
}
```

## PATCH /mesas/{id}/estado

Cambia el estado de una mesa.

Body:

```json
{
  "estado": "reservada"
}
```

## DELETE /mesas/{id}

Elimina una mesa.

---

## Reservas

## GET /reservas

Lista todas las reservas.

Filtros permitidos:

```txt
GET /reservas?fecha=2026-06-10
GET /reservas?cliente=Carlos
GET /reservas?estado=confirmada
```

## GET /reservas/{id}

Consulta una reserva por ID.

Ejemplo:

```txt
GET /reservas/1
```

## POST /reservas

Crea una nueva reserva.

Body:

```json
{
  "nombre_cliente": "Carlos Ramirez",
  "telefono_cliente": "3001234567",
  "cantidad_personas": 4,
  "fecha": "2026-06-10",
  "hora": "19:00:00",
  "observaciones": "Reserva familiar",
  "estado": "confirmada",
  "mesa_id": 2
}
```

Estados permitidos:

```txt
pendiente
confirmada
cancelada
finalizada
```

## PUT /reservas/{id}

Actualiza una reserva.

Body:

```json
{
  "nombre_cliente": "Carlos Ramirez",
  "telefono_cliente": "3001234567",
  "cantidad_personas": 5,
  "fecha": "2026-06-11",
  "hora": "20:00:00",
  "observaciones": "Cambio de horario",
  "estado": "confirmada",
  "mesa_id": 2
}
```

## PATCH /reservas/{id}/cancelar

Cancela una reserva.

## PATCH /reservas/{id}/estado

Cambia el estado de una reserva.

Body:

```json
{
  "estado": "finalizada"
}
```

---

# 3. Microservicio ms-productos

## URL base

```txt
http://127.0.0.1:8003
```

## GET /health

Verifica que el microservicio esté funcionando.

---

## Categorías

## GET /categorias

Lista todas las categorías.

## GET /categorias/{id}

Consulta una categoría por ID.

Ejemplo:

```txt
GET /categorias/1
```

## POST /categorias

Crea una categoría.

Body:

```json
{
  "nombre": "Sopas",
  "descripcion": "Sopas y cremas"
}
```

## PUT /categorias/{id}

Actualiza una categoría.

Body:

```json
{
  "nombre": "Bebidas",
  "descripcion": "Bebidas frías y calientes"
}
```

## DELETE /categorias/{id}

Elimina una categoría.

---

## Productos

## GET /productos

Lista todos los productos.

Filtros permitidos:

```txt
GET /productos?categoria_id=2
GET /productos?disponible=1
```

## GET /productos/disponibles

Lista productos disponibles.

## GET /productos/categoria/{categoria_id}

Lista productos por categoría.

Ejemplo:

```txt
GET /productos/categoria/2
```

## GET /productos/{id}

Consulta un producto por ID.

Ejemplo:

```txt
GET /productos/1
```

## POST /productos

Crea un producto.

Body:

```json
{
  "nombre": "Pizza Personal",
  "descripcion": "Pizza personal de queso",
  "precio": 18000,
  "disponible": true,
  "categoria_id": 3
}
```

## PUT /productos/{id}

Actualiza un producto.

Body:

```json
{
  "nombre": "Pizza Personal",
  "descripcion": "Pizza personal de queso y jamón",
  "precio": 20000,
  "disponible": true,
  "categoria_id": 3
}
```

## DELETE /productos/{id}

Elimina un producto.

---

# 4. Microservicio ms-pedidos

## URL base

```txt
http://127.0.0.1:8004
```

## GET /health

Verifica que el microservicio esté funcionando.

## GET /pedidos

Lista todos los pedidos.

Filtros permitidos:

```txt
GET /pedidos?estado=pendiente
GET /pedidos?mesa_id=1
GET /pedidos?fecha=2026-06-10
```

## GET /pedidos/{id}

Consulta un pedido por ID.

Ejemplo:

```txt
GET /pedidos/1
```

## POST /pedidos

Crea un pedido con productos.

Body:

```json
{
  "mesa_id": 1,
  "fecha": "2026-06-10",
  "hora": "20:00:00",
  "estado": "pendiente",
  "productos": [
    {
      "producto_id": 1,
      "nombre_producto": "Hamburguesa Especial",
      "cantidad": 1,
      "precio_unitario": 28000
    },
    {
      "producto_id": 2,
      "nombre_producto": "Limonada Natural",
      "cantidad": 1,
      "precio_unitario": 8000
    }
  ]
}
```

El sistema calcula automáticamente:

* Subtotal.
* Total.
* Cantidad total de productos.

## PUT /pedidos/{id}

Actualiza los datos generales del pedido.

Body:

```json
{
  "mesa_id": 1,
  "fecha": "2026-06-10",
  "hora": "21:00:00",
  "estado": "en_preparacion"
}
```

## PATCH /pedidos/{id}/estado

Cambia el estado del pedido.

Body:

```json
{
  "estado": "entregado"
}
```

Estados permitidos:

```txt
pendiente
en_preparacion
entregado
pagado
cancelado
```

## DELETE /pedidos/{id}

Elimina un pedido y sus detalles.

---

## Detalles del pedido

## POST /pedidos/{id}/detalles

Agrega un producto a un pedido existente.

Body:

```json
{
  "producto_id": 3,
  "nombre_producto": "Cheesecake",
  "cantidad": 2,
  "precio_unitario": 12000
}
```

## PUT /pedidos/{id}/detalles/{detalle_id}

Actualiza un producto dentro del pedido.

Body:

```json
{
  "producto_id": 3,
  "nombre_producto": "Cheesecake",
  "cantidad": 3,
  "precio_unitario": 12000
}
```

## DELETE /pedidos/{id}/detalles/{detalle_id}

Elimina un producto del pedido y recalcula el total.

---

# Puertos de ejecución

Cada microservicio se ejecuta desde su carpeta correspondiente.

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

# Usuarios de prueba

## Administrador

```txt
Usuario: admin
Contraseña: admin123
```

## Empleado

```txt
Usuario: empleado
Contraseña: empleado123
```

---

# Formato general de respuestas

## Respuesta exitosa

```json
{
  "status": true,
  "message": "Operación realizada correctamente.",
  "data": {}
}
```

## Respuesta de error

```json
{
  "status": false,
  "message": "Mensaje del error."
}
```
