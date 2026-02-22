# Opapru HRIS – Deployment Notes

## Summary

- **Project path:** `/var/www/html/oppapru_hris`
- **Repo:** https://github.com/novu-jeff/hris_opap (branch: `production`)
- **Stack:** PHP 8.3, Composer, MySQL 8, Nginx, Laravel 10
- **URL:** https://opapru-hris.novulutions.com/

## Configured

- PHP 8.3 FPM + required extensions (mysql, xml, curl, mbstring, zip, bcmath, gd, intl)
- Nginx site: `opapru-hris.novulutions.com` (HTTP → HTTPS redirect, HTTPS with self-signed cert)
- MySQL databases: `opapru_hris`, `oppap_logs`; user: `opapru_app`
- Laravel: `.env` in place, `composer install --no-dev`, `storage:link`, `config:cache`, migrations run
- Web root owner: `www-data`; `storage` and `bootstrap/cache` are writable

## SSL (Let's Encrypt)

Certbot failed because the domain did not resolve to this server (ACME 404). After **DNS for opapru-hris.novulutions.com points to this host**, run:

```bash
sudo certbot --nginx -d opapru-hris.novulutions.com -n --agree-tos -m your@email.com
```

Certbot will replace the self-signed certificate with Let's Encrypt.

## Optional

- **Route cache:** `php artisan route:cache` fails due to duplicate route name `home.register`. Fix in routes/ then run route:cache.
- **Migration:** `2025_11_10_191724_create_social_security_table` was skipped (table already exists from create_gsis_records). No action needed.
- **Scheduler:** Add cron for www-data: `* * * * * cd /var/www/html/oppapru_hris && php artisan schedule:run >> /dev/null 2>&1`
- **Queue:** If using database queue, run `php artisan queue:work` (or Supervisor).
