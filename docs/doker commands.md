# Docker Deploy from GIT
    docker-compose -f docker-deploy/docker-compose.yml build --no-cache
    docker-compose -f docker-deploy/docker-compose.yml up -d
    docker-compose -f docker-deploy/docker-compose.yml exec app php artisan migrate:fresh --seed
    docker-compose -f docker-deploy/docker-compose.yml exec -u 0 app service supervisor start
    
    docker-compose -f docker-deploy/docker-compose.yml exec -u 0 app supervisorctl reread
    docker-compose -f docker-deploy/docker-compose.yml exec -u 0 app supervisorctl update &&
    docker-compose -f docker-deploy/docker-compose.yml exec -u 0 app supervisorctl start kara-worker:*

# Useful Commands 

    docker-compose -f docker-deploy/docker-compose.yml down
    docker-compose -f docker-deploy/docker-compose.yml up -d --force-recreate --build
    docker-compose -f docker-deploy/docker-compose.yml exec app bash
    docker-compose -f docker-deploy/docker-compose.yml exec db bash
    docker-compose -f docker-deploy/docker-compose.yml exec -u 0 app composer update --no-dev
    
# Git
    docker-compose -f docker-deploy/docker-compose.yml exec app git reset --hard    
    docker-compose -f docker-deploy/docker-compose.yml exec app git pull origin main
    # Note: Use GitHub CLI or environment variables for authentication instead of hardcoding tokens

# ecs
    docker context create ecs dockerGrow
    docker context use dockerGrow


    docker-compose -f docker-deploy/docker-compose.yml exec -u 0 app openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout nginx-selfsigned.key -out nginx-selfsigned.crt
