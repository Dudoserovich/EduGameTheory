set-backend: token init-db

token:
	bash scripts/check_jwt_dir.sh

get-admin-token:
	php bin/console lexik:jwt:generate-token -c App\\Entity\\User -t 86400 admin

init-db:
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --no-interaction