pipeline {
    agent any
    triggers { githubPush() }

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

        stage('Deploy on remote') {
            steps {
                sshagent(credentials: ['digitalocean-ssh']) {
                    sh """
                        ssh -o StrictHostKeyChecking=no ${REMOTE_USER}@${REMOTE_HOST} <<'EOSSH'
                        set -e

                        echo '[1/7] cd project'
                        cd '${REMOTE_PATH}'

                        echo '[2/7] git pull'
                        git fetch --prune
                        git reset --hard origin/main

                        echo '[3/7] ensure Docker network'
                        docker network inspect prime-it-laravel-network >/dev/null 2>&1 || \
                          docker network create prime-it-laravel-network

                        echo '[4/7] compose up --build'
                        docker compose up -d --build

                        echo '[6/7] npm ci & build'
                        docker compose exec -T laravel-prim-it \
                          bash -c 'npm ci && npm run build'

                        echo '[7/7] tests and artisan tasks'
                        docker compose exec -T laravel-prim-it php artisan test
                        docker compose exec -T laravel-prim-it php artisan migrate --force
                        docker compose exec -T laravel-prim-it php artisan db:seed --force
                        docker compose exec -T laravel-prim-it php artisan config:cache
                        docker compose exec -T laravel-prim-it php artisan route:cache
                        docker compose exec -T laravel-prim-it php artisan view:cache

                        echo '✅  Deployment complete'
                        EOSSH
                    """
                }
            }
        }
    }
}