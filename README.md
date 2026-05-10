# <span style="color:#f97316">Gym</span><span style="color:#a1a1aa">Mate</span>

<p align="center">
  <img alt="Laravel 12" src="https://img.shields.io/badge/Laravel-12-ff2d20?style=flat-square">
  <img alt="Livewire 3" src="https://img.shields.io/badge/Livewire-3-8b5cf6?style=flat-square">
  <img alt="Tailwind CSS" src="https://img.shields.io/badge/Tailwind-3-38bdf8?style=flat-square">
  <img alt="Alpine.js" src="https://img.shields.io/badge/Alpine.js-3-77c1d2?style=flat-square">
  <img alt="SQLite" src="https://img.shields.io/badge/SQLite-3-003b57?style=flat-square">
</p>

GymMate ist eine mobile-first Fitness-Tracking-App auf Laravel 12. Nutzer erfassen Trainingseinheiten und Cardio-Sessions, analysieren ihr Volumen und ihren Fortschritt, planen ihre Trainingswoche und können anderen Nutzern folgen. Alle Daten bleiben lokal – kein Cloud-Zwang, kein Abo.

## 🟧 Tech-Stack
- Laravel 12, PHP 8.2
- Livewire 3 (reaktive UI ohne eigene JS-Komponenten)
- Alpine.js (Sidebar-Drawer, Modals, Theme-Toggle)
- Tailwind CSS + Vite
- SQLite (kein separater Datenbankserver nötig)
- Spatie Media Library (Standort-Bilder, Profilfoto)

## 🎨 Design
- Mobile-first, optimiert für Smartphone-Nutzung im Gym
- Durchgängiges Orange-Akzent-Schema (`#f97316`)
- Light- und Dark-Mode, Toggle im Sidebar, gespeichert in `localStorage`
- Sidebar als Drawer mit Touch-Swipe-Geste (links/rechts wischen)
- Runde Karten-UI, Mono-Labels für Statistik-Felder, dezente Glow-Effekte

## 🟩 Funktionen im Detail

### 🟩 Standorte
- Beliebig viele Gyms/Standorte anlegen, umbenennen und löschen
- Profilbild pro Standort (Spatie Media Library, Upload per Livewire)
- Beim Erstellen eines Standorts werden automatisch 5 Trainingspläne angelegt: **Schulter, Arme, Rücken, Brust, Beine**
- Dashboard zeigt pro Standort: Session-Anzahl, letzter Besuch, Aktivitäts-Heatmap des aktuellen Monats (Punkte-Grid)

### 🟧 Trainingspläne & Übungen
- Pro Standort mehrere Trainingspläne (Muskelgruppen), editierbar und löschbar
- Pro Trainingsplan beliebig viele Übungen mit Name, Ziel-Sätze und Ziel-Wiederholungen
- Übungen können umbenannt, umsortiert und gelöscht werden

### 🟨 Workout Logging
- Livewire-Komponente zum Eintragen von Sessions direkt bei der Übung
- Pro Session: Gewicht (kg), Wiederholungen, Sätze, Zeitstempel
- Vergangene Sessions editierbar und löschbar
- Volumen-Berechnung berücksichtigt bilateral/unilateral (Sätze × Wiederholungen × Gewicht)

### 🟦 Wochenplan
- Trainingsplan für jeden Wochentag festlegen (Montag–Sonntag)
- Übungen per Livewire zuweisen oder Tag als Rest Day markieren
- Heutiger Tag wird farblich hervorgehoben
- Wochenplan erscheint auch auf dem öffentlichen Profil

### 🟧 Cardio
- Cardio-Sessions erfassen: Aktivität (Laufen, Radfahren, Schwimmen etc.), Dauer (Minuten), verbrannte Kalorien
- Kalorienberechnung optional (eigener Wert oder leer lassen)
- Liste aller Cardio-Sessions mit Lösch-Funktion

### 🟨 Analyse
- Gewichtsverlauf über Zeit (Chart)
- Gesamtvolumen pro Woche oder Monat
- Streak-Berechnung: Anzahl aufeinanderfolgender Wochen mit mindestens einer Trainingseinheit
- Lieblingsstandort (nach Anzahl Sessions)

### 🟩 Öffentliches Profil (`/u/{name}`)
- Jeder Account hat eine öffentlich einsehbare Profilseite
- Anzeige: Avatar, Name, Land, Alter, Körpergröße, Gewicht, Mitglied seit
- Statistik-Grid: Anzahl Sessions, Wochen-Streak 🔥, Gesamtvolumen (t/kg), Lieblingsstandort, BMI (farbcodiert), Lieblingsübung
- Aktivitäts-Heatmap des aktuellen Monats (Punkte-Raster)
- Letztes Training (relativ + Datum) parallel zur Heatmap
- Wochenplan als interaktiver Tages-Wechsler (Alpine.js)
- Eigenes Profil zeigt Bearbeiten-Button oben rechts

### 🟪 Following
- Andere Nutzer suchen und ihnen folgen (Live-Suche per Livewire, 300ms Debounce)
- Following-Liste auf `/following` mit Unfollow-Funktion
- Kein gegenseitiges Follow nötig (einseitig)

### 🟦 Einstellungen (`/einstellungen`)
- Profilfoto hochladen (bis 2 MB, JPG/PNG/WebP) – Vorschau sofort sichtbar
- Benutzername und E-Mail ändern
- Körperdaten: Gewicht, Größe, Geschlecht, Geburtstag, Land
- Passwort ändern
- Konto löschen (mit Passwortbestätigung und Bestätigungs-Dialog)

### 🟩 Onboarding
- Neue Nutzer werden nach der Registrierung zu einem einmaligen Onboarding-Formular geleitet
- Abfrage: Gewicht, Größe, Geschlecht – Basis für Kalorienberechnung und BMI

### 🟧 Export / Import
- Workouts als JSON exportieren
- Cardio-Sessions als JSON exportieren
- Import aus zuvor exportierter Datei (JSON)

## 🟦 Datenmodell und Relationen

```
User 1--* Location 1--* TrainingPlan 1--* Exercise 1--* WorkoutSession
User 1--* CardioSession
User 1--* WeeklySchedule (pro Wochentag)
WeeklySchedule *--* Exercise (über weekly_schedule_exercises)
User *--* User (follows: follower_id / following_id)
Location 1--* Media (Spatie)
User    1--* Media (Spatie, Avatar)
```

Tabellen im Überblick:
- `users` – inkl. weight_kg, height_cm, gender, birthday, country
- `locations` – user_id, name
- `training_plans` – location_id, name
- `exercises` – training_plan_id, name, target_sets, target_reps
- `exercise_logs` – exercise_id, weight_kg, reps, sets, logged_at
- `weekly_schedules` – user_id, day_of_week, is_rest
- `weekly_schedule_exercises` – weekly_schedule_id, exercise_id
- `cardio_sessions` – user_id, activity, duration_minutes, calories_burned, logged_at
- `follows` – follower_id, following_id (unique)
- `media` – Spatie Media Library

## 🟩 Lokales Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run dev
php artisan serve
```

Kein Datenbank-Server nötig – SQLite läuft out of the box.

## 🟧 Produktionsserver Setup (Ubuntu + Nginx)

1) Pakete installieren
```bash
sudo apt update
sudo apt install -y nginx git unzip php8.2-fpm php8.2-cli php8.2-sqlite3 php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd
```

2) Projekt ausrollen
```bash
git clone <repo-url> /var/www/gymmate
cd /var/www/gymmate
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force
```

3) Assets bauen
```bash
npm install
npm run build
```

4) Rechte setzen
```bash
sudo chown -R www-data:www-data /var/www/gymmate
sudo chmod -R 775 storage bootstrap/cache database
```

5) Nginx-Konfiguration
```nginx
server {
    server_name example.com;
    root /var/www/gymmate/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

6) Optimierung
```bash
php artisan optimize
```

## 🟨 Konfiguration (.env) – wichtige Keys

Minimal:
- `APP_ENV`, `APP_KEY`, `APP_URL`
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=/absoluter/pfad/zu/database/database.sqlite`

Optional:
- `MAIL_*` – für E-Mail-Verifizierung und Passwort-Reset
- `FILESYSTEM_DISK` – für Spatie Media Library (default: `public`)

## 🟩 Betrieb und Wartung
- Nach Deploy: `php artisan optimize:clear`
- Bei Schema-Änderungen: `php artisan migrate --force`
- Bei geänderten Assets: `npm run build`
- Spatie Media Library Storage-Link: `php artisan storage:link`

## 🟥 Sicherheitshinweise
- `APP_DEBUG=false` in Produktion setzen
- SQLite-Datei außerhalb des `public/`-Verzeichnisses halten (Standard bereits korrekt)
- `.env` niemals in die Versionskontrolle aufnehmen
