FROM node:19.1.0-alpine3.15

COPY ./ /app

WORKDIR /app

RUN NODE_ENV=development npm install
RUN npm run build

EXPOSE 3000

LABEL org.opencontainers.image.source=https://github.com/Dudoserovich/EduGameTheory
