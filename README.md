# Laravel SaaS API con Arquitectura DDD

Una aplicación SaaS desarrollada en Laravel 12 siguiendo principios de Domain Driven Design (DDD), que proporciona una API RESTful completa para la gestión de planes de suscripción, inquilinos (tenants) y usuarios.

## 🎯 Características

- **Arquitectura DDD**: Separación clara entre dominio, aplicación e infraestructura
- **API RESTful**: Endpoints completamente documentados y probados
- **Multi-tenancy**: Soporte para múltiples inquilinos con usuarios independientes
- **Sistema de Suscripciones**: Gestión de planes y suscripciones con límites de usuarios
- **Autorización**:  (Laravel Sanctum) Políticas de autorización basadas en roles 
- **Validación de Negocio**: Límites de usuarios por solo un plan activo 
- **Seeding**: Datos de ejemplo para desarrollo y testing
- **Historial**: En la tabla subscript se guardar historial de los planes

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
git clone <repository-url>
cd laravel-saas-ddd
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
curl http://localhost:8086/api/v1/plans
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
| GET | `/tenants/{tenantId}/users/{userId}` | Mostrar usuario específico |
| PUT | `/tenants/{tenantId}/users/{userId}` | Actualizar usuario |
| DELETE | `/tenants/{tenantId}/users/{userId}` | Desactivar usuario |

### Suscripciones (`/api/v1/tenants/{tenantId}/subscriptions`)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/tenants/{tenantId}/subscriptions/change-plan` | Cambiar plan de suscripción |

## 📝 Ejemplos de Uso

### Obtener todos los planes
```bash
curl -X GET http://localhost:8086/api/v1/plans \
  -H "Accept: application/json"
```

### Crear un nuevo tenant
```bash
curl -X POST http://localhost:8086/api/v1/tenants \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Mi Empresa",
    "slug": "mi-empresa"
  }'
```

### Crear usuario en un tenant
```bash
curl -X POST http://localhost:8086/api/v1/tenants/1/users \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Juan Pérez",
    "email": "juan@miempresa.com",
    "password": "password123",
    "role": "user"
  }'
```

### Cambiar plan de suscripción
```bash
curl -X POST http://localhost:8086/api/v1/tenants/1/subscriptions/change-plan \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "plan_id": 2
  }'
```

## 🔒 Autorización

El sistema implementa políticas de autorización basadas en roles:

- **Administradores globales**: Pueden gestionar planes y tienen acceso completo
- **Usuarios regulares**: Acceso limitado según su tenant
- **Operaciones públicas**: Consulta de planes disponibles

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
- ✅ Validación de límites de usuarios por plan
- ✅ Respuestas de API correctas
- ✅ Manejo de errores y validaciones

## 🔄 Validaciones de Negocio

### Límites de Usuarios por Plan
- El sistema valida automáticamente que no se excedan los límites de usuarios por plan
- Al crear un nuevo usuario, se verifica contra la suscripción activa del tenant
- Respuesta HTTP 422 si se intenta exceder el límite

### Suscripciones Únicas
- Solo una suscripción activa por tenant (implementado con triggers MySQL)
- Al cambiar de plan, se cierra automáticamente la suscripción anterior
- Se mantiene historial completo de suscripciones

## 🛠️ Comandos Útiles

### Artisan Commands
```bash
# Limpiar cache
docker compose exec app php artisan cache:clear

# Ver rutas
docker compose exec app php artisan route:list

# Tinker (REPL)
docker compose exec app php artisan tinker

# Rollback y re-seed
docker compose exec app php artisan migrate:fresh --seed
```

### Logs
```bash
# Ver logs de Laravel
docker compose exec app tail -f storage/logs/laravel.log

# Ver logs de Nginx
docker compose logs web

# Ver logs de MySQL
docker compose logs db
```

## 🚧 Funcionalidades Implementadas

- ✅ CRUD completo para Planes
- ✅ CRUD completo para Tenants
- ✅ CRUD completo para Usuarios con validación de límites
- ✅ Sistema de suscripciones con cambio de planes
- ✅ Políticas de autorización basadas en roles
- ✅ Validaciones de negocio automáticas
- ✅ Seeders con datos de ejemplo
- ✅ Tests automatizados
- ✅ Documentación API completa
- ✅ Arquitectura DDD
- ✅ Dockerización completa



## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request


## 👨‍💻 Autor

Laura Milena Leon Mendez

---

🚀 **¿Listo para probar la API?** Inicia con `docker compose up -d` y explora los endpoints disponibles!
