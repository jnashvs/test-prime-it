pipeline {
  // Define a Docker agent. Replace 'your-custom-php-node-image' with your actual image name.
  // You would build this image once with all your dependencies.
  agent {
    docker {
      image 'ubuntu:22.04' // A base image, you'll build upon this
      args '-u root' // This will make the commands inside the container run as root, avoiding sudo issues for apt/php/node installs
      // If you need more specific versions or tools, build a custom image
      // Example of a custom image: 'my-jenkins-php-node-agent:latest'
    }
  }

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
            # These commands will run inside the Docker container
            # If the base image doesn't have apt-get, you'd use its package manager (e.g., yum for CentOS)
            apt-get update -y
            apt-get install -y software-properties-common ca-certificates curl git
            add-apt-repository ppa:ondrej/php -y
            apt-get update -y
            apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-zip php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml php${PHP_VERSION}-curl php${PHP_VERSION}-bcmath

            # Install Composer globally inside the container
            curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

            # Create environment files and database file
            cp .env.test .env
            touch database/database.sqlite

            # Install Composer dependencies
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
        sshagent(credentials: ['ssh-key-id']) { // Replace with your Jenkins SSH credentials ID
          sh '''
            ssh -o StrictHostKeyChecking=no -p ${DEFAULT_PORT} ${ROOT}@${HOST} << 'EOF'
              set -e
              PROJECT_DIR="${PROJECT_DIR}"

              echo "▶️  Ensure project dir exists"
              if [ ! -d "$PROJECT_DIR" ]; then
                mkdir -p "$PROJECT_DIR"
                git clone https://github.com/jnashvs/test-prime-it.git "$PROJECT_DIR" # Use your actual repo
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