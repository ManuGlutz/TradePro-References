# sylius-reference-linking

Sylius-Bundle zur konfigurierbaren Aufl\u00f6sung von Referenzlinks auf Variationsgruppen oder konkrete Varianten.

## Problem & Herausforderung

Variationsgruppen k\u00f6nnen bis zu 2'000 Varianten enthalten. Eine artikelweise Pflege von Referenzen ist teuer und fehleranf\u00e4llig. Falsch gesetzte Links f\u00fchren zu UX-Br\u00fcchen, Duplicate Content und schlechterer SEO.

## Ziel

Referenzen sollen gruppenbasiert gepflegt werden, Artikel-scharf nur wenn notwendig. Damit sinkt der Pflegeaufwand drastisch und die Datenqualit\u00e4t steigt.

## Kundennutzen

* **Kostenersparnis:** einmalige Pflege auf Gruppenebene statt tausendfach pro Variante.
* **Datenqualit\u00e4t:** konsistente Referenzen f\u00fcr alle Varianten.
* **SEO & Conversion:** kanonische Gruppen-URLs und stabile Navigationspfade.
* **Risiko-Reduktion:** weniger 404-Links bei Variantenlebenszyklus.

## Funktionsumfang

* Policy pro Referenztyp (`required_accessory`, `optional_accessory`, `spare_part`, `matching`).
* API zum Aufl\u00f6sen einzelner oder mehrerer SKUs.
* Twig-Helper `reference_url(type, sku)`.
* Produktseite tolerant f\u00fcr `?artnr=` Parameter und setzt `rel="canonical"`.
* Einfache Admin-UI zur Bearbeitung der Policies.

## Installation

```bash
composer require acme/sylius-reference-linking
bin/console doctrine:migrations:migrate
bin/console doctrine:fixtures:load --no-interaction
```

F\u00fcr lokale Entwicklung steht ein Docker-Setup zur Verf\u00fcgung:

```bash
make install
make fixtures
make up # docker-compose up -d
```

## Konfiguration

Referenztypen k\u00f6nnen in `ReferenceUrlResolver::REFERENCE_TYPES` erweitert werden. Standard-Policies lassen sich \u00fcber Fixtures oder direkt in der Datenbank anpassen.

## Nutzung

### Twig

```twig
<a href="{{ reference_url('required_accessory', variant.code) }}">Zubeh\u00f6r</a>
```

### JavaScript

```html
<a data-reference-type="required_accessory" data-sku="SKU1">Link</a>
<script src="/bundles/reference-linking/reference-snippet.js"></script>
```

### API

* `GET /api/references/resolve?type=required_accessory&sku=SKU1`
* `POST /api/references/resolve-batch` mit Body `{"type":"required_accessory","skus":["SKU1"]}`

Antwort:

```json
{ "type": "required_accessory", "results": { "SKU1": "/de_CH/products/ABC" } }
```

## Sicherheit

Admin-Routen liegen hinter der Sylius-Admin-Firewall und erfordern `ROLE_ADMIN`.

## Tests & Qualit\u00e4t

```bash
make test
```

CI-Status: siehe GitHub Actions.

## Lizenz

MIT
