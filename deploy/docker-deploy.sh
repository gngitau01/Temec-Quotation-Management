#!/usr/bin/env bash
set -euo pipefail

# ------------------------------------------------------------------
# Temec Quotation Management - Docker Deployment Script
#
# Usage:
#   ./deploy/docker-deploy.sh              # Build & start (no seeding)
#   ./deploy/docker-deploy.sh --seed       # Build & start with DB seeding
#   ./deploy/docker-deploy.sh --down       # Stop and remove containers
#   ./deploy/docker-deploy.sh --rebuild    # Force rebuild and restart
#   ./deploy/docker-deploy.sh --logs       # Tail application logs
# ------------------------------------------------------------------

PROJECT_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$PROJECT_ROOT"

ACTION="up"
SEED="false"

for arg in "$@"; do
    case $arg in
        --seed)    SEED="true" ;;
        --down)    ACTION="down" ;;
        --rebuild) ACTION="rebuild" ;;
        --logs)    ACTION="logs" ;;
        *)         echo "Unknown option: $arg"; exit 1 ;;
    esac
done

# ---- Check prerequisites ----
if ! command -v docker >/dev/null 2>&1; then
    echo "Error: docker is not installed."
    exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
    echo "Error: docker compose (v2) is not available."
    exit 1
fi

case $ACTION in
    down)
        echo "==> Stopping containers..."
        docker compose down
        echo "Done. Containers stopped."
        ;;

    logs)
        echo "==> Tailing logs (Ctrl+C to stop)..."
        docker compose logs -f app
        ;;

    rebuild)
        echo "==> Rebuilding and restarting..."
        DB_SEED="$SEED" docker compose down
        DB_SEED="$SEED" docker compose build --no-cache
        DB_SEED="$SEED" docker compose up -d
        echo ""
        echo "==> Rebuild complete. Application running at http://localhost:8000"
        ;;

    up)
        echo "==> Building and starting Temec..."
        echo ""

        # Build images
        echo "--- Building Docker images ---"
        DB_SEED="$SEED" docker compose build

        echo ""
        echo "--- Starting containers ---"
        DB_SEED="$SEED" docker compose up -d

        echo ""
        echo "--- Waiting for application to be ready ---"
        retries=0
        max_retries=60
        until curl -sf http://localhost:8000 > /dev/null 2>&1 || [ $retries -ge $max_retries ]; do
            retries=$((retries + 1))
            printf "."
            sleep 2
        done
        echo ""

        if [ $retries -ge $max_retries ]; then
            echo "Warning: Application may not be fully ready yet."
            echo "Check logs with: ./deploy/docker-deploy.sh --logs"
        else
            echo "Application is up and running."
        fi

        echo ""
        echo "========================================="
        echo "  Temec is running at:"
        echo "  http://localhost:8000"
        echo ""
        echo "  MySQL is available at:"
        echo "  Host: localhost:3307"
        echo "  Database: temec"
        echo "  User: temec_user"
        echo "========================================="
        echo ""
        echo "Useful commands:"
        echo "  ./deploy/docker-deploy.sh --logs      # View logs"
        echo "  ./deploy/docker-deploy.sh --down      # Stop everything"
        echo "  ./deploy/docker-deploy.sh --rebuild    # Rebuild from scratch"
        echo "  docker compose exec app php artisan tinker  # Laravel REPL"
        ;;
esac
