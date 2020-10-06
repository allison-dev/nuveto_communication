run:
	cd dev && ./run.sh

start:
	cd dev && docker-compose start

down:
	cd dev && docker-compose down

horizon:
	cd dev && docker-compose exec app php artisan horizon$(cmd)

cartisan:
	cd dev && docker-compose exec app php artisan $(cmd)

lartisan:
	cd src && php artisan $(cmd)