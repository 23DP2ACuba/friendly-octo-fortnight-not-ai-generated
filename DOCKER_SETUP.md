# Docker Setup Guide - BudgetNest

This guide will help you run the BudgetNest application using Docker containers.

## Prerequisites

- Docker Desktop installed (Windows/Mac) or Docker Engine (Linux)
- Docker Compose installed
- At least 4GB of free RAM
- Ports 3306, 8000, and 5173 available

## Quick Start

### 1. Clone or Navigate to Project
```bash
cd BudgetNest
```

### 2. Build and Start All Containers
```bash
docker-compose up --build
```

This single command will:
- Build the backend (Laravel) container
- Build the frontend (React) container
- Pull MySQL 8.0 image
- Create network and volumes
- Start all services
- Run database migrations
- Seed the database with sample data

### 3. Access the Application

Once all containers are running, access:

- **Frontend:** http://localhost:5173
- **Backend API:** http://localhost:8000/api
- **Database:** localhost:3306

## Container Details

### Services

#### 1. MySQL Database (`budgetnest-db`)
- **Image:** mysql:8.0
- **Port:** 3306
- **Database:** budgetnest
- **Username:** budgetnest_user
- **Password:** budgetnest_password
- **Root Password:** root_password

#### 2. Laravel Backend (`budgetnest-backend`)
- **Port:** 8000
- **Built from:** ./backend/Dockerfile
- **PHP Version:** 8.3
- **Auto-runs:** migrations and seeders

#### 3. React Frontend (`budgetnest-frontend`)
- **Port:** 5173
- **Built from:** ./frontend/Dockerfile
- **Node Version:** 20
- **Serves:** Production build

## Docker Commands

### Start Containers (Detached Mode)
```bash
docker-compose up -d
```

### Stop Containers
```bash
docker-compose down
```

### Stop and Remove All Data (Including Database)
```bash
docker-compose down -v
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f db
```

### Rebuild Containers
```bash
docker-compose up --build
```

### Restart a Specific Service
```bash
docker-compose restart backend
```

### Check Running Containers
```bash
docker-compose ps
```

### Access Container Shell
```bash
# Backend
docker exec -it budgetnest-backend sh

# Database
docker exec -it budgetnest-db mysql -u budgetnest_user -p budgetnest

# Frontend
docker exec -it budgetnest-frontend sh
```

## Database Management

### Access MySQL CLI
```bash
docker exec -it budgetnest-db mysql -u budgetnest_user -p
# Password: budgetnest_password
```

### Run Migrations Manually
```bash
docker exec -it budgetnest-backend php artisan migrate
```

### Run Seeders Manually
```bash
docker exec -it budgetnest-backend php artisan db:seed
```

### Reset Database
```bash
docker exec -it budgetnest-backend php artisan migrate:fresh --seed
```

## Default Login Credentials

After seeding, you can login with:

**Admin Account:**
- Email: admin@budgetnest.com
- Password: admin123

**Regular Users:**
- Email: john@example.com
- Password: password123

**OR**

- Email: jane@example.com
- Password: password123

## Environment Variables

### Backend (.env in container)
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=budgetnest
DB_USERNAME=budgetnest_user
DB_PASSWORD=budgetnest_password
FRONTEND_URL=http://localhost:5173
```

### Frontend
```env
VITE_API_URL=http://localhost:8000/api
```

## Troubleshooting

### Container Won't Start

**Check logs:**
```bash
docker-compose logs backend
```

**Common issues:**
1. Port already in use
2. Docker daemon not running
3. Insufficient disk space

### Database Connection Failed

**Wait for MySQL to be ready:**
```bash
docker-compose logs db
```

Look for: "ready for connections"

**Test connection:**
```bash
docker exec -it budgetnest-db mysqladmin ping -h localhost
```

### Backend API Not Responding

**Check if migrations ran:**
```bash
docker exec -it budgetnest-backend php artisan migrate:status
```

**Run migrations manually:**
```bash
docker exec -it budgetnest-backend php artisan migrate --force
```

### Frontend Not Loading

**Check if build succeeded:**
```bash
docker-compose logs frontend
```

**Rebuild frontend:**
```bash
docker-compose up --build frontend
```

### Port Conflicts

If ports 3306, 8000, or 5173 are in use, modify `docker-compose.yml`:

```yaml
ports:
  - "3307:3306"  # Changed MySQL port
  - "8001:8000"  # Changed backend port
  - "5174:5173"  # Changed frontend port
```

### Clear All Docker Data

**Remove all containers and volumes:**
```bash
docker-compose down -v
docker system prune -a --volumes
```

**Then rebuild:**
```bash
docker-compose up --build
```

## Performance Optimization

### Reduce Build Time

**Use Docker cache:**
```bash
docker-compose build --parallel
```

### Volume Performance (Windows/Mac)

For better performance on Windows/Mac, consider using named volumes instead of bind mounts for node_modules and vendor folders.

## Production Deployment

### Build for Production
```bash
docker-compose -f docker-compose.prod.yml up --build -d
```

### Security Considerations

1. **Change default passwords** in docker-compose.yml
2. **Set APP_DEBUG=false** in backend environment
3. **Use HTTPS** with reverse proxy (nginx/traefik)
4. **Restrict database access**
5. **Use secrets management** for sensitive data

## Networking

All containers are on the `budgetnest-network` bridge network and can communicate using service names:

- Backend connects to database using hostname: `db`
- Frontend API calls go to: `http://localhost:8000/api` (from browser)

## Data Persistence

### Volumes Created

1. **db_data:** MySQL data (persistent)
2. **backend_storage:** Laravel storage files
3. **backend_cache:** Laravel cache files

### Backup Database
```bash
docker exec budgetnest-db mysqldump -u budgetnest_user -pbudgetnest_password budgetnest > backup.sql
```

### Restore Database
```bash
docker exec -i budgetnest-db mysql -u budgetnest_user -pbudgetnest_password budgetnest < backup.sql
```

## Development Mode

For development with hot-reload:

### Frontend Development
```bash
cd frontend
npm install
npm run dev
```

Then access: http://localhost:5173 (with Vite HMR)

### Backend Development
```bash
cd backend
php artisan serve
```

## Container Health Checks

### Check Container Health
```bash
docker inspect budgetnest-db --format='{{.State.Health.Status}}'
```

### Wait for All Services
The docker-compose file includes health checks. Backend waits for database to be healthy before starting.

## Useful Commands Summary

```bash
# Start everything
docker-compose up -d

# View logs
docker-compose logs -f

# Stop everything
docker-compose down

# Restart backend
docker-compose restart backend

# Execute artisan command
docker exec -it budgetnest-backend php artisan [command]

# Access MySQL
docker exec -it budgetnest-db mysql -u budgetnest_user -p

# Clean rebuild
docker-compose down -v && docker-compose up --build

# Check status
docker-compose ps
```

## Support

If you encounter issues:

1. Check container logs: `docker-compose logs [service]`
2. Verify all containers are running: `docker-compose ps`
3. Check network connectivity: `docker network inspect budgetnest-network`
4. Ensure sufficient resources (RAM/Disk)

## Architecture Diagram

```
┌─────────────────┐
│   Browser       │
└────────┬────────┘
         │
         ├──── Port 5173 ───► ┌──────────────────┐
         │                     │   Frontend       │
         │                     │   (React/Vite)   │
         │                     └──────────────────┘
         │
         └──── Port 8000 ───► ┌──────────────────┐
                               │   Backend        │
                               │   (Laravel)      │
                               └────────┬─────────┘
                                        │
                                Port 3306
                                        │
                               ┌────────▼─────────┐
                               │   Database       │
                               │   (MySQL 8.0)    │
                               └──────────────────┘
```

---

**Last Updated:** October 7, 2026  
**Version:** 1.0  
**Project:** BudgetNest
