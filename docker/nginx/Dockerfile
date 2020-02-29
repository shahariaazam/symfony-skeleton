FROM nginx:alpine

# Custom nginx configuration for this app
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./public /var/www/html/public