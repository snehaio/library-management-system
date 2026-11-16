FROM php:8.3-cli
WORKDIR /app
COPY . /app
RUN mkdir -p /app/data && chmod -R 777 /app/data
EXPOSE 10000
# Load sample data on first start (only if no data exists), then run the app
CMD ["sh", "-c", "[ -f data/books.json ] || php seed.php; php -S 0.0.0.0:${PORT:-10000} -t /app"]
