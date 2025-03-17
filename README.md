# News Aggregator API

A modern RESTful API for aggregating and delivering personalized news content based on user preferences.

## Features

- 📰 Browse and search news articles from multiple sources
- 🔍 Filter articles by category, source, and date
- 👤 User authentication with Laravel Sanctum
- ⭐ Save user preferences for news sources and categories
- 📱 Personalized news feed based on user preferences
- 📚 Comprehensive API documentation with Swagger/OpenAPI

## Tech Stack

- **Framework**: Laravel 12.1.1
- **Database**: PostgreSQL
- **Authentication**: Laravel Sanctum
- **Documentation**: L5-Swagger (OpenAPI)
- **Containerization**: Docker & Docker Compose

## Prerequisites

- Docker and Docker Compose
- Git

## Getting Started with Docker

### 1. Clone the Repository

```bash
git clone https://github.com/Mashfooq/be-news-aggregator.git
cd be-news-aggregator
```

### 2. Environment Setup

Copy the example environment file and modify it for Docker:

```bash
cp .env.example .env
```

Update the following variables in the `.env` file:

```
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=postgres_db
DB_PORT=5432
DB_DATABASE=news_aggregator
DB_USERNAME=postgres
DB_PASSWORD=your_password

L5_SWAGGER_CONST_HOST=http://localhost:8000/api

NEWS_API_KEY=your_api_key
GUARDIAN_API_KEY=your_api_key
NYTIMES_API_KEY=your_api_key

OPENROUTER_API_KEY=your_api_key
```

### API Keys

To obtain the required API keys for the news sources and OpenRouter:

- **NEWS_API_KEY**: Register at [News API](https://newsapi.org/)
- **GUARDIAN_API_KEY**: Get access at [The Guardian Open Platform](https://open-platform.theguardian.com/access/)
- **NYTIMES_API_KEY**: Register at [New York Times Developer Portal](https://developer.nytimes.com/get-started)
- **OPENROUTER_API_KEY**: Generate a key at [OpenRouter Settings](https://openrouter.ai/settings/keys)

### 3. Build and Start Docker Containers

```bash
docker-compose up -d
```

This will start the following containers:
- `app`: PHP-FPM application container
- `postgres_db`: PostgreSQL database
- `nginx`: Web server

### 4. Install Dependencies and Set Up the Application

```bash
# Enter the app container
docker-compose exec app bash

# Inside the container:
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan l5-swagger:generate
```

### 5. Set Proper Permissions

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### 6. Access the Application

- **API**: http://localhost:8000/api
- **API Documentation**: http://localhost:8000/api/documentation

## Docker Environment

The project uses Docker Compose with the following services:

### App Service
- PHP 8.2 with FPM
- Composer for dependency management
- Required PHP extensions for Laravel and PostgreSQL

### PostgreSQL Service
- PostgreSQL 15
- Persistent volume for data storage
- Exposed on port 5432

### Nginx Service
- Nginx web server
- Configured to serve the Laravel application
- Exposed on port 8000

## API Authentication

The API uses Bearer token authentication. To access protected endpoints:

1. Register a new user or login with existing credentials
2. Use the returned token in the Authorization header:
   ```
   Authorization: Bearer your_token_here
   ```

## Using the API Documentation

1. Navigate to http://localhost:8000/api/documentation
2. Click the "Authorize" button (lock icon)
3. Enter your token (without "Bearer " prefix)
4. Click "Authorize" and then "Close"
5. Now you can execute authenticated API requests

## Available Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login and get a token
- `POST /api/logout` - Logout (invalidate token)
- `POST /api/password-reset` - Reset user password

### Articles
- `GET /api/articles` - Get a list of articles (with filtering and pagination)
- `GET /api/articles/{id}` - Get a single article by ID

### User Preferences
- `POST /api/preferences` - Save user preferences
- `GET /api/preferences` - Get user preferences
- `GET /api/news-feed` - Get personalized news feed

## Development

### Useful Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View container logs
docker-compose logs -f

# Execute command in the app container
docker-compose exec app php artisan <command>

# Rebuild containers after Dockerfile changes
docker-compose up -d --build
```

### Regenerating API Documentation

After making changes to the API endpoints or models, regenerate the Swagger documentation:

```bash
docker-compose exec app php artisan l5-swagger:generate
```

### Running Tests

```bash
docker-compose exec app php artisan test
```

## Troubleshooting

### Common Issues

1. **Database Connection Issues**:
   - Ensure PostgreSQL container is running: `docker-compose ps`
   - Check database credentials in `.env` file
   - Make sure `DB_HOST` is set to `postgres_db` (the service name in docker-compose.yml)

2. **Permission Issues**:
   - Make sure storage and bootstrap/cache directories are writable:
     ```bash
     docker-compose exec app chmod -R 777 storage bootstrap/cache
     ```

3. **API Authentication Issues**:
   - Verify you're using a valid token
   - Ensure the token is correctly formatted in the Authorization header
   - Check that the Swagger UI is properly configured with the token

4. **Swagger Documentation Issues**:
   - If the documentation doesn't reflect your latest changes, regenerate it:
     ```bash
     docker-compose exec app php artisan l5-swagger:generate
     ```
   - Ensure `L5_SWAGGER_CONST_HOST` is set correctly in your `.env` file

## License

[MIT License](LICENSE)

## Contact

For questions or support, please contact [mashhussmashfoo@gmail.com](mailto:mashhussmashfoo@gmail.com). 