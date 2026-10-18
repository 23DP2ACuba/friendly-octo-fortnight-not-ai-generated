# BudgetNest

A collaborative budget management application built with Laravel and React.

## 🚀 Quick Start with Docker (Recommended)

The easiest way to run BudgetNest is using Docker:

```bash
# Clone or navigate to the project
cd BudgetNest

# Start all services (MySQL, Backend, Frontend)
docker-compose up --build

# Access the application
# Frontend: http://localhost:5173
# Backend API: http://localhost:8000/api
```

**Default Login Credentials:**
- Admin: `admin@budgetnest.com` / `admin123`
- User: `john@example.com` / `password123`

👉 **[Complete Docker Setup Guide](DOCKER_SETUP.md)**

---

## Project Structure

```
BudgetNest/
├── backend/          # Laravel 11 API
├── frontend/         # React + Vite + Tailwind CSS
├── docker-compose.yml
└── DOCKER_SETUP.md   # Docker documentation
```

## Features

- ✅ User authentication and authorization
- ✅ Budget creation and management
- ✅ Transaction tracking with filters
- ✅ Collaborative budgets with role-based access (owner/editor/viewer)
- ✅ Category management (23 pre-configured categories)
- ✅ Admin dashboard with user management
- ✅ Budget detail page with members
- ✅ Real-time budget analytics
- ✅ Invite members by email
- ✅ Responsive design

## Tech Stack

### Backend
- Laravel 11
- PHP 8.3
- MySQL 8.0
- Laravel Sanctum (API authentication)

### Frontend
- React 18
- Vite
- Tailwind CSS
- Axios
- React Router v6

### DevOps
- Docker & Docker Compose
- Multi-container setup
- Health checks
- Persistent volumes

## Getting Started

### Option 1: Docker (Recommended)

**Prerequisites:**
- Docker Desktop or Docker Engine
- Docker Compose

**Steps:**
```bash
docker-compose up --build
```

See [DOCKER_SETUP.md](DOCKER_SETUP.md) for detailed instructions.

### Option 2: Manual Setup

**Prerequisites:**
- PHP 8.3+
- Composer
- Node.js 20+
- npm
- MySQL 8.0

#### Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=budgetnest
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations and seeders
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

#### Frontend Setup

```bash
cd frontend
npm install
cp .env.example .env

# Configure API URL in .env
VITE_API_URL=http://localhost:8000/api

# Start development server
npm run dev
```

## API Endpoints

The API runs on `http://localhost:8000/api` by default.

### Public Routes
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user

### Protected Routes
- `GET /api/auth/user` - Get current user
- `POST /api/auth/logout` - Logout
- `GET /api/categories` - List categories
- `GET /api/budgets` - List user's budgets
- `POST /api/budgets` - Create budget
- `GET /api/transactions` - List transactions
- `POST /api/transactions` - Create transaction
- And more...

### Admin Routes
- `GET /api/admin/dashboard` - Admin statistics
- `GET /api/admin/users` - User management

## Docker Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Restart backend
docker-compose restart backend

# Access backend shell
docker exec -it budgetnest-backend sh

# Access MySQL
docker exec -it budgetnest-db mysql -u budgetnest_user -p
```

## Database Credentials (Docker)

- **Database:** budgetnest
- **Username:** budgetnest_user
- **Password:** budgetnest_password
- **Root Password:** root_password

## Default Users (After Seeding)

| Email | Password | Role |
|-------|----------|------|
| admin@budgetnest.com | admin123 | Admin |
| john@example.com | password123 | User |
| jane@example.com | password123 | User |

## Project Status

**Current Version:** 1.0  
**Completion:** 85%  
**Status:** ✅ Core Features Complete

### Completed
- ✅ Full authentication system
- ✅ CRUD for budgets, transactions, categories
- ✅ Collaborative budget management
- ✅ Admin dashboard
- ✅ Docker containerization
- ✅ Database seeding with sample data

### In Progress
- 🔄 Charts and data visualization
- 🔄 Email notifications
- 🔄 Profile update functionality

## Documentation

- [Docker Setup Guide](DOCKER_SETUP.md) - Complete Docker documentation
- [Implementation Report](FINAL_IMPLEMENTATION_REPORT.md) - Detailed progress report
- [Implementation Progress](IMPLEMENTATION_PROGRESS.md) - Development tracking

## Architecture

```
┌─────────────────┐
│   Browser       │
└────────┬────────┘
         │
         ├──── :5173 ──► Frontend (React/Vite)
         │
         └──── :8000 ──► Backend (Laravel API)
                              │
                         MySQL (:3306)
```

## Development

### Run Tests (Backend)
```bash
cd backend
php artisan test
```

### Build for Production (Frontend)
```bash
cd frontend
npm run build
```

### Code Quality
```bash
# Laravel
cd backend
./vendor/bin/pint

# Frontend
cd frontend
npm run lint
```

## Troubleshooting

### Docker Issues

**Containers won't start:**
```bash
docker-compose logs
```

**Reset everything:**
```bash
docker-compose down -v
docker-compose up --build
```

### Backend Issues

**Migration errors:**
```bash
docker exec -it budgetnest-backend php artisan migrate:fresh --seed
```

**Cache issues:**
```bash
docker exec -it budgetnest-backend php artisan config:clear
docker exec -it budgetnest-backend php artisan cache:clear
```

## Contributing

This is an educational project. Feel free to fork and modify for your needs.

## License

MIT

## Team

- TM_1: Frontend Development
- TM_2: Backend Development

## Last Updated

October 7, 2026
