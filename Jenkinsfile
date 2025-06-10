pipeline {
  agent any

  environment {
    PHP_VERSION = '8.3'
    NODE_VERSION = '18'
    PROJECT_DIR = '/root/test-prime-it'
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
            sudo update-alternatives --set php /usr/bin/php${PHP_VERSION}
            sudo apt-get update
            sudo apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-sqlite3
            cp .env.test .env
            touch database/database.sqlite
            composer install
          '''
        }
      }
    }

    stage('Setup Node and Build Assets') {
      steps {
        dir('src') {
          sh '''
            curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | sudo -E bash -
            sudo apt-get install -y nodejs
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
        sshagent(credentials: ['ssh-key-id']) { // Replace with your Jenkins SSH credentials ID
          sh '''
            ssh -o StrictHostKeyChecking=no -p ${DEFAULT_PORT} ${ROOT}@${HOST} << 'EOF'
              set -e
              PROJECT_DIR="${PROJECT_DIR}"

              echo "▶️  Ensure project dir exists"
              if [ ! -d "$PROJECT_DIR" ]; then
                mkdir -p "$PROJECT_DIR"
                git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git "$PROJECT_DIR"
                cd "$PROJECT_DIR/src"
                cp .env.example .env
              fi

              cd "$PROJECT_DIR"
              echo "[1/6] Git pull"
              git fetch --prune
              git reset --hard origin/main

              echo "[2/6] Ensure Docker network"
              docker network inspect prime-it-laravel-network >/dev/null 2>&1 || docker network create prime-it-laravel-network

              echo "[3/6] Rebuild & (re)start containers"
              docker compose up -d --build

              echo "[4/6] Composer install (in container)"
              docker compose exec -T laravel-prim-it composer install --no-interaction --prefer-dist

              echo "[5/6] npm ci & build (in container)"
              docker compose exec -T laravel-prim-it bash -c "npm install && npm run build"

              echo "[6/6] Migrate, seed & cache (in container)"
              docker compose exec -T laravel-prim-it php artisan migrate
              docker compose exec -T laravel-prim-it php artisan db:seed
              docker compose exec -T laravel-prim-it php artisan config:cache
              docker compose exec -T laravel-prim-it php artisan route:cache
              docker compose exec -T laravel-prim-it php artisan view:cache

              echo "✅ Deployment successful!"
            EOF
          '''
        }
      }
    }
  }
}