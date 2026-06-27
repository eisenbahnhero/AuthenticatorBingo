# Authenticator Bingo 🎯

A fun team game that turns your daily MFA routine into a bingo competition. Every time someone authenticates with Microsoft Authenticator (or any app that shows a two-digit push code), they submit the number — and everyone's bingo card gets marked automatically.

---

## How It Works

When you log in with an authenticator app, you're shown a two-digit number (10–99) to confirm the push notification. Instead of just tapping "Approve" and forgetting it, you enter that number into Authenticator Bingo. The number gets logged and marked on every player's card — whoever completes a row, column, or diagonal first scores a **BINGO**.

A new round starts every month with fresh cards.

---

## Screenshots

<img src="docs/imgs/img_start.png" width="400">
<img src="docs/imgs/img_player.png" width="400">
<img src="docs/imgs/img_overview.png" width="400">

---

## Features

- **5×5 bingo cards** with numbers 10–99 and a FREE center square
- **Auto-generated or custom cards** — players can pick their own numbers or get a random card
- **Real-time shared state** — submitted numbers are marked for all players simultaneously
- **Monthly rounds** — each month is an independent game saved as a JSON file
- **Leaderboard & history** — track bingos per round and across all rounds
- **Archive** — browse all past months
- **Access control** — whitelist specific players via IP or Windows authentication
- **Exchange directory** — lightweight file-based mechanism for broadcasting new numbers to all clients

---

## Tech Stack

- **PHP** (no framework, no database)
- **JSON files** for game state persistence
- **Plain CSS + vanilla JS** for the frontend
- Runs on any standard web server (Apache, Nginx, etc.)

---

## Project Structure

```
AuthenticatorBingo/
├── index.php              # Entry point, routing, and game loop
├── src/
│   ├── config.php         # All configuration settings
│   ├── auth.php           # Player identification (IP or Windows auth)
│   ├── game.php           # Game class: card generation, marking, bingo detection
│   └── stylesheet.css     # Application styles
├── pages/
│   ├── chooser.php        # Card selection screen for new players
│   ├── game.php           # Active bingo card view
│   ├── history.php        # Round history and leaderboard
│   ├── overall.php        # All-time overall standings
│   ├── archiv.php         # Archive of past rounds
│   ├── rules.php          # How to play
│   └── no-access.php      # Shown when ACL blocks a user
├── data/                  # Monthly game state (e.g. 2026-06.json)
├── exchange/              # Temporary files for broadcasting new numbers
└── favicon/               # App icons
```

---

## Setup

### Requirements

- PHP 8.0+
- A web server with PHP support (Apache, Nginx, IIS, or `php -S` for local use)
- Write permissions on the `data/` and `exchange/` directories (created automatically on first run)

### Installation

1. Clone or copy the project to your web server's document root (or a subdirectory).
2. Open `src/config.example.php` and adjust the settings for your environment (see below).
3. Rename `src/config.example.php` to `src/config.php`
4. Make sure the web server process can write to the project directory.
5. Open the app in your browser — the `data/` and `exchange/` directories are created automatically on the first visit. Create an config file for your webserver to protect them.

---

## Configuration

All settings live in `src/config.php`:

```php
# Title displayed in the browser and header
$config["title"] = "Authenticator Bingo";

# Directories for game state and exchange files
$config["data_dir"]     = "data/";
$config["exchange_dir"] = "exchange/";
$config["write_alerts_to_exchange_dir"] = true;

# Authentication mode: "IP" or "Windows"
$config["auth_mode"] = "IP";

# Access control list
$config["use_acl"] = true;
$config["acl_allowed_players"] = array(
    "192_168_1_10",
    "192_168_1_11",
    // add more players here
);
```

### Authentication Modes

| Mode | How players are identified |
|---|---|
| `IP` | Player's IP address (dots replaced with underscores, e.g. `192_168_1_10`) |
| `Windows` | Windows username from `REMOTE_USER` (e.g. via IIS Windows Authentication) |

### Access Control

Set `use_acl` to `true` and list the allowed player identifiers in `acl_allowed_players`. Anyone not on the list sees the no-access page. Set `use_acl` to `false` to allow anyone who can reach the server.

---

## Gameplay

1. **Join the round** — visit the app and register for the current month. You'll receive a randomly generated 5×5 bingo card, or you can pick your own numbers.
2. **Submit numbers** — whenever your authenticator shows a two-digit push code, enter it into the app before anyone else does.
3. **Cards get marked** — once a number is submitted, it's automatically marked on every player's card where it appears.
4. **Score bingos** — complete a full row, column, or diagonal to score a BINGO. Multiple bingos per round are possible.
5. **Check the leaderboard** — the history tab shows who's submitted which numbers and the current bingo standings.

The FREE square in the center (position [2][2]) is always marked.

---

## Data Storage

Game state is stored as JSON files in the `data/` directory, one file per month (e.g. `data/2026-06.json`). No database is required.

The `exchange/` directory is used to propagate newly submitted numbers to all connected clients without requiring a database or WebSocket server. These files are small and temporary.

---

## License

This project is licensed under the MIT License – see the LICENSE file for details.