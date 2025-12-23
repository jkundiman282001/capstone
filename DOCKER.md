# Docker Setup Guide

This project is containerized using Docker and Docker Compose.

## Prerequisites

- Docker Desktop (Windows/Mac) or Docker Engine + Docker Compose (Linux)
- Git

## Quick Start

1. **Clone the repository** (if not already done)
   ```bash
   git clone <repository-url>
   cd capstone
   ```

2. **Create environment file**
   ```bash
   cp .env.example .env
   ```

3. **Update `.env` file with Docker database configuration:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=capstone
   DB_USERNAME=capstone_user
   DB_PASSWORD=password
   ```

4. **Build and start containers**
   ```bash
   docker-compose up -d --build
   ```

5. **Install dependencies and setup Laravel**
   ```bash
   # Install Composer dependencies
   docker-compose exec app composer install

   # Generate application key
   docker-compose exec app php artisan key:generate

   # Run migrations
   docker-compose exec app php artisan migrate

   # (Optional) Seed database
   docker-compose exec app php artisan db:seed
   ```

6. **Set storage permissions**
   ```bash
   docker-compose exec app chmod -R 775 storage bootstrap/cache
   ```

7. **Access the application**
   - Web: http://localhost
   - Database: localhost:3307 (default, or use DB_PORT in .env to customize)
   
   **Note:** If you have XAMPP MySQL running on port 3306, Docker will use port 3307 by default to avoid conflicts.

## Development with Vite

For development with hot module replacement (HMR), start the Node service:

```bash
docker-compose --profile dev up node
```

Or include it in your docker-compose up command:

```bash
docker-compose --profile dev up
```

## Common Commands

### Start containers
```bash
docker-compose up -d
```

### Stop containers
```bash
docker-compose down
```

### View logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql
```

### Execute Artisan commands
```bash
docker-compose exec app php artisan <command>
```

### Execute Composer commands
```bash
docker-compose exec app composer <command>
```

### Access container shell
```bash
docker-compose exec app bash
```

### Rebuild containers
```bash
docker-compose up -d --build
```

### Clear Laravel cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

### Run tests
```bash
docker-compose exec app php artisan test
```

## Services

- **nginx**: Web server (port 80)
- **app**: PHP-FPM application container
- **mysql**: MySQL 8.0 database (port 3306)
- **node**: Node.js service for Vite (development only)

## Environment Variables

You can customize the setup by modifying these environment variables in your `.env` file or `docker-compose.yml`:

- `APP_PORT`: Web server port (default: 80)
- `DB_DATABASE`: Database name (default: capstone)
- `DB_USERNAME`: Database user (default: capstone_user)
- `DB_PASSWORD`: Database password (default: password)
- `DB_ROOT_PASSWORD`: MySQL root password (default: rootpassword)
- `DB_PORT`: Database port (default: 3306)

## Troubleshooting

### Permission Issues
If you encounter permission issues with storage:
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Issues
1. Ensure MySQL container is healthy: `docker-compose ps`
2. Check database credentials in `.env` match `docker-compose.yml`
3. Verify network connectivity: `docker-compose exec app ping mysql`

### Port Already in Use
If port 80 or 3306 is already in use, change the ports in `docker-compose.yml`:
```yaml
ports:
  - "8080:80"  # Change 80 to 8080
```

### Clear Everything and Start Fresh
```bash
docker-compose down -v
docker-compose up -d --build
```

## Production Considerations

For production deployment:

1. Use production-optimized Dockerfile (multi-stage build)
2. Set proper environment variables
3. Use secrets management
4. Configure SSL/TLS certificates
5. Set up proper backup strategies
6. Use production database credentials
7. Disable development tools and debug mode

