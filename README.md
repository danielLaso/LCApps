# Symfony Catalog & Orders API

Este proyecto es una solución al test técnico Full Stack que implementa una API REST para el manejo de catálogo de productos y órdenes.

## 📋 Descripción

La aplicación está desarrollada utilizando Symfony 6.4 y sigue los principios básicos de Domain-Driven Design (DDD). La arquitectura está dividida en dos contextos principales:

- **📚 Catalog**: gestión de productos disponibles para pedidos.
- **🛒 Orders**: creación y confirmación de pedidos, validando stock.


## 🛠️ Requisitos Previos

- Docker y Docker Compose instalados
- Git

## 🚀 Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/danielLaso/LCApps.git
cd LCApps
```

2. Iniciar los contenedores Docker:
```bash
cd docker
docker-compose up --build
```

3. Instalar dependencias
```bash
cd LCApp
composer install
```
## 📬 Colección de Endpoints

Puedes probar todos los endpoints de la API desde el archivo `requests.http` incluido en el repositorio.

Este archivo contiene ejemplos listos para usar de:

- Crear y listar productos
- Actualizar stock
- Crear y listar pedidos
- Confirmar pedidos

## 🧪 Pruebas

Para ejecutar las pruebas unitarias:

```bash
cd LCApp
./bin/phpunit
```

## 👥 Autor

Desarrollado por Daniel Laso