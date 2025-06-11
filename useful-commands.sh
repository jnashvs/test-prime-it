docker compose down -v
docker logs mariadb-prim-it -f
sudo rm -rf ~/test-prime-it/db
ls -la ~/test-prime-it/
mkdir -p ~/test-prime-it/db
sudo chown -R 1001:1001 ~/test-prime-it/db
sudo chmod -R 775 ~/test-prime-it/db
ls -la ~/test-prime-it/
ls -ld ~/test-prime-it/db

sudo chown -R 1001:1001 ~/test-prime-it/db
sudo chmod -R 775 ~/test-prime-it/db

sudo chown -R 1000:1000 ./jenkins_home
ls -ld ./jenkins_home