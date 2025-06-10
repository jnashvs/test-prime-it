pipeline {
  agent {
      dockerfile {
        filename 'jenkins/Dockerfile'
        args '-u root:root -v /var/run/docker.sock:/var/run/docker.sock'
        label ''
      }
  }

  environment {
    PROJECT_DIR = '/root/test-prime-it'
    DEFAULT_PORT = '22'
  }

  stages {

    stage('Checkout') {
      steps { checkout scm }
    }

    stage('Prepare Laravel') {
      steps {
        dir('src') {
          sh '''
            cp .env.test .env
            touch database/database.sqlite
            composer install
            npm ci
            npm run build
          '''
        }
      }
    }

    stage('Test') {
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
      when { branch 'main' }
      steps {
        sshagent(credentials: ['ssh-key-id']) {
          sh '''
            ssh -o StrictHostKeyChecking=no -p ${DEFAULT_PORT} $ROOT@$HOST <<'EOF'
            set -e
            PROJECT_DIR="${PROJECT_DIR}"

            if [ ! -d "$PROJECT_DIR" ]; then
              mkdir -p "$PROJECT_DIR"
              git clone https://github.com/${GIT_URL} "$PROJECT_DIR"
              cd "$PROJECT_DIR/src"
              cp .env.example .env
            fi

            cd "$PROJECT_DIR"
            git fetch --prune
            git reset --hard origin/main

            docker network inspect prime-it-laravel-network >/dev/null 2>&1 || \
              docker network create prime-it-laravel-network

            docker compose up -d --build
            docker compose exec -T laravel-prim-it composer install --no-interaction --prefer-dist
            docker compose exec -T laravel-prim-it bash -c "npm install && npm run build"

            docker compose exec -T laravel-prim-it php artisan migrate --force
            docker compose exec -T laravel-prim-it php artisan db:seed --force
            docker compose exec -T laravel-prim-it php artisan config:cache
            docker compose exec -T laravel-prim-it php artisan route:cache
            docker compose exec -T laravel-prim-it php artisan view:cache
            EOF
          '''
        }
      }
    }
  }
}