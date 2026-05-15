FROM php:8.2-cli

# Install dependensi untuk PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git

# Install ekstensi pdo_pgsql agar Laravel bisa konek ke Supabase
RUN docker-php-ext-install pdo pdo_pgsql

# Tentukan direktori kerja di dalam server
WORKDIR /app

# Copy semua file kodingan ke dalam server
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Jalankan Laravel menggunakan port yang disediakan Render secara otomatis
CMD php artisan serve --host=0.0.0.0 --port=$PORT