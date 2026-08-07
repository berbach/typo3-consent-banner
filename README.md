# Consent Banner (`bb/consent_banner`)

TYPO3-Extension für ein DSGVO-konformes Consent-Banner mit **Google Consent Mode v2**,
**Matomo**- und **Script**-Steuerung, revisionssicherem **Consent-Protokoll** und
Freischaltung eingebetteter Inhalte **ohne Seiten-Reload**.

- **Extension-Key:** `consent_banner`
- **Composer:** `bb/consent_banner`
- **Version:** 1.3.0 · **State:** stable
- **Autor:** Andreas Schulze, berbach GmbH

---

## Features

- **Drei-Ebenen-Modell:** Banner (pro Site) → Gruppen → Components (einzelne Dienste).
- **Vier Integrations-Typen** pro Component:
  - **Iframe / Inhaltselement** – Platzhalter statt Inhalt; nach Einwilligung wird der
    echte Inhalt **ohne Reload** eingeblendet (reversibel bei Widerruf).
  - **Google Consent Mode (gtag)** – setzt `consent`-Signale (`default`/`update`); Tags
    laden immer, respektieren aber den Consent-Status. Optionaler GTM-Container.
  - **Matomo** – steuert `_paq` (`setConsentGiven`/`forgetConsentGiven`), inkl. MTM.
  - **Script laden** – injiziert bei Einwilligung ein Snippet (mit CSP-Nonce), bei
    Widerruf Entfernen + Aufräum-Script.
- **HTML-Inhaltselemente:** externe Iframes werden **immer** automatisch durch einen
  Platzhalter ersetzt – ganz ohne Konfiguration (siehe unten).
- **Consent-Protokoll:** revisionssichere Speicherung pro Besucher (Browser-Fingerprint),
  **multi-site-fähig** (`root_page_id`), mit Suche und automatischer Aufbewahrungs-Löschung.
- **Banner-Versionierung:** `banner_version` wird bei Änderungen automatisch hochgezählt;
  das Frontend fragt bei neuer Version oder abgelaufener Lifetime erneut ab.
- **Consent Mode Bootstrap** früh im `<head>` (uncached, mit CSP-Nonce).

---

## Voraussetzungen

| Komponente | Version |
|-----------|---------|
| TYPO3     | 13.4 – 14.3 |
| PHP       | 8.3 – 8.4 |

---

## Installation

In der Root-`composer.json` des TYPO3-Projekts das Repository eintragen:

```json
"repositories": [
    { "type": "path", "url": "local_packages/*" },
    { "type": "vcs",  "url": "https://github.com/berbach/typo3-consentbanners.git" }
]
```

Anschließend installieren:

```bash
composer require bb/consent_banner:^1.3 --prefer-dist
```

- `^1.3` installiert das aktuelle stabile Release (getaggt, `v1.3.0`). Composer
  bevorzugt Tags vor Dev-Branches; der Default-Branch des Repos ist `prod`.
- `--prefer-dist` lädt ein schlankes Dist-Archiv statt eines vollständigen Git-Clones;
  Build-Toolchain und Entwicklungsdateien sind per `.gitattributes` (`export-ignore`)
  ausgeschlossen, sodass nur die für den Betrieb nötigen Dateien installiert werden.

Danach im TYPO3-Backend das Site-Set **„Berbach Consent Banner"** (`bb/consent-banner`)
zur Site hinzufügen (Site-Konfiguration → *Sets*), damit Banner-Ausgabe und
Consent-Mode-Bootstrap greifen.

---

## Schnellstart

1. Backend-Modul **Consent Banners** öffnen (Auswahl der Site oben rechts).
2. Ist kein Banner vorhanden: Button **„Consent Banner anlegen"**.
3. Banner bearbeiten (Stift-Symbol): Texte, Layout, Lifetimes, Tracking/Consent-Mode.
4. **Gruppen** und **Components** anlegen; pro Component den **Integrations-Typ** wählen.
5. Für Iframe-Components das Feld **Target Content Element** (`component_ce_target`)
   auf den passenden CType setzen (z. B. `cevideoplayer` für YouTube).

Ausführliche Anleitung inkl. Screenshots: **[Documentation/Configuration.md](Documentation/Configuration.md)**

---

## HTML-Inhaltselemente (CType `html`)

- **Externe Iframes werden immer entfernt** – ohne jede Konfiguration. Ein Iframe zu einer
  fremden Domain im Bodytext wird durch einen generischen Platzhalter (*„Third-party HTML
  has been removed."*, ohne Toggle) ersetzt; der umgebende Text bleibt. Die Ersetzung ist
  statisch/einwilligungsunabhängig und daher auch bei gecachten Seiten sicher. Interne bzw.
  relative Iframes und HTML ohne Iframe bleiben unverändert.
- **Einwilligungs-Toggle für ein HTML-Element** ist optional und erfordert: (1) eine Component
  targetet den CType `html` (`component_ce_target` → erzeugt einen `COA_INT`-Block, uncached),
  und (2) das Element ist dieser Component über das Feld **Drittanbieter/Component**
  (`ce_consent_component`) zugeordnet. Ohne dieses Setup gilt das statische Entfernen.

---

## Architektur

| Baustein | Aufgabe |
|----------|---------|
| `Controller\ManagementController` | Backend-Modul (Banner-Formular, Consent-Protokoll). |
| `DataProcessing\ConsentBannerProcessor` | Liefert `bbBannerData` (Banner/Gruppen/Components) ins Frontend-Template. |
| `ViewHelpers\AllowCookieViewHelper` (`<cb:allowCookie>`) | Gate pro Content-Element: Consent → echter Inhalt, sonst Platzhalter. |
| `Frontend\ConsentModeRenderer` | Früher `<head>`-Bootstrap (gtag/Matomo, `consent default/update`), USER_INT. |
| `Middleware\ConsentMiddleware` | Nimmt `POST /api/consent/save` entgegen und schreibt das Consent-Protokoll. |
| `EventListener\TypoScriptModifier` | Schreibt pro getargetetem CType einen `COA_INT`-Block in `sys_template.config` (uncached), räumt verwaiste Blöcke auf. |
| `Hook\DataHandlerHook` | Zählt `banner_version` bei echten Änderungen hoch. |
| `Domain\Repository\ConsentLogRepository` | Consent-Protokoll (multi-site, Suche, Retention). |
| `Resources/Public/…/CbLoader.js` | Frontend-Logik: Banner-UI, Präferenzen, Freischaltung/Widerruf ohne Reload, Consent-Signale. |

**Frontend-Verhalten:** Beim Einwilligen/Widerrufen werden Platzhalter ohne Reload getauscht
(deferred `<template>` → live), Content-Module (z. B. Videoplayer) re-initialisiert und
gtag-/Matomo-Signale sowie Script-Components aktualisiert.

---

## Assets bauen

JavaScript und CSS werden per Webpack gebaut (Ausgabe nach `Resources/Public/Dist/`):

```bash
npm install
npm run build        # Produktion (JS + CSS)
npm run build:dev    # Development
npm run script       # nur JS (Produktion)
npm run style        # nur CSS (Produktion)
```

> Content-Module (z. B. `Videoplayer.js`) müssen als **natives ESM** gebaut sein
> (`export`), sonst greift die dynamische Re-Initialisierung nach dem Einblenden nicht.

---

## Content Security Policy (CSP)

Inline-Scripts der Extension tragen eine **Nonce**. Von GTM/Matomo/YouTube nachgeladene
Skripte benötigen eine **Domain-Freigabe** in der CSP (z. B. `googletagmanager.com`,
`youtube-nocookie.com`). Details in [Documentation/Configuration.md](Documentation/Configuration.md).

---

## Lizenz

GPL-2.0-or-later
