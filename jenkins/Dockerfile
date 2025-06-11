FROM jenkins/jenkins:lts

USER root

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    php-cli php-mbstring php-xml php-curl php-zip php-gd \
    php-mysql php-bcmath unzip git curl && \
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm /tmp/composer-setup.php && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

USER jenkins