# Laravel SaaS API con Arquitectura DDD

Una aplicación SaaS desarrollada en Laravel 12 siguiendo principios de Domain Driven Design (DDD), que proporciona una API RESTful completa para la gestión de planes de suscripción, inquilinos (tenants) y usuarios.

## 🎯 Características

- **Arquitectura DDD**: Separación clara entre dominio, aplicación e infraestructura
- **API RESTful**: Endpoints completamente documentados y probados
- **Multi-tenancy**: Soporte para múltiples inquilinos con usuarios independientes
- **Sistema de Suscripciones**: Gestión de planes y suscripciones con límites por estado
- **Autorización**:  (Laravel Sanctum) Políticas de autorización basadas en roles 
- **Validación de Negocio**: Límites por ROL 
- **Seeding**: Datos de ejemplo para desarrollo y testing
- **Historial**: En la tabla suscripciones se guardar historial de los planes

## 🏗️ Arquitectura

El proyecto sigue una arquitectura DDD organizada en las siguientes capas:

```
app/
├── Application/           # Casos de uso y lógica de aplicación
│   └── Subscriptions/
│       └── UseCases/
├── Domain/               # Entidades, repositorios e interfaces de dominio
│   └── Subscriptions/
│       └── Repositories/
├── Infrastructure/       # Implementaciones concretas e infraestructura
│   ├── Http/
│   │   └── Controllers/
│   └── Persistence/
│       └── Eloquent/
├── Http/                # Resources, Requests y middleware
│   ├── Resources/
│   └── Requests/
├── Models/              # Modelos de Laravel
├── Policies/            # Políticas de autorización
└── Providers/           # Service providers
```

## 🚀 Instalación

### Prerrequisitos
- Docker y Docker Compose
- Git

### Pasos de instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/lauraleon9/laravel-saas-ddd
cd laravel-saas-ddd
chmod 777 -R .
```

2. **Construir y levantar los contenedores**
```bash
docker compose up -d --build
```

3. **Instalar dependencias**
```bash
docker compose exec app composer install
```

4. **Configurar el entorno**
```bash
docker compose exec app cp .env.example .env
docker compose exec app php artisan key:generate
```

5. **Ejecutar migraciones y seeders**
```bash
docker compose exec app php artisan migrate --seed
```

6. **Verificar la instalación**
```bash
desde un navegador web: http://localhost:8086
```

## 🐳 Servicios Docker

| Servicio | Puerto | Descripción |
|----------|--------|-------------|
| app | - | Laravel 12 + PHP 8.4-fpm |
| web | 8086 | Nginx 1.25-alpine |
| db | 3309 | MySQL 8.0 |

## 📊 Base de Datos

### Estructura

- **plans**: Planes de suscripción disponibles
- **tenants**: Inquilinos/empresas registradas  
- **subscriptions**: Suscripciones activas/históricas
- **users**: Usuarios del sistema (globales y por tenant)

### Datos de ejemplo

Los seeders crean automáticamente:
- 4 planes de suscripción (Basic, Professional, Enterprise, Legacy)
- 5 tenants con diferentes estados
- 1 usuario administrador global
- 12 usuarios distribuidos entre los tenants activos
- 4 suscripciones activas

**Credenciales del administrador:**
- Email: `admin@laravel-saas.com`
- Contraseña: `password`

## 🔧 API Endpoints

### Planes (`/api/v1/plans`)

| Método | Endpoint | Descripción | Autorización |
|--------|----------|-------------|--------------|
| GET | `/plans` | Listar planes activos | Pública |
| POST | `/plans` | Crear nuevo plan | Admin |
| GET | `/plans/{id}` | Mostrar plan específico | Pública |
| PUT | `/plans/{id}` | Actualizar plan | Admin |
| DELETE | `/plans/{id}` | Desactivar plan | Admin |

### Tenants (`/api/v1/tenants`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/tenants` | Listar todos los tenants |
| POST | `/tenants` | Crear nuevo tenant |
| GET | `/tenants/{id}` | Mostrar tenant específico |
| PUT | `/tenants/{id}` | Actualizar tenant |
| DELETE | `/tenants/{id}` | Desactivar tenant |

### Usuarios por Tenant (`/api/v1/tenants/{tenantId}/users`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/tenants/{tenantId}/users` | Listar usuarios del tenant |
| POST | `/tenants/{tenantId}/users` | Crear usuario (valida límites) |

### Suscripciones (`/api/v1/tenants/{tenantId}/subscriptions`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/tenants/{tenantId}/subscriptions/change-plan` | Cambiar plan de suscripción |

## 📝 Ejemplos de Uso

### Obtener todos los planes
Impotar el Archivo en POSTMAN: postman/laravel-saas-api.postman_collection.json


### Roles de usuario
- `admin`: Administrador global del sistema
- `user`: Usuario regular de un tenant

## 🧪 Testing

### Ejecutar todos los tests
```bash
docker compose exec app php artisan test
```

### Ejecutar tests específicos
```bash
docker compose exec app php artisan test --filter PlansTest
```

### Tests incluidos
- ✅ CRUD completo de planes con validaciones
- ✅ Respuestas de API correctas
- ✅ Manejo de errores y validaciones



## 👨‍💻 Autor

Laura Milena Leon Mendez


