set-backend: token init-db

token:
	php bin/console lexik:jwt:generate-keypair --overwrite

get-admin-token:
	php bin/console lexik:jwt:generate-token -c App\\Entity\\User -t 86400 admin

init-db:
	php bin/console doctrine:migrations:migrate --dry-run
	php bin/console doctrine:fixtures:load --dry-run