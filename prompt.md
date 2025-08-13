Rolle: Du bist ein erfahrener Senior-Entwickler für Symfony/Sylius. Erstelle ein produktionsreifes, testbares Repository als eigenständiges Sylius-Bundle/Plugin, das „Referenzlinks“ (Zubehör/Ersatzteile/Passende Produkte) w wahlweise auf Variationsgruppe oder konkrete Variante auflöst. Fokus: klare Architektur, geringe Betriebskosten, saubere DX.

Repository-Name: sylius-reference-linking

1) Ziel & Hintergrund
Ist-Problem: Der Shop verlinkt Referenzen immer auf konkrete Artikel (Variante) mit URL-Muster
/de_CH/products/{groupCode}?artnr={variantCode}.
Referenzen werden im PIM jedoch artikelweise gepflegt; in der Praxis gibt es teils ~2’000 Varianten pro Variationsgruppe. Pflege ist teuer, fehleranfällig und unlogisch.

Soll-Lösung (kurzfristig Shop-seitig): Pro Referenztyp (z. B. required_accessory, optional_accessory, spare_part, matching) konfigurierbar, ob Links auf Variationsgruppe oder Variante zeigen.

Langfristig (PIM-seitig): PIM exportiert referenzielle Links gruppenbasiert inkl. Flag scope = group|variant. Shop respektiert dies bzw. kann per Policy übersteuern.

Wirtschaftlicher Nutzen: 1× Pflege auf Gruppenebene statt x-tausendfach auf Variantenebene ⇒ deutlich weniger Pflegeaufwand, höhere Datenqualität, stabilere SEO (kanonische Gruppen-URLs), weniger Supportfälle, bessere Konversionspfade.

2) Anforderungen (MVP)
Entity & Storage

Tabelle app_reference_linking_policy(reference_type PK, target_level ENUM['group','variant']).

Seed/Fixture mit Defaults:
required_accessory=group, optional_accessory=group, spare_part=variant, matching=group.

Routing & PDP

Kanonische Gruppen-URL: /de_CH/products/{code} (ohne artnr).

Optionaler Query ?artnr={variantCode} nur zur Vorselektion; Seite muss ohne diesen Param vollständig funktionieren.

<link rel="canonical" href="/de_CH/products/{code}" /> setzen.

Service

ReferenceUrlResolver::resolveUrl(referenceType, variantCode):

Lädt Variant → zugehöriges Product (Variationsgruppe).

Ermittelt Policy und baut URL:

Gruppe → /de_CH/products/{code}

Variante → /de_CH/products/{code}?artnr={variantCode}

Rückgabe ABSOLUTE_URL.

resolveUrls(type, [variantCodes]) (Batch).

Admin-UI

Einfache CRUD-Maske je Referenztyp (Dropdown „Variationsgruppe“ vs. „Variante“).

Berechtigungen nur für Admin-Rolle.

API

GET /api/references/resolve?type={t}&sku={v} → { sku, type, url }

POST /api/references/resolve-batch Body: { type, skus: [] } → { type, results: { sku: url|null } }

Twig-Extension

Funktion reference_url(type, sku) → string|null.

Frontend-Snippet

Kleines JS-Beispiel, das Batch-Endpoint aufruft und Anker-Tags befüllt.

Tests

Unit-Tests für Resolver (group/variant/null).

API-Tests (Batch, Fehlerfälle).

Controller-Test PDP (mit/ohne artnr).

DX & Betrieb

makefile mit Targets (make install, make test, make cs-fix, make fixtures).

GitHub Actions (PHP 8.2/8.3) für CI: lint, phpstan (max lvl sinnvoll), phpunit.

Docker-Compose (php-fpm, nginx, postgres) für lokales Spin-Up.

Kompatibilität

Sylius ^1.12 (Symfony ^6.3) – Version im composer.json dokumentieren.

3) Projektstruktur (erzeuge alle Dateien)
pgsql
Kopieren
Bearbeiten
sylius-reference-linking/
├─ src/
│  ├─ DependencyInjection/
│  │  └─ ReferenceLinkingExtension.php
│  ├─ Entity/
│  │  └─ ReferenceLinkingPolicy.php
│  ├─ Repository/ (falls benötigt)
│  ├─ Service/
│  │  └─ ReferenceUrlResolver.php
│  ├─ Controller/
│  │  ├─ Api/ReferenceUrlController.php
│  │  └─ Product/ShowController.php
│  ├─ Twig/
│  │  └─ ReferenceUrlExtension.php
│  ├─ Form/Type/
│  │  └─ ReferenceLinkingPolicyType.php
│  ├─ Resources/config/
│  │  ├─ routes.yaml
│  │  ├─ services.yaml
│  │  └─ security_admin_notes.md
│  └─ ReferenceLinkingBundle.php
├─ migrations/
│  └─ Version2025xxxxxx.php
├─ fixtures/
│  └─ ReferenceLinkingPolicyFixtures.php
├─ templates/
│  ├─ admin/reference_linking/index.html.twig
│  ├─ admin/reference_linking/edit.html.twig
│  └─ product/show.html.twig  (Demo-PDP mit Canonical)
├─ public/
│  └─ demo-snippets/reference-list.html
├─ tests/
│  ├─ Unit/ReferenceUrlResolverTest.php
│  ├─ Functional/Api/ReferenceUrlControllerTest.php
│  └─ Functional/Product/ShowControllerTest.php
├─ .github/workflows/ci.yml
├─ docker/
│  ├─ nginx.conf
│  └─ php.ini
├─ docker-compose.yml
├─ Makefile
├─ composer.json
├─ phpunit.xml.dist
├─ phpcs.xml
├─ phpstan.neon
├─ README.md
└─ LICENSE
4) Implementierungsdetails (wichtige Punkte)
Entity: ReferenceLinkingPolicy (referenceType string PK, targetLevel string). Getter/Setter, Validierung (only group|variant).

Migration: erstellt Tabelle, setzt Defaults.

Resolver: nutzt ProductVariantRepositoryInterface + ProductRepositoryInterface + UrlGeneratorInterface. Fallback: wenn Policy fehlt → group. Wenn SKU/Variante nicht existiert → null.

Routes:

PDP: /{_locale}/products/{code} (_locale: de_CH|de_DE|en_GB|fr_CH).

API: /api/references/resolve (GET) & /api/references/resolve-batch (POST).

Admin-UI: Simple Form per Typ; Index zeigt Liste & Links zum Edit.

Twig: reference_url(type, sku).

JS-Snippet: Batch-Fetch und DOM-Befüllung.

SEO: PDP setzt <link rel="canonical"> auf Gruppen-URL, auch wenn ?artnr= vorhanden.

Konfigurierbarkeit: Referenztypenliste als Konstante im Form/Service und im README dokumentieren; einfach erweiterbar.

5) README.md – Inhalt (bitte ausführlich verfassen)
Titel & Kurzbeschreibung
Was macht das Plugin? Ein-Satz-Pitch.

Problem & Herausforderung (klar wirtschaftlich argumentiert)
Variationsgruppen mit bis zu 2’000 Varianten → artikelweise Referenzpflege ist teuer und fehleranfällig.

Falsche Link-Ebene erzeugt UX-Brüche, duplicate content, schlechtere SEO.

Ziel: Gruppenbasierte Links dort, wo es sinnvoll ist; artikelscharf nur, wenn nötig.

Kundennutzen / Business Impact
Kostenersparnis: x-fach weniger Pflegeaufwand → geringere TCO.

Datenqualität: konsistente Referenzen über die ganze Gruppe.

SEO/Conversion: kanonische Gruppen-URLs, einfachere Navigation, höhere Relevanz.

Risiko-Reduktion: weniger 404/Fehlverlinkungen beim Variantenlebenszyklus.

Funktionsumfang
Policy pro Referenztyp (Gruppe vs. Variante).

API (einzeln & Batch).

Twig-Helper.

PDP tolerant für ?artnr.

Admin-UI.

Roadmap (PIM-seitige Zukunft)
PIM-Export mit scope=group|variant, from_group_code, to_group_code.

Shop-Importer übernimmt scope bzw. übersteuert per Policy.

Installation
Composer-Install, Bundle-Registrierung (falls nötig), Migration ausführen, Fixtures laden.

.env/Docker Hinweise.

Konfiguration
Referenztypen erweitern (Beispiel).

Standard-Policy per Fixtures/SQL anpassen.

Nutzung
Twig-Beispiel (reference_url).

JS-Beispiel (Batch-Fetch).

Beispiel-URLs.

API
Endpunkte, Parameter, Response-Beispiele.

Sicherheit & Berechtigungen
Admin-Route hinter Admin-Firewall.

API ggf. schreibgeschützt/ratelimited (Hinweis).

Tests & Qualität
Wie Tests ausführen (make test).

CI-Status.

Beispiele & Screenshots
Admin-Maske, PDP-Canonical.

Lizenz
6) Qualität & Tooling
Coding Standards: PSR-12, phpcs.xml.

Static Analysis: phpstan lvl passend mit baseline.

CI: GitHub Actions Matrix (8.2/8.3), Cache Composer, PHPUnit, PHPStan, PHPCS.

Docker: nginx + php-fpm + postgres; make install && make fixtures && make up.

7) Abnahmekriterien
ReferenceUrlResolver liefert korrekte URL für beide Modi.

PDP funktioniert mit und ohne ?artnr.

Admin-UI ändert Policy persistent.

API-Batch gibt Mapping für ≥10 SKUs performant zurück.

Tests grün in CI; Docker-Stack bootet; README vollständig.

8) Bonus (optional, wenn Zeit)
Symfony Config für Default-Policy pro Typ (config/packages/reference_linking.yaml).

Event/Hook, um PIM-Scope zu respektieren.

Cache-Layer (in-memory) für Resolver, invalidiert bei Policy-Änderung.

Wichtige Hinweise für den Agenten:

Schreibe alle Dateien vollständig aus, kein Pseudocode.

Nutze Sylius-/Symfony-Best Practices (Autowiring, Attribute-Routing).

Halte dich an die Struktur oben; achte auf lauffähige Namespaces und composer.json-Autoload.

README in Deutsch, klar strukturiert, mit Codebeispielen.
