#!/bin/bash
# Double-click this file (macOS) to run BoatRent Cyprus locally with clean URLs.
cd "$(dirname "$0")" || exit 1
PORT=8011
echo "──────────────────────────────────────────────"
echo "  BoatRent Cyprus  →  http://localhost:$PORT"
echo "  (clean URLs enabled via router.php)"
echo "  Press Ctrl+C to stop."
echo "──────────────────────────────────────────────"
php -S "localhost:$PORT" router.php
