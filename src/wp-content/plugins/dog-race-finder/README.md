# Dog Race Finder

WordPress-plugin af simonduun, version 1.0.0. PHP 8.3+, Composer PSR-4 og PHP-DI.

## Installation og Composer

Fra projektets rod (mappen med compose.yaml):

```powershell
docker compose run --rm composer install --no-dev --prefer-dist
docker compose run --rm composer validate
docker compose run --rm composer audit
```

composer.lock skal versionsstyres. Vendor-mappen skal inkluderes i en installations-ZIP. Den manuelle autoloader er erstattet af Composer.

## Shortcodes

Samlet galleri:

```text
[dog_race_finder group="dogs"]
```

Separate komponenter kan placeres i forskellige blokke, widgets eller områder på samme side:

```text
[drf_search group="dogs"]
[drf_favorites_button group="dogs"]
[drf_results group="dogs" count="12" columns="3"]
```

En separat favoritside behøver kun:

```text
[drf_favorites columns="3"]
```

Et selvstændigt hjerte til et bestemt hundebillede:

```text
[drf_heart url="BILLEDETS_URL_FRA_API" breed="pug" label="Pug"]
```

Hjerter accepterer kun billeder fra den konfigurerede billedtjeneste. `group` forbinder søgning, resultatliste og favoritvisningsknap på samme side. Brug forskellige grupper til uafhængige gallerier. `breed="hound/afghan"` kan angive en startrace. Backendens tilladte racer håndhæves også i REST API'et. Valg af en hovedrace inkluderer dens underracer i billederne.

Favoritter deles mellem komponenter og sider på samme origin og gemmes i browserens localStorage, også uden login. Maksimum 200 billeder. De synkroniseres mellem åbne faner. Ved blokeret lagring bevares de kun i hukommelsen, og brugeren får besked. Backendens favoritindstilling skjuler alle favoritkomponenter og galleriets hjerter.

## PHP i tema-skabeloner

Efter WordPress har indlæst plugins:

```php
use DogRaceFinder\Frontend\Components;

$dogs = new Components();
echo $dogs->search(['group' => 'sidebar-dogs']);
echo $dogs->favoritesButton(['group' => 'sidebar-dogs']);
echo $dogs->results(['group' => 'sidebar-dogs', 'count' => 8, 'columns' => 2]);
// Andre steder eller på en separat side:
echo $dogs->favorites(['columns' => 3]);
// Et enkelt hjerte:
echo $dogs->heart(['url' => $imageUrl, 'label' => $breedLabel, 'breed' => $breedSlug]);
```

Metoderne returnerer HTML, så temakoden bestemmer placeringen. Temaet skal kalde wp_head() og wp_footer(). Assets indlæses tidligt på frontend, så PHP-komponenter i sidebars og temaskabeloner også virker.

## Ansvar

- Hovedfilen: metadata, centrale URL-konstanter, Composer og Bootstrap.
- Bootstrap/config/services.php: dependency container og de få eksplicitte service-definitioner.
- Plugin: starter AdminProvider, FrontendProvider og RestProvider.
- Providers/: registrering af hooks for hvert område.
- Admin/: menuer, sider, Settings API og beskyttede POST-handlinger.
- Settings/: databaseindstillinger og validering.
- Api/: HTTP-kald til Dog CEO og kontrol af svar.
- Cache/: racekatalog og billedpuljer.
- Frontend/: komponentrendering, shortcodes, assets og galleri-services.
- Rest/: offentlige, læsebaserede breeds/images-endpoints med validerede parametre.
- templates/: escaped HTML uden HTTP-kald eller databaseskrivninger.
- assets/js/core/: API-adgang, gruppetilstand og favoritlager.
- assets/js/components/: én klasse pr. frontendkomponent.
- assets/js/ui/: billedkort og dialog til store billeder.

## Konfiguration og sikkerhed

Eksterne URL'er defineres kun som konstanter i dog-race-finder.php. WordPress-, asset- og REST-adresser dannes dynamisk. API'et kræver ingen credentials, og pluginet indeholder ingen API-hemmeligheder. Indstillinger gemmes i wp_options under drf_settings. Eventuelle fremtidige credentials skal administreres og gemmes i WordPress-databasen og må ikke sendes til frontend.

REST-endpoints er offentlige, da de kun leverer offentlige hundedata. Raceparametre og billedantal valideres. API-serveren og billedværten er fastlagt centralt, så besøgende ikke kan sende vilkårlige upstream-URL'er. Billedpuljer caches i ét minut, mens racelistens cachevarighed styres i backend. Vis flere fjerner dubletter; små racesamlinger kan blive udtømt.

## Tests

Fra pluginmappen:

```powershell
npm test
```

Testene bruger Nodes indbyggede test runner uden npm-afhængigheder. PHP-syntaks kan kontrolleres via WordPress-containeren. Integrationstests mod WordPress findes i tests/BackendSmokeTest.php og køres fra projektets rod:

```powershell
docker compose exec -T wordpress php wp-content/plugins/dog-race-finder/tests/BackendSmokeTest.php
```
