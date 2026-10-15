<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## RabbitMQ queues

- The project uses `QUEUE_CONNECTION=failover` by default.
- `failover` sends jobs to RabbitMQ first and falls back to the `database` queue if RabbitMQ is unavailable.
- Queue jobs live in `app/Jobs` and are routed to named queues:
  - `users.notifications.verify`
  - `users.notifications.welcome`
  - `orders.notifications.user`
  - `orders.notifications.staff`
- Start infrastructure with `docker compose up -d rabbitmq postgres`.
- Start the site locally with `composer dev`.
- Start the RabbitMQ worker separately with `composer run dev:queue:rabbitmq`.
- If RabbitMQ is not available, start the database worker with `composer run dev:queue:database`.
- RabbitMQ management UI is available at `http://localhost:15672`.

## Sales reports scheduler

- Run `php artisan migrate` to create `daily_sales_reports`.
- `composer dev` starts `schedule:work`; the seven most recent daily reports are recalculated every two minutes.
- On a server, run `php artisan schedule:run` every minute through cron instead of `schedule:work`.
- Check the task with `php artisan schedule:list`. The sales report runs synchronously and does not need a queue worker.

## Product search

- Start Elasticsearch with `docker compose up -d elasticsearch` and set `ELASTICSEARCH_HOST=http://elasticsearch:9200` for the PHP container.
- Run `php artisan products:reindex` inside the PHP container to index products that existed before search was enabled.
- The catalog searches product names and descriptions through Elasticsearch. Product changes from the application update the index automatically.

## YooKassa payments

- Set `YOOKASSA_SHOP_ID` and `YOOKASSA_SECRET_KEY` in `.env`.
- Use `yookassa` as the online payment method during checkout.
- Configure YooKassa webhook URL as `/payments/yookassa/webhook`.
- The customer return URL is generated automatically as `/payments/yookassa/return/{order}`.
- Receipt data is embedded into payment creation when `YOOKASSA_RECEIPTS_MODE=embedded`.
