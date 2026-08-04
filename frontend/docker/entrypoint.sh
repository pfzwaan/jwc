#!/bin/sh
set -e

cd /app

# --include=dev is required even in production: the build tooling
# (@tailwindcss/postcss, tailwindcss, typescript) lives in devDependencies, and
# NODE_ENV=production would otherwise make npm omit it, so `next build` fails
# resolving the PostCSS plugin. Run unconditionally so a deploy that adds a
# dependency installs it instead of reusing a stale node_modules volume.
echo "Installing npm dependencies..."
npm install --include=dev

if [ "$NODE_ENV" = "production" ]; then
    npm run build
    exec npm run start -- --port 3002 --hostname 0.0.0.0
fi

exec npm run dev -- --port 3002 --hostname 0.0.0.0
