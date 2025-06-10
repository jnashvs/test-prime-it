# ./jenkins-agent/Dockerfile
FROM jenkins/jenkins:lts

# --------------------------------------------------------------------
# 0. Disable all interactive prompts (tzdata, etc.)
# --------------------------------------------------------------------
ENV DEBIAN_FRONTEND=noninteractive \
    TZ=Etc/UTC

USER root

# --------------------------------------------------------------------
# 1. Base OS packages + tzdata (pre-configured)
# --------------------------------------------------------------------
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        tzdata curl ca-certificates apt-transport-https gnupg lsb-release \
        git unzip build-essential && \
    ln -sf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone && \
    dpkg-reconfigure -f noninteractive tzdata

# --------------------------------------------------------------------
# 2. Docker CLI (so pipelines can run `docker compose`)
# --------------------------------------------------------------------
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | \
        gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg && \
    echo "deb [arch=$(dpkg --print-architecture) \
        signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] \
        https://download.docker.com/linux/debian $(lsb_release -cs) stable" \
        > /etc/apt/sources.list.d/docker.list && \
    apt-get update && \
    apt-get install -y docker-ce-cli && \
    groupadd -r docker || true && usermod -aG docker jenkins

# --------------------------------------------------------------------
# 3. PHP 8.3 + extensions
# --------------------------------------------------------------------
RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu jammy main" \
        > /etc/apt/sources.list.d/ondrej-php.list && \
    apt-key adv --fetch-keys https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14AA40EC0831756756BD04F6A93B220BBE6A82A5 && \
    apt-get update && \
    apt-get install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml \
                       php8.3-sqlite3 php8.3-curl php8.3-zip

# --------------------------------------------------------------------
# 4. Composer
# --------------------------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --------------------------------------------------------------------
# 5. Node 18.x + npm
# --------------------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# --------------------------------------------------------------------
# 6. Clean up
# --------------------------------------------------------------------
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

USER jenkins