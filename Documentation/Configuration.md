# Consent Banner – Einrichtung & Konfiguration

Diese Anleitung beschreibt, wie der Consent Banner im TYPO3-Backend eingerichtet
und konfiguriert wird und wie die verschiedenen Integrations-Typen im Frontend
wirken.

---

## 1. Überblick

Der Consent Banner besteht aus drei Ebenen:

- **Banner** – ein Datensatz pro Site (Root-Seite). Enthält Texte, Layout,
  Lifetimes und die Tracking-/Consent-Mode-Konfiguration.
- **Gruppen** – z. B. *Technisch notwendig* (essential, immer aktiv),
  *Analytics*, *Marketing*, *Multimedia*.
- **Components** – einzelne Dienste innerhalb einer Gruppe (z. B. YouTube,
  Google Analytics, Matomo). Jede Component bestimmt über ihren
  **Integrations-Typ**, wie sie im Frontend behandelt wird.

Verwaltet wird alles im Backend-Modul **Consent Banners**:

![Backend-Modul Consent Banner Management](Images/01-module.png)

Das Modul zeigt den Banner der aktuell gewählten Site (Auswahl oben rechts über
das Site-Menü). Über das Stift-Symbol wird der Banner bearbeitet.

---

## 2. Voraussetzungen

- Pro Site existiert **ein** Banner-Datensatz (auf der Root-Seite gespeichert).
- Ist noch keiner vorhanden, bietet das Modul einen Button **„Consent Banner
  anlegen"**.
- Das Set *ConsentBanner* muss in der Site aktiv sein (Site-Sets / statisches
  TypoScript), damit Banner-Ausgabe und Consent-Mode-Bootstrap greifen.

---

## 3. Banner bearbeiten

Über den Stift im Modul öffnet sich das Banner-Formular mit mehreren Tabs:

![Banner-Formular](Images/02-banner-general.png)

- **Banner** – Titel, Beschreibung, Sichtbarkeit.
- **Text for labels** – Button- und Cookie-Info-Texte (Fallback: Sprachdatei).
- **Essential consent group** – die immer aktive Gruppe *technisch notwendig*.
- **Other consent groups** – weitere, abwählbare Gruppen mit ihren Components.
- **Banner setting** – Layout, Opener (Textlink/Button), Lifetimes.
- **Tracking / Consent Mode** – GTM- und Matomo-Konfiguration (siehe unten).

---

## 4. Integrations-Typen pro Component

Jede Component hat im Tab **Integration** das Feld **Integrations-Typ**. Es
steuert, wie die Component im Frontend eingebunden wird:

![Component Integration-Tab](Images/04-component-integration.png)

| Typ | Verhalten im Frontend |
|-----|-----------------------|
| **Inhaltselement / Iframe (Placeholder)** | Blockt Inhaltselemente/Iframes. Statt des Inhalts erscheint ein Placeholder; nach Einwilligung wird der echte Inhalt **ohne Reload** eingeblendet (reversibel bei Widerruf). |
| **Google Consent Mode (gtag)** | Setzt gtag-Consent-Signale (`consent update`). Google-Tags laden immer, respektieren aber den Consent-Status. |
| **Matomo** | Steuert die Matomo-Einwilligung über `_paq` (`setConsentGiven` / `forgetConsentGiven`). |
| **Script laden** | Lädt bei Einwilligung ein hinterlegtes Script; bei Widerruf wird es entfernt und ein Aufräum-Script ausgeführt. |

### 4.1 Iframe / Inhaltselement

- Feld **Target Content Element** (`component_ce_target`): der/die CType(s), die
  diese Component abdeckt (z. B. `cevideoplayer` für YouTube).
- Optional **Placeholder-Titel/-Text**; ist der Titel leer, wird der
  Component-Titel verwendet.
- Wird im jeweiligen Fluid-Template über `<cb:allowCookie>` genutzt.

Ein getargeteter CType wird über einen `COA_INT`-Block (in `sys_template.config`,
automatisch vom `TypoScriptModifier` beim Speichern der Component geschrieben)
**uncached** gerendert. Nur so kann der `<cb:allowCookie>`-ViewHelper pro Request
entscheiden, ob der echte Inhalt oder ein Placeholder **mit Einwilligungs-Toggle**
erscheint.

#### HTML-Inhaltselemente (CType `html`)

Das `html`-Element wird gesondert behandelt (`AllowCookieViewHelper::renderHtmlElement()`):

- **Externe Iframes werden IMMER entfernt** – ohne jede Konfiguration. Enthält der
  Bodytext ein Iframe zu einer fremden Domain, wird es durch einen generischen
  Placeholder (*„Third-party HTML has been removed."*, **ohne** Toggle) ersetzt;
  der umgebende Text bleibt erhalten. Das ist eine statische, einwilligungs­
  unabhängige Ersetzung und funktioniert daher auch bei gecachten Seiten – es
  muss **keine** Component den CType `html` targeten. Interne bzw. relative Iframes
  und HTML ohne Iframe bleiben unverändert.
- **Einwilligungs-Toggle für ein HTML-Element** (Besucher kann zustimmen → Iframe
  wird live) ist optional und erfordert zwei Dinge: (1) eine Component muss den
  CType `html` targeten (`component_ce_target`, erzeugt den `COA_INT`-Block →
  uncached), und (2) das Element muss dieser Component über das Feld
  **Drittanbieter/Component** (`ce_consent_component`) zugeordnet sein. Fehlt das
  Uncaching-Setup, fällt das Element sicher auf das statische Entfernen zurück.

### 4.2 Google Consent Mode

- **Google Consent Mode Signale** (`consent_mode_signals`): Mehrfachauswahl,
  welche Signale diese Component bei Einwilligung auf `granted` setzt:
  - `analytics_storage` – Analytics (z. B. GA4)
  - `ad_storage`, `ad_user_data`, `ad_personalization` – Werbung (Google Ads)
  - `functionality_storage`, `personalization_storage`, `security_storage`
- Voraussetzung: **GTM-Container-ID** im Banner (Tab *Tracking / Consent Mode*).

### 4.3 Matomo

- Voraussetzung: **Matomo-URL + Site-ID** (oder MTM-Container-URL) im Banner.
- Matomo-Consent ist global (ein Tracker): sobald **irgendeine** Matomo-Component
  eingewilligt ist, wird `setConsentGiven`/`setCookieConsentGiven` gesendet.

### 4.4 Script laden

Im Tab **JavaScripts** werden die Snippets hinterlegt:

![Component JavaScripts-Tab](Images/05-component-javascripts.png)

- **accepted_script** – wird bei Einwilligung als `<script>` (mit CSP-Nonce)
  injiziert.
- **rejected_script** – wird bei Widerruf ausgeführt (Cleanup, z. B. Cookies
  löschen), das injizierte Script wird entfernt.

### 4.5 Cookie-Informationen (Dienst-Suche & Cookies)

Jede Component hat den Tab **Cookie-Informationen** mit den Dienst-Metadaten und der Cookie-Liste,
die im Frontend im Cookie-Informationsfenster angezeigt werden.

Das Feld **Service** bietet eine Autocomplete-Suche gegen die mitgelieferte Dienst-Datenbank
`Resources/Public/cookie_database.json` (durchsucht: Name, Kennung, Aliasse). Ein Klick auf einen
Vorschlag befüllt automatisch *Provider domains*, *Publisher*, *Privacy policy link*,
*Data collected* sowie – je Cookie des Dienstes – eine Inline-Zeile (Name, Typ, Zweck, Laufzeit,
Beschreibung).

![Cookie-Informationen – Dienst-Suche](Images/15-component-cookie-search.png)

![Cookie-Informationen – ausgefüllt](Images/16-component-cookie-information.png)

- **Nur Ausfüllhilfe:** Alle Werte bleiben editierbar; es entstehen reguläre TYPO3-Datensätze
  (keine Laufzeit-Referenz auf die JSON). Die Cookie-Zeilen werden erst beim **Speichern** der
  Component persistiert.
- **Sprache:** Übernommen wird der zur bearbeiteten Datensatz-Sprache passende Text (de/en,
  Fallback en → de).
- **Dienst-Datenbank erweitern:** Weitere Dienste lassen sich in `cookie_database.json` unter dem
  Schlüssel `services` ergänzen. Felder je Dienst: `service_name`, `service_identifier`,
  `service_aliases` (Array), `service_provider`, `service_publisher`, `service_privacy_link`,
  `service_data_collected` (`{de, en}`) und `cookies[]` mit `name`, `type` sowie
  `lifetime`/`purpose`/`description` (jeweils `{de, en}`).

---

## 5. Tracking / Consent Mode (Banner-Ebene)

Im Banner-Tab **Tracking / Consent Mode** werden die Tag-Manager/Tracker global
konfiguriert:

![Banner Tracking / Consent Mode](Images/03-banner-tracking.png)

- **Google Tag Manager Container-ID** – z. B. `GTM-XXXXXX`. Leer lassen, wenn
  kein GTM verwendet wird.
- **Matomo URL** – Basis-URL inkl. abschließendem `/`,
  z. B. `https://matomo.example.com/`.
- **Matomo Site-ID** – die `idSite` der Matomo-Property.
- **Matomo Tag Manager Container-URL** (optional) – wenn gesetzt, wird der
  MTM-Container statt des Standard-Trackers geladen.

Die Extension erzeugt daraus früh im `<head>` (vor den Tags):

- **Google:** gtag-Stub + `consent default` (alles `denied`, `security_storage`
  granted, `wait_for_update: 500`) + für wiederkehrende Besucher direkt ein
  `consent update` aus dem Cookie + den GTM-Loader.
- **Matomo:** `_paq` mit `requireConsent` + `requireCookieConsent` + Tracker
  bzw. MTM-Container; für wiederkehrende Besucher direkt `setConsentGiven`.

---

## 6. Verhalten im Frontend

- **Erstbesuch:** Consent-Signale stehen auf `denied`; Iframes werden durch
  Placeholder ersetzt; Scripts werden nicht geladen.
- **Einwilligung** (Banner „Alle akzeptieren"/Auswahl **oder** Toggle direkt im
  Placeholder): ohne Reload werden Iframes eingeblendet, gtag-/Matomo-Consent
  aktualisiert und Scripts injiziert.
- **Widerruf** (Banner erneut öffnen, Häkchen entfernen, speichern): Placeholder
  kehren zurück, Consent-Signale werden auf `denied` gesetzt, Matomo widerrufen
  und injizierte Scripts entfernt.
- **Wiederkehrende Besucher:** Der Consent-Status wird serverseitig aus dem
  Cookie gelesen und bereits im `<head>` angewandt, damit Tags korrekt starten.

Der Consent wird gespeichert in:
- **Cookie** `BbConsentPreferences` = `{ "<component_id>": true|false }`
- **localStorage** (reichhaltiger: Hash, Version, Timestamp, Services)

### 6.1 Cookie-Informationsfenster

Über „Cookie-Informationen anzeigen" öffnet sich ein responsives Overlay mit allen Diensten
(nach Gruppe/Dienst gegliedert). Je Dienst werden die Metadaten (Anbieter, Domains, erhobene
Daten, Datenschutz-Link) angezeigt, darunter unter der Überschrift **„Cookies"** eine Tabelle mit
den Spalten *Name, Type, Purpose, Lifetime, Description*. Das Layout ist responsiv (Smartphone
gestapelt, Tablet 2×2-Raster, Desktop volle Tabelle).

![Cookie-Informationsfenster (Desktop)](Images/10-frontend-cookie-info.png)

---

## 7. Content Security Policy (CSP)

- Die von der Extension ausgegebenen Inline-Scripts tragen eine CSP-Nonce.
- Von GTM/Matomo **nachgeladene** Scripts benötigen jedoch Domain-Freigaben in
  der CSP, u. a.:
  - `https://www.googletagmanager.com` (GTM)
  - die eigene Matomo-Domain
  - ggf. `https://www.youtube.com` / `https://www.youtube-nocookie.com` (Video)
- Ohne diese Freigaben blockt der Browser das Laden der Tags.

---

## 8. Screenshots aktualisieren

Die Backend-Screenshots in `Documentation/Images/` werden mit dem Skill
`backend-screenshot` (`.claude/skills/backend-screenshot/`) erzeugt – siehe
dessen `SKILL.md`.
