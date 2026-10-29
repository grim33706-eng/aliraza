#!/usr/bin/env bash
# Builds the portfolio for Wasmer Edge and (with --deploy) deploys it.
#   bash deploy/wasmer/build.sh            build only, into deploy/wasmer/build
#   bash deploy/wasmer/build.sh --deploy   build, then deploy to Wasmer Edge
# Run from Git Bash on Windows (or any bash). Only project files that git doesn't
# ignore are packaged, so .env, node_modules, vendor and local uploads stay here.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
HERE="$ROOT/deploy/wasmer"
OUT="$HERE/build"
WASMER="${WASMER:-wasmer}"
command -v "$WASMER" >/dev/null 2>&1 || WASMER="$HOME/.wasmer/bin/wasmer"

echo "→ Copying the app into $OUT"
rm -rf "$OUT"
mkdir -p "$OUT"
cd "$ROOT"
git ls-files -z --cached --others --exclude-standard -- . ':!tests' ':!deploy' \
    | tar --null -T - -cf - | tar -xf - -C "$OUT"

echo "→ Building the CSS/JS"
npm run build
cp -r public/build "$OUT/public/build"

echo "→ Installing PHP packages (production only)"
cd "$OUT"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress
# The app's files are read-only on Wasmer, so Laravel's provider caches must be built here.
php artisan package:discover --ansi >/dev/null
test -f bootstrap/cache/packages.php && test -f bootstrap/cache/services.php

cp "$HERE/wasmer.toml" "$HERE/app.yaml" "$HERE/router.php" .
cp -r "$HERE/php-config" .
rm -rf node_modules

echo "✓ Package ready in $OUT ($(du -sh . | cut -f1))."

if [ "${1:-}" = "--deploy" ]; then
    "$WASMER" deploy
    # `wasmer deploy` records the app's owner and id in app.yaml; keep that for next time.
    cp app.yaml "$HERE/app.yaml"

    # The database is migrated by the app itself on the first visit (AUTO_MIGRATE).
    APP_URL="$(sed -n 's/^ *APP_URL: *//p' app.yaml)"
    echo "✓ Deployed. Open $APP_URL once to finish setting up the database."
fi
