#!/bin/bash

# Electric Bill Calculation - Docker Management Script

case "$1" in
    "start")
        echo "Starting the application..."
        docker-compose up
        ;;
    "start-bg")
        echo "Starting the application in background..."
        docker-compose up -d
        ;;
    "stop")
        echo "Stopping the application..."
        docker-compose down
        ;;
    "build")
        echo "Building and starting the application..."
        docker-compose up --build
        ;;
    "rebuild")
        echo "Rebuilding the application..."
        docker-compose down
        docker-compose up --build
        ;;
    "logs")
        if [ -z "$2" ]; then
            docker-compose logs
        else
            docker-compose logs $2
        fi
        ;;
    "shell")
        echo "Accessing app container shell..."
        docker-compose exec app bash
        ;;
    "migrate")
        echo "Running database migrations..."
        docker-compose exec app php artisan migrate
        ;;
    "seed")
        echo "Seeding the database..."
        docker-compose exec app php artisan db:seed
        ;;
    "fresh")
        echo "Fresh migration with seeding..."
        docker-compose exec app php artisan migrate:fresh --seed
        ;;
    "artisan")
        shift
        docker-compose exec app php artisan "$@"
        ;;

    "npm-build")
        echo "Running npm run build..."
        docker-compose exec app npm run build
        ;;
    "status")
        echo "Container status:"
        docker-compose ps
        ;;
    "clean")
        echo "Cleaning up Docker resources..."
        docker-compose down --volumes --remove-orphans
        docker system prune -f
        ;;
    *)
        echo "Docker Management"
        echo ""
        echo "Usage: $0 {command}"
        echo ""
        echo "Commands:"
        echo "  start       Start the application"
        echo "  start-bg    Start the application in background"
        echo "  stop        Stop the application"
        echo "  build       Build and start the application"
        echo "  rebuild     Rebuild the application"
        echo "  logs [service]  Show logs (optionally for specific service)"
        echo "  shell       Access app container shell"
        echo "  migrate     Run database migrations"
        echo "  seed        Seed the database"
        echo "  fresh       Fresh migration with seeding"
        echo "  artisan [cmd]   Run artisan command"
        echo "  status      Show container status"
        echo "  clean       Clean up Docker resources"
        echo ""
        echo "Services:"
        echo "  - App: http://localhost:8000"
        echo "  - phpMyAdmin: http://localhost:8080"
        echo ""
        ;;
esac
