# Host setup — Nucleus on Apache (Linux)

Nucleus is a local dev dashboard: it manages project directories that belong to
the developer, so the web process must be able to create and remove entries in
the web root. On Windows (Laragon) the dashboard runs as the logged-in user and
this is free. On Linux Apache runs as `www-data`, so it must be arranged.

## Web root

```
WEB_ROOT=/home/zorin/.2ti/web/nucleus      # /var/www/html is a symlink to this
```

Applied:

```bash
sudo usermod -aG zorin www-data            # already present on this machine
sudo chmod 2775 "$WEB_ROOT"                # group write + setgid (new dirs keep group zorin)
```

`setgid` matters: without it, directories created by the dashboard belong to
`www-data:www-data` and the developer cannot edit them afterwards.

## Apache umask

Without this, files created by PHP are `0644` and stay unwritable by the owner's
group even with a setgid root.

`/etc/systemd/system/apache2.service.d/umask.conf`

```ini
[Service]
UMask=0002
```

```bash
sudo systemctl daemon-reload && sudo systemctl restart apache2
```

Result: dashboard-created projects are `drwxrwsr-x www-data:zorin` with
`-rw-rw-r-- www-data:zorin` files — editable by both the developer and Apache.

## Virtual host

A local wildcard certificate already exists at
`~/.2ti/config/ssl/2ti_local.{crt,key}` (`DNS:*.local`), so no per-project cert
is needed. Pair for `nucleus-2.local`:

```apache
# /etc/apache2/sites-available/nucleus-2.local.conf
<VirtualHost *:80>
    ServerName nucleus-2.local
    ServerAlias www.nucleus-2.local
    DocumentRoot /var/www/html/nucleus-2
    <Directory /var/www/html/nucleus-2>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
    </Directory>
</VirtualHost>
```

HTTPS twin adds `SSLEngine on` plus the two certificate paths on `*:443`.

```bash
sudo ln -s /etc/apache2/sites-available/nucleus-2.local.conf     /etc/apache2/sites-enabled/
sudo ln -s /etc/apache2/sites-available/nucleus-2.local_ssl.conf /etc/apache2/sites-enabled/
grep -q nucleus-2.local /etc/hosts || echo "127.0.0.1 nucleus-2.local" | sudo tee -a /etc/hosts
sudo apache2ctl configtest && sudo systemctl reload apache2
```

## Project root

When the dashboard is its own vhost, `DOCUMENT_ROOT` is the dashboard, not the
directory holding the projects. Set it explicitly in `data/preferences.json`:

```json
{ "document_root": "/var/www/html" }
```

`Nucleus\Core\System::getWwwPath()` reads this first.

## Hosts / vhost sync

`nucleus-sync.timer` runs `/usr/local/bin/nucleus-sync-hosts.sh` every 15 min:
it adds `127.0.0.1 <name>.local` and an HTTP/HTTPS vhost pair for each project
directory, prunes entries for removed projects, and reloads Apache.

## Verify

```bash
curl -sk -o /dev/null -w '%{http_code}\n' https://nucleus-2.local/     # 200
id www-data | grep -q zorin && echo "www-data in zorin group"
namei -l /var/www/html/ | tail -3                                     # setgid + g+w
```
