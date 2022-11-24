ARG FRONTEND_SOURCE_IMAGE=node:19.1.0-alpine3.15
ARG NGINX_SOURCE_IMAGE=nginx:1.19.7-alpine

FROM $FRONTEND_SOURCE_IMAGE AS node-builder

RUN apk add --update --no-cache git openssh python3 make g++ gettext libintl && \
    ln -sf python3 /usr/bin/python

WORKDIR /app
EXPOSE 8090

RUN npm config set cache
RUN npm install -g npm@latest

ARG NODE_ENV=production
ENV NODE_OPTIONS --max-old-space-size=8192

COPY package.json package-lock.json ./

RUN NODE_ENV=development npm install

ARG API_BASE_URL=/api

COPY . /app

RUN envsubst < config.js.template > config.js
RUN cat config.js

RUN NODE_OPTIONS=$NODE_OPTIONS npm run build

CMD /bin/sh

FROM $NGINX_SOURCE_IMAGE

RUN rm /etc/nginx/conf.d/*

COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --from=node-builder /app/dist /app

WORKDIR /app
EXPOSE 3000
