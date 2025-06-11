pipeline {
    agent any

    triggers {
        githubPush()
    }

    environment {
        REMOTE_HOST = '64.227.116.13'
        REMOTE_USER = 'root'
        REMOTE_PATH = '/root/test-prime-it'
    }

    stages {
        stage('Checkout') {
            steps {
                git url: 'https://github.com/jnashvs/test-prime-it.git', branch: 'main'
            }
        }

        stage('Deploy on Remote Server') {
            steps {
                sshagent(credentials: ['digitalocean-ssh']) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_HOST} bash -s <<'ENDSSH'
                        set -e

                        echo '[1/7] Change to project directory'
                        cd ${REMOTE_PATH}

                        echo '[2/7] Pull latest code from Git'
                        git fetch --prune
                        git reset --hard origin/main

                        echo '[3/7] Ensure Docker network exists'
                        docker network inspect prime-it-laravel-network >/dev/null 2>&1 || \
                          docker network create prime-it-laravel-network

                        echo '[4/7] Rebuild and start Docker containers'
                        docker compose up -d --build

                        echo '[5/7] Run Composer install'
                        if ! docker compose exec -T laravel-prim-it composer install --no-interaction --prefer-dist; then
                          echo 'Composer install failed'
                          exit 1
                        fi

                        echo '[6/7] Run npm install and build'
                        if ! docker compose exec -T laravel-prim-it bash -c 'npm install && npm run build'; then
                          echo 'npm install or build failed'
                          exit 1
                        fi

                        echo '[7/7] Run tests and Laravel commands'
                        docker compose exec -T laravel-prim-it php artisan test
                        docker compose exec -T laravel-prim-it php artisan migrate --force
                        docker compose exec -T laravel-prim-it php artisan db:seed --force
                        docker compose exec -T laravel-prim-it php artisan config:cache
                        docker compose exec -T laravel-prim-it php artisan route:cache
                        docker compose exec -T laravel-prim-it php artisan view:cache

                        echo '✅ Deployment complete.'
                        ENDSSH
                    """
                }
            }
        }
    }
}