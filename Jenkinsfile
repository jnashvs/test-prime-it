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

                        cd ${REMOTE_PATH}

                        echo '[1/6] Git pull'
                        git fetch --prune
                        git reset --hard origin/main

                        echo '[2/6] Ensure Docker network'
                        docker network inspect prime-it-laravel-network >/dev/null 2>&1 || \
                          docker network create prime-it-laravel-network

                        echo '[3/6] Rebuild & (re)start containers'
                        docker compose up -d --build

                        echo '[4/6] Composer install'
                        docker compose exec -T laravel-prim-it \
                          composer install --no-interaction --prefer-dist

                        echo '[5/6] npm install & build'
                        docker compose exec -T laravel-prim-it \
                          bash -c 'npm install && npm run build'

                        echo '[6/6] Migrate, seed & cache'
                        docker compose exec -T laravel-prim-it php artisan migrate --force
                        docker compose exec -T laravel-prim-it php artisan db:seed --force
                        docker compose exec -T laravel-prim-it php artisan config:cache
                        docker compose exec -T laravel-prim-it php artisan route:cache
                        docker compose exec -T laravel-prim-it php artisan view:cache
                        ENDSSH
                    """
                }
            }
        }
    }
}