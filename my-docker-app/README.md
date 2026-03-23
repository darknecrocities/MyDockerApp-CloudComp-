# 🐳 Dockerized Player Management System

> A cloud-deployed, containerized web application for managing player data — built with NGINX, PHP, and MySQL, orchestrated via Docker Compose, and securely accessed through Tailscale VPN.

---

## 📋 Project Overview

This project is a **Dockerized web application** deployed on **AWS EC2 (Tokyo region)** that allows users to add, store, and view player records in a MySQL database through a clean web interface.

All services run inside **Docker containers**, ensuring portability, consistency, and easy deployment across any environment. Secure remote access is provided through **Tailscale VPN**, restricting the application to authorized users only.

---

## 🏗️ Architecture Overview

```
┌────────────┐       ┌────────────┐       ┌────────────┐
│            │       │            │       │            │
│   NGINX    │──────▶│  PHP-FPM   │──────▶│   MySQL    │
│  (Port 80) │       │ (Port 9000)│       │ (Port 3306)│
│            │       │            │       │            │
└────────────┘       └────────────┘       └────────────┘
   Web Server          Backend              Database
```

- **NGINX** serves static files (HTML, CSS, JS) and forwards PHP requests to PHP-FPM
- **PHP-FPM** processes backend logic, connects to MySQL, and returns responses
- **MySQL** stores player data persistently using Docker volumes
- **Docker Compose** orchestrates all three services within a shared network

---

## 📁 Project Structure

```
my-docker-app/
│
├── docker-compose.yml          # Service orchestration
├── init.sql                    # Database initialization script
├── README.md                   # Project documentation
│
├── nginx/
│   └── default.conf            # NGINX server configuration
│
├── php/
│   └── Dockerfile              # PHP-FPM image with extensions
│
└── www/
    ├── index.php               # Main application (backend + frontend)
    ├── style.css               # UI styling
    └── app.js                  # Client-side interactivity
```

---

## 🛠️ Technologies Used

| Technology       | Purpose                          |
|------------------|----------------------------------|
| **NGINX**        | Web server & reverse proxy       |
| **PHP 8.2 FPM**  | Backend processing               |
| **MySQL 5.7**    | Relational database              |
| **Docker**       | Containerization                 |
| **Docker Compose** | Multi-container orchestration  |
| **Tailscale VPN** | Secure remote access            |
| **AWS EC2**      | Cloud hosting (Tokyo region)     |
| **Amazon Linux** | Server operating system          |

---

## 🚀 Setup Instructions

### Step 1 — Launch an EC2 Instance

1. Log in to [AWS Management Console](https://console.aws.amazon.com/)
2. Navigate to **EC2** → **Launch Instance**
3. Select **Amazon Linux 2023 AMI**
4. Choose an instance type (e.g., `t2.micro` for free tier)
5. Set the region to **Asia Pacific (Tokyo) — ap-northeast-1**
6. Configure a security group:
   - Allow **SSH (Port 22)** from your IP
   - Allow **HTTP (Port 80)** for web access (or restrict to Tailscale)
7. Launch and connect via SSH

### Step 2 — Install Docker & Docker Compose

```bash
# Update the system
sudo yum update -y

# Install Docker
sudo yum install -y docker
sudo systemctl start docker
sudo systemctl enable docker

# Add your user to the docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verify installation
docker --version
docker-compose --version
```

> **Note:** Log out and log back in for the group changes to take effect.

### Step 3 — Clone or Copy Project Files

```bash
# Clone from repository
git clone <your-repo-url> my-docker-app
cd my-docker-app

# OR copy files manually via SCP
scp -i your-key.pem -r ./my-docker-app ec2-user@<EC2-IP>:~/
```

### Step 4 — Build and Start the Application

```bash
cd my-docker-app
docker-compose up -d --build
```

### Step 5 — Access the Application

Open your browser and navigate to:

```
http://<EC2-PUBLIC-IP>
```

Or via Tailscale VPN:

```
http://<TAILSCALE-IP>
```

---

## 🗄️ Database Configuration

| Parameter         | Value              |
|-------------------|--------------------|
| **Database Name** | `haumonstersDB`    |
| **Table Name**    | `playerstbl`       |
| **User**          | `dbmanager`        |
| **Password**      | `6cloudcom123!`    |
| **Root Password** | `root`             |
| **Host**          | `mysql` (container name) |

### Table Schema

```sql
CREATE TABLE playerstbl (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);
```

### Sample Data

```sql
INSERT INTO playerstbl (name, password) VALUES ('PlayerOne', 'pass1234');
INSERT INTO playerstbl (name, password) VALUES ('ShadowKnight', 'knight@99');
```

---

## ✨ Application Features

- ➕ **Add Player** — Submit a player name and password through a styled form
- 👥 **View Players** — Display all registered players in a dynamic table
- 🎨 **Modern UI** — Clean, dark-themed responsive design
- ⚡ **AJAX Support** — Add players without page reload using Fetch API
- 🔗 **Database Integration** — Full CRUD connectivity to MySQL via PHP PDO

---

## ⚙️ API / Backend Logic

The PHP backend connects to MySQL using `PDO` with the hostname `mysql` (the Docker service name resolves automatically within the Docker network).

**Connection Example:**

```php
$pdo = new PDO("mysql:host=mysql;dbname=haumonstersDB;charset=utf8mb4", "dbmanager", "6cloudcom123!");
```

**Endpoints:**

| Method | URL                       | Description              |
|--------|---------------------------|--------------------------|
| `GET`  | `/`                       | Renders the full page    |
| `GET`  | `/?api=players`           | Returns players as JSON  |
| `POST` | `/` (JSON body)           | Adds a player via API    |
| `POST` | `/` (form data)           | Adds a player via form   |

---

## 🔒 Security Implementation

### Tailscale VPN

- **Tailscale** creates a secure, encrypted mesh network between authorized devices
- Only devices connected to the same Tailscale network can access the application
- No public IP exposure required — access via Tailscale IP only

### AWS Security Group

- **Inbound rules** are restricted to:
  - SSH (Port 22) — your IP only
  - HTTP (Port 80) — Tailscale network or specific IPs
- All other traffic is **denied by default**

> ⚠️ This ensures the application is **not publicly accessible** without VPN authorization.

---

## 📸 Screenshots

> Replace the placeholders below with actual screenshots of the running application.

![Application UI](./screenshots/app.png)

![Add Player Form](./screenshots/add-player.png)

![Player List](./screenshots/player-list.png)

---

## 🔄 How to Stop / Start the Application

**Stop all containers:**

```bash
docker-compose down
```

**Start containers (without rebuilding):**

```bash
docker-compose up -d
```

**Rebuild and restart:**

```bash
docker-compose up -d --build
```

**Stop and remove all data (including database):**

```bash
docker-compose down -v
```

---

## 🐞 Common Issues & Fixes

### ❌ MySQL Connection Refused

**Cause:** PHP container starts before MySQL is fully ready.

**Fix:** Wait a few seconds and refresh, or restart:

```bash
docker-compose restart php
```

---

### ❌ Port 80 Already in Use

**Cause:** Another service is using port 80.

**Fix:** Stop the conflicting service or change the port mapping in `docker-compose.yml`:

```yaml
ports:
  - "8080:80"
```

---

### ❌ Permission Denied on Docker Commands

**Cause:** Current user is not in the `docker` group.

**Fix:**

```bash
sudo usermod -aG docker $USER
# Then log out and log back in
```

---

### ❌ Database Not Initialized

**Cause:** `init.sql` only runs on the first container startup with an empty volume.

**Fix:** Remove the volume and rebuild:

```bash
docker-compose down -v
docker-compose up -d --build
```

---

## 👨‍💻 Authors

| Name              | Role                |
|-------------------|---------------------|
| *[Student Name 1]* | Developer / DevOps |
| *[Student Name 2]* | Developer / DevOps |

---

## 📄 License

This project is developed for **academic purposes** as part of a cloud computing course. It is intended for educational use only and is not licensed for commercial distribution.

---

<p align="center">
  <b>Built with 🐳 Docker · ⚙️ NGINX · 🐘 PHP · 🐬 MySQL</b>
</p>
