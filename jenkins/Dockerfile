# ./jenkins-agent/Dockerfile
FROM jenkins/jenkins:lts           # Debian 12 (bookworm) base

USER root

# ------------------------------------------------------------------
# 0. Keep all APT calls non-interactive
# ------------------------------------------------------------------
ENV DEBIAN_FRONTEND=noninteractive TZ=Etc/UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# ------------------------------------------------------------------
# 1. Base packages
# ------------------------------------------------------------------
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        tzdata ca-certificates curl gnupg lsb-release git unzip build-essential

# ------------------------------------------------------------------
# 2. Docker CLI (so the agent can run `docker compose`)
# ------------------------------------------------------------------
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /usr/share/keyrings/docker.gpg && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker.gpg] \
         https://download.docker.com/linux/debian $(lsb_release -cs) stable" \
         > /etc/apt/sources.list.d/docker.list && \
    apt-get update && apt-get install -y docker-ce-cli && \
    groupadd -r docker || true && usermod -aG docker jenkins

# ------------------------------------------------------------------
# 3. PHP 8.3 + common extensions (from deb.sury.org — Debian-friendly)
# ------------------------------------------------------------------
RUN curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /etc/apt/trusted.gpg.d/php.gpg && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -cs) main" > /etc/apt/sources.list.d/php.list && \
    apt-get update && \
    apt-get install -y \
        php8.3 php8.3-cli php8.3-mbstring php8.3-xml \
        php8.3-sqlite3 php8.3-curl php8.3-zip

# ------------------------------------------------------------------
# 4. Composer
# ------------------------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ------------------------------------------------------------------
# 5. Node 18
# ------------------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# ------------------------------------------------------------------
# 6. Clean up APT cache
# ------------------------------------------------------------------
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

USER jenkins   # back to non-root
