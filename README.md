
# 📚 Prueba Técnica - Full Stack

## 📦 Descripción

Proyecto desarrollado como solución a la prueba técnica Full Stack.

El sistema implementa:

- API REST desarrollada con **Symfony 6.4**
- Principios básicos de **Domain-Driven Design (DDD)**
- Separación de contextos:
    - `Catalog`: gestión de productos
    - `Orders`: gestión de pedidos
- Validación de stock en la confirmación de pedidos
- Tests unitarios
- Contenedores Docker (PHP-FPM + Nginx + MySQL)

---

## 🚀 Instrucciones para levantar el proyecto

### Requisitos:

- Docker
- Docker Compose

### Pasos para ejecutar el proyecto:

```bash
# Clonar el repositorio
git clone https://github.com/danielLaso/LCApps.git  # (Si vas a entregar ZIP, puedes borrar esta línea)
cd LCApps/docker

# Levantar los contenedores
docker-compose up --build

# Acceder a la aplicación en:
http://localhost:8080
```

## 🧪 Cómo ejecutar los tests

```bash
# Acceder a la carpeta del proyecto donde se encuentra Symfony LCApp
# Entrar al contenedor PHP
docker-compose exec php-fpm bash

# Ejecutar PHPUnit
php bin/phpunit
```

---

## 🏗️ Decisiones técnicas y de diseño

### Arquitectura:

- Se ha seguido un enfoque DDD básico:
    - Contextos separados `Catalog` y `Orders`
    - Uso de Value Object `Money` para el precio de los productos
    - Agregado `Order` con entidad `OrderLine`

### Repositorios:

- Se han implementado `DoctrineProductRepository` y `DoctrineOrderRepository` para encapsular la persistencia.

### Validación de stock:

- La confirmación de pedido valida el stock de cada producto antes de confirmar el pedido.
- Esta lógica está testeada mediante tests unitarios.

### CQRS:

- Se ha optado por **no implementar CQRS completo** en esta versión para evitar sobreingeniería y mantener el proyecto simple y claro.
- La estructura actual permite añadirlo fácilmente en el futuro.

### Tests:

- Se han implementado tests unitarios sobre:
    - Confirmación de pedido (obligatorio en la prueba)
    - Entidad `OrderLine`
    - Entidad `Product`
    - Value Object `Money` (opcional, recomendado)
- Los tests aseguran el correcto comportamiento de las reglas de negocio clave.

---

## 🌐 Ficheros HTTP de ejemplo

Se adjunta el fichero [`requests.http`](requests.http) con ejemplos de uso de la API:

- `POST /products` → Crear producto
- `GET /products` → Listar productos
- `PATCH /products/{id}/stock` → Modificar stock de producto

- `POST /orders` → Crear pedido
- `GET /orders` → Listar pedidos
- `PATCH /orders/{id}/confirm` → Confirmar pedido

Estos ejemplos pueden ejecutarse directamente desde **Visual Studio Code** (con la extensión REST Client) o importarse en **Postman**.

---

## 📋 Estructura de carpetas

```plaintext
src/
├── Catalog/
│   ├── Application/
│   ├── Domain/
│   ├── Infrastructure/
│   └── Presentation/
└── Orders/
    ├── Application/
    ├── Domain/
    ├── Infrastructure/
    └── Presentation/

tests/
├── Catalog/
│   └── Domain/
└── Orders/
    └── Domain/
```

---

## 👨‍💻 Autor

Daniel Laso  
Junio 2025

---