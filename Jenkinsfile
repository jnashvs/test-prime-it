pipeline {
    agent any

    environment {
        PHP_VERSION = "8.3"
        NODE_VERSION = "18"
        PROJECT_DIR = "/root/test-prime-it"
        DEFAULT_PORT = 22
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Setup PHP') {
            steps {
                sh '''
                    sudo add-apt-repository ppa:ondrej/php -y
                    sudo apt-get update
                    sudo apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-cli php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-curl php${PHP_VERSION}-zip unzip curl
                    curl -sS https://getcomposer.org/installer | php
                    sudo mv composer.phar /usr/local/bin/composer
                '''
            }
        }

        stage('Prepare .env and DB') {
            steps {
                dir('src') {
                    sh '''
                        cp .env.test .env
                        mkdir -p database
                        touch database/database.sqlite
                    '''
                }
            }
        }

        stage('Install Composer Dependencies') {
            steps {
                dir('src') {
                    sh 'composer install'
                }
            }
        }

        stage('Setup Node & Build Assets') {
            steps {
                sh '''
                    curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | sudo -E bash -
                    sudo apt-get install -y nodejs
                '''
                dir('src') {
                    sh '''
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

        stage('Deploy to Server') {
            when {
                branch 'main'
            }
            steps {
                sshagent(credentials: ['your-ssh-key-id']) {
                    sh """
                        ssh -p ${DEFAULT_PORT} root@your-server-ip << 'EOF'
                        set -e
                        PROJECT_DIR="${PROJECT_DIR}"

                        echo "▶️ Ensure project directory exists"
                        if [ ! -d "$PROJECT_DIR" ]; then
                            mkdir -p "$PROJECT_DIR"
                            git clone https://github.com/your-org/your-repo.git "$PROJECT_DIR"
                            cd "$PROJECT_DIR/src"
                            cp .env.example .env
                        fi

                        cd "$PROJECT_DIR"
                        echo "[1/6] Git pull"
                        git fetch --prune
                        git reset --hard origin/main

                        echo "[2/6] Ensure Docker network"
                        docker network inspect prime-it-laravel-network >/dev/null 2>&1 || \
                            docker network create prime-it-laravel-network

                        echo "[3/6] Rebuild containers"
                        docker compose up -d --build

                        echo "[4/6] Composer install"
                        docker compose exec -T laravel-prim-it \
                            composer install --no-interaction --prefer-dist

                        echo "[5/6] npm ci & build"
                        docker compose exec -T laravel-prim-it bash -c "npm install && npm run build"

                        echo "[6/6] Migrate, seed, cache"
                        docker compose exec -T laravel-prim-it php artisan migrate
                        docker compose exec -T laravel-prim-it php artisan db:seed
                        docker compose exec -T laravel-prim-it php artisan config:cache
                        docker compose exec -T laravel-prim-it php artisan route:cache
                        docker compose exec -T laravel-prim-it php artisan view:cache

                        echo "✅ Deployment successful!"
                        EOF
                    """
                }
            }
        }
    }
}