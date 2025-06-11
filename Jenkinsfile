pipeline {
    agent any
    triggers { githubPush() }

    environment {
        REMOTE_HOST      = '64.227.116.13'
        REMOTE_USER      = 'root'
        REMOTE_PATH      = '/root/test-prime-it'
        SSH_OPTS         = '-o StrictHostKeyChecking=no'
        CREDENTIALS_ID   = 'digitalocean-ssh'
        NETWORK_NAME     = 'prime-it-laravel-network'
        APP_SERVICE      = 'laravel-prim-it'
    }

    stages {
        /*───────────────────────────*
         | 0.  Source checkout        |
         *───────────────────────────*/
        stage('Checkout') {
            steps {
                git url: 'https://github.com/jnashvs/test-prime-it.git', branch: 'main'
            }
        }

        /*───────────────────────────*
         | 1.  Pull latest code       |
         *───────────────────────────*/
        stage('Remote – Git update') {
            steps {
                sshagent(credentials: [CREDENTIALS_ID]) {
                    sh """
                        ssh ${SSH_OPTS} ${REMOTE_USER}@${REMOTE_HOST} '
                            set -e
                            echo "[1/7] cd project + git pull"
                            cd ${REMOTE_PATH}
                            git fetch --prune
                            git reset --hard origin/main
                        '
                    """
                }
            }
        }

        /*───────────────────────────*
         | 2.  Ensure network         |
         *───────────────────────────*/
        stage('Remote – Docker network') {
            steps {
                sshagent(credentials: [CREDENTIALS_ID]) {
                    sh """
                        ssh ${SSH_OPTS} ${REMOTE_USER}@${REMOTE_HOST} '
                            set -e
                            echo "[2/7] ensure Docker network"
                            docker network inspect ${NETWORK_NAME} >/dev/null 2>&1 || \
                                docker network create ${NETWORK_NAME}
                        '
                    """
                }
            }
        }

        /*───────────────────────────*
         | 3.  Build & start stack    |
         *───────────────────────────*/
        stage('Remote – Compose build/up') {
            steps {
                sshagent(credentials: [CREDENTIALS_ID]) {
                    sh """
                        ssh ${SSH_OPTS} ${REMOTE_USER}@${REMOTE_HOST} '
                            set -e
                            echo "[3/7] docker compose up --build"
                            cd ${REMOTE_PATH}
                            docker compose up -d --build
                        '
                    """
                }
            }
        }

        /*───────────────────────────*
         | 4.  Composer install       |
         *───────────────────────────*/
        stage('Remote – Composer') {
            steps {
                sshagent(credentials: [CREDENTIALS_ID]) {
                    sh """
                        ssh ${SSH_OPTS} ${REMOTE_USER}@${REMOTE_HOST} '
                            set -e
                            echo "[4/7] composer install"
                            cd ${REMOTE_PATH}
                            docker compose exec -T ${APP_SERVICE} \\
                                composer install --no-interaction --prefer-dist
                        '
                    """
                }
            }
        }

        /*───────────────────────────*
         | 5.  NPM / Vite build       |
         *───────────────────────────*/
        stage('Remote – NPM build') {
            steps {
                sshagent(credentials: [CREDENTIALS_ID]) {
                    sh """
                        ssh ${SSH_OPTS} ${REMOTE_USER}@${REMOTE_HOST} '
                            set -e
                            echo "[5/7] NPM clean, ci & build"
                            cd ${REMOTE_PATH}

                            docker compose exec -T ${APP_SERVICE} \
                                bash -c "rm -rf node_modules \\
                                         && npm cache clean --force \\
                                         && npm ci --no-audit \\
                                         && npm run build"
                        '
                    """
                }
            }
        }

        /*───────────────────────────*
         | 6.  Artisan tasks         |
         *───────────────────────────*/
        stage('Remote – Artisan & migrate & seeder') {
            steps {
                sshagent(credentials: [CREDENTIALS_ID]) {
                    sh """
                        ssh ${SSH_OPTS} ${REMOTE_USER}@${REMOTE_HOST} '
                            set -e
                            echo "[6/7] artisan migrate/seed/cache"
                            cd ${REMOTE_PATH}
                            docker compose exec -T ${APP_SERVICE} php artisan test
                            docker compose exec -T ${APP_SERVICE} php artisan migrate   --force
                            docker compose exec -T ${APP_SERVICE} php artisan db:seed    --force
                            docker compose exec -T ${APP_SERVICE} php artisan config:cache
                            docker compose exec -T ${APP_SERVICE} php artisan route:cache
                            docker compose exec -T ${APP_SERVICE} php artisan view:cache
                            docker compose exec -T ${APP_SERVICE} php artisan db:seed
                        '
                    """
                }
            }
        }
    }

    /*───────────────────────────*
     | 6.  Tests                 |
     *───────────────────────────*/
    stage('Remote – Tests: Unit & Feature') {
        steps {
            sshagent(credentials: [CREDENTIALS_ID]) {
                sh """
                    ssh ${SSH_OPTS} ${REMOTE_USER}@${REMOTE_HOST} '
                        set -e
                        cd ${REMOTE_PATH}

                        echo "[7/7] prepare SQLite for testing"
                        docker compose exec -T ${APP_SERVICE} bash -c "touch /app/database/database.sqlite"

                        echo "[7/7] running tests"
                        docker compose exec -T ${APP_SERVICE} bash -c "APP_ENV=testing php artisan test"
                    '
                """
            }
        }
    }

    post {
        success { echo '✅  Deployment complete'  }
        failure { echo '❌  Deployment failed'    }
        always  { echo '📦  Pipeline finished'    }
    }
}