# Dog Race Finder

Lokalt WordPress-udviklingsmiljø med officielle Docker Hub-images: WordPress (PHP 8.3 / Apache), MySQL 8.4 og Caddy 2 som reverse proxy.

## Start

Installér først pluginets Composer-afhængigheder fra denne mappe:

```powershell
docker compose run --rm composer install --no-dev --prefer-dist
```


Docker Desktop skal køre med Linux-containere.

```powershell
Copy-Item .env.example .env
# Angiv to forskellige lokale passwords i .env.
docker compose up -d
```

Åbn http://dog-race-finder-wp.local og gennemfør WordPress-installationen. Aktivér derefter **Dog Race Finder** under Plugins. Pluginet har sin egen menu med Oversigt og Indstillinger. Backend og frontend er implementeret med galleri, separate komponenter og browsergemte favoritter. Se src/wp-content/plugins/dog-race-finder/README.md for arkitekturen.

Kun Caddy publiceres på værten, bundet til loopback. WordPress og MySQL tilgås internt via Docker-netværket. Miljøet er beregnet til lokal udvikling og bruger HTTP.

## Struktur

```text
compose.yaml
Caddyfile
.env.example
src/
  wp-content/
    plugins/
      dog-race-finder/
        dog-race-finder.php
        src/
          Plugin.php
```

Pluginmappen bind-mountes, så ændringer på værten slår igennem direkte. WordPress-kernen hentes fra det officielle image og gemmes i et Docker-volume. Databasen har sit eget volume.

## Kommandoer

```powershell
docker compose ps
docker compose logs -f
docker compose exec wordpress php -l wp-content/plugins/dog-race-finder/dog-race-finder.php
docker compose exec wordpress php -l wp-content/plugins/dog-race-finder/src/Plugin.php
docker compose down
```

`docker compose down` bevarer data. Tilføj kun `-v`, hvis databasen og alle miljøets Docker-volumes bevidst skal slettes.

Images bruger opdaterbare tags. Brug image-digests, hvis miljøet senere skal fastlåses til præcise builds.
