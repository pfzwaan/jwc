#!/bin/sh
set -e

cd /app

if [ ! -d node_modules ] || [ -z "$(ls -A node_modules 2>/dev/null)" ]; then
    echo "Installing npm dependencies..."
    npm install
fi

if [ "$NODE_ENV" = "production" ]; then
    npm run build
    exec npm run start -- --port 3002 --hostname 0.0.0.0
fi

exec npm run dev -- --port 3002 --hostname 0.0.0.0
