FROM node:19.7-bullseye-slim

COPY ./ /app

WORKDIR /app

RUN NODE_ENV=development npm install
RUN npm run build

EXPOSE 3000

LABEL org.opencontainers.image.source=https://github.com/Dudoserovich/EduGameTheory
