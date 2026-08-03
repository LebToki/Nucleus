# Changelog

All notable changes to Nucleus Sovereign Platform will be documented in this file.

## [1.0.0] - 2026-08-03

### Nucleus v1.0.0 Release
- **Linux-Native Engine**: Native Linux architecture tailored for ZorinOS, Ubuntu, and Debian distributions.
- **Systemd & Service Management**: Monitored and managed systemd services (Apache2, MariaDB/MySQL, PHP-FPM, Postfix).
- **Virtual Host & Project Discovery**: Scans `/var/www/html/` and `/etc/apache2/sites-enabled/` automatically.
- **phpMyAdmin & Database Integration**: Writable `tmp/` caching with passwordless root MySQL connection.
- **Dynamic Log & Vitals Monitoring**: Tails Apache error/access logs, PHP-FPM logs, MariaDB logs, and 2TI Orchestrator logs.
- **2TI Orchestrator Ecosystem**: Seamless UI/UX porting, MAM suite, Intelligence suite, and Inertia.js React frontend.
