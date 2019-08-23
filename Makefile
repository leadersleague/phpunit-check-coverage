run:
	docker-compose -f ./docker-compose.yml -p pcc rm -f
	docker-compose -f ./docker-compose.yml -p pcc pull
	docker-compose -f ./docker-compose.yml -p pcc build #--no-cache
	docker-compose -f ./docker-compose.yml -p pcc up -d --force-recreate
	docker-compose -f ./docker-compose.yml -p pcc exec php composer install

test:
	docker-compose -f ./docker-compose.yml -p pcc exec php ./vendor/bin/phpunit -c phpunit.xml.dist --coverage-text

coverage:
	docker-compose -f ./docker-compose.yml -p pcc exec php php ./coverage.php coverage.clover 50
