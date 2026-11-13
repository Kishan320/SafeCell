# SafeCell

SafeCell is an advanced e-commerce platform built with Laravel 12, PHP 8.4, Inertia.js, and React.

---

## 🐳 Docker Setup

### Prerequisites
- Docker Engine & Docker Compose

### 1. Quick Start with Docker Compose
Clone the repository and spin up the full stack (Application, MySQL 8.0, and Redis):

```bash
# Start all containers in the background
docker compose up -d --build
```

- **Application Web**: [http://localhost:8302](http://localhost:8302)
- **MySQL**: `localhost:3306` (Database: `safecell`, User: `safecell`, Pass: `safecell123`)
- **Redis**: `localhost:6379`

> **Note:** On first startup, MySQL automatically loads the initial schema from `installation/backup/database.sql`.

### 2. Standalone Docker Build & Run
You can also build and run the production container standalone:

```bash
# Build the Docker image
docker build -t safecell:latest .

# Run the container (maps port 8000 to port 80)
docker run -d --name safecell -p 8000:80 safecell:latest
```

### 3. Deployments (Railway, Render, Fly.io, Cloud Run)
The Dockerfile is fully optimized for cloud platform deployments:
- Uses multi-stage builds (Node.js builds Vite assets, PHP 8.4 + Nginx serves the app).
- Dynamically respects the `$PORT` environment variable injected by hosting providers.
- Supervises both Nginx and PHP-FPM with auto-restarts and logging to stdout/stderr.
