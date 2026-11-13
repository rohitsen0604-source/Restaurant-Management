# Render Deployment Guide (Restaurant Management System)

Yeh project **CodeIgniter 3 (PHP 8.0)** par based hai. Render par PHP natively available nahi hota, isliye ise **Docker** ke through deploy kiya gaya hai.

---

## Step 1: Database Setup (Database Server Par Import Karna)

Aapke pass jo remote MySQL database server hai (jaise Aiven, TiDB, Clever Cloud, Railway, AWS RDS, ya koi aur hosting):

1. Apne MySQL server par ek database banayein (e.g. `db_reservation` ya jo bhi naam aap chahein).
2. Repo mein `cianos_system/cianos_system/db/db_reservation.sql` file di gayi hai.
3. Is SQL file ko apne database mein **Import / Execute** karein:
   - Agar phpMyAdmin hai: Direct `Import` tab se file upload karein.
   - Ya command line se:
     ```bash
     mysql -h <DB_HOST> -u <DB_USER> -p <DB_NAME> < cianos_system/cianos_system/db/db_reservation.sql
     ```
4. **Default Admin Login**:
   - **Username**: `admin`
   - **Password**: `admin`

---

## Step 2: Code Ko GitHub Par Push Karein

Aapke local system par saari deployment files (`Dockerfile`, `docker-entrypoint.sh`, `render.yaml`, aur updated config files) ready hain. Inhe GitHub par push karein:

```bash
git add .
git commit -m "Configure Docker and Render deployment with dynamic database env vars"
git push origin main
```

---

## Step 3: Render Par Deploy Karein

### Method A: Blueprints (Sabse Simple - 1 Click)

1. [dashboard.render.com](https://dashboard.render.com/) par login karein.
2. **Blueprints** par click karein -> **New Blueprint Instance**.
3. Apna GitHub repo `Restaurant-Management` select karein.
4. Render automatically `render.yaml` ko detect karega aur aapse Database credentials puchega.
5. Apne database ke credentials fill karein aur **Apply** par click karein.

---

### Method B: Manual Web Service

1. [dashboard.render.com](https://dashboard.render.com/) par jayein.
2. **New +** -> **Web Service** par click karein.
3. Apna GitHub repository connect karein: `Restaurant-Management`.
4. Settings check karein:
   - **Name**: `restaurant-management` (ya koi bhi naam)
   - **Region**: Frankfurt / Singapore / Oregon (apne database ke paas wala region chunein)
   - **Branch**: `main`
   - **Runtime**: **Docker** (Render khud Dockerfile detect kar lega)
   - **Instance Type**: **Free**
5. **Environment Variables** section mein jayein aur yeh variables add karein:

| Key | Example Value | Description |
|---|---|---|
| `DB_HOST` | `mysql-xxxx.aivencloud.com` | Aapke MySQL server ka host/IP |
| `DB_USER` | `avnadmin` ya `root` | Database ka username |
| `DB_PASS` | `your_secret_password` | Database ka password |
| `DB_NAME` | `db_reservation` | Database ka naam |
| `DB_PORT` | `3306` | Port (default 3306 ya custom) |
| `DB_SSL` | `true` ya `false` | Agar cloud DB SSL require karta hai to `true`, warna `false` |
| `CI_ENV` | `production` | Production environment |

> **Note**: Agar aapke database provider ne single connection URL diya hai (jaise `mysql://user:pass@host:port/dbname`), toh aap direct **`DATABASE_URL`** key bhi use kar sakte hain.

6. **Create Web Service** par click karein!

Render build complete karega aur aapko ek live URL de dega (jaise `https://restaurant-management-xxxx.onrender.com`).

