pipeline {
  agent {
    docker {
      image 'ubuntu:22.04' // The base image for your build agent
      args '-u root' // Run commands inside the container as root
    }
  }

  environment {
    PHP_VERSION = '8.3'
    NODE_VERSION = '18'
    PROJECT_DIR = '/root/test-prime-it' // Adjust this to your desired path on the *deployment* server
    DEFAULT_PORT = '22'
    COMPOSER_NO_INTERACTION = '1'
  }

  options {
    skipDefaultCheckout(true)
  }

  stages {
    stage('Checkout') {
      steps {
        checkout scm
      }
    }

    stage('Setup PHP and Composer') {
      steps {
        dir('src') {
          sh '''
            # These commands run inside the 'ubuntu:22.04' Docker agent container
            apt-get update -y
            apt-get install -y software-properties-common ca-certificates curl git gnupg2
            add-apt-repository ppa:ondrej/php -y
            apt-get update -y
            apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-zip php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml php${PHP_VERSION}-curl php${PHP_VERSION}-bcmath

            curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

            cp .env.test .env || true # Use || true to prevent build failure if .env.test doesn't exist
            touch database/database.sqlite
            composer install --no-interaction --prefer-dist
          '''
        }
      }
    }

    stage('Setup Node and Build Assets') {
      steps {
        dir('src') {
          sh '''
            # Install Node.js
            curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash -
            apt-get install -y nodejs
            npm ci
            npm run build
          '''
        }
      }
    }

    stage('Run Laravel Tests') {
      steps {
        dir('src') {
          sh '''
            php artisan migrate
            php artisan db:seed
            ./vendor/bin/phpunit
          '''
        }
      }
    }

    stage('Deploy') {
      when {
        branch 'main'
      }
      steps {
        sshagent(credentials: ['ssh-key-id']) { // Your Jenkins SSH credentials ID
          sh '''
            # SSH into the deployment server
            ssh -o StrictHostKeyChecking=no -p ${DEFAULT_PORT} ${ROOT}@${HOST} << 'EOF'
              set -e
              PROJECT_DIR="${PROJECT_DIR}"

              echo "▶️  Ensure project dir exists"
              if [ ! -d "$PROJECT_DIR" ]; then
                mkdir -p "$PROJECT_DIR"
                # Correct repo URL here
                git clone https://github.com/jnashvs/test-prime-it.git "$PROJECT_DIR"
                cd "$PROJECT_DIR/src"
                cp .env.example .env || true
              fi

              cd "$PROJECT_DIR"
              echo "[1/6] Git pull"
              git fetch --prune
              git reset --hard origin/main

              echo "[2/6] Ensure Docker network"
              # Note: This network name "prime-it-laravel-network" is different from your `om-laravel-network`
              # You need to decide which network name is canonical for your deployment
              docker network inspect om-laravel-network >/dev/null 2>&1 || docker network create om-laravel-network

              echo "[3/6] Rebuild & (re)start containers"
              # Assuming your docker-compose.yml on the *deployment* server uses these service names
              # And that your deployment server has its own docker-compose.yml for the app
              docker compose -f ./docker-compose.yml up -d --build mariadb laravel phpmyadmin mailhog-website
              # The Jenkins service should *not* be part of the production docker-compose if deploying to a different server.
              # If deploying back to the same DigitalOcean Droplet, you'd manage the entire stack.

              echo "[4/6] Composer install (in container)"
              docker compose exec -T laravel-om composer install --no-interaction --prefer-dist

              echo "[5/6] npm ci & build (in container)"
              docker compose exec -T laravel-om bash -c "npm install && npm run build"

              echo "[6/6] Migrate, seed & cache (in container)"
              docker compose exec -T laravel-om php artisan migrate
              docker compose exec -T laravel-om php artisan db:seed
              docker compose exec -T laravel-om php artisan config:cache
              docker compose exec -T laravel-om php artisan route:cache
              docker compose exec -T laravel-om php artisan view:cache

              echo "✅ Deployment successful!"
            EOF
          '''
        }
      }
    }
  }
}