# Quick Start Guide

This guide will help you get the Geo Ops Platform up and running in minutes.

## Prerequisites Check

Before starting, ensure you have:

- ✅ Node.js 18 or higher (`node --version`)
- ✅ PostgreSQL 13 or higher (`psql --version`)
- ✅ Docker and Docker Compose (optional, recommended)
- ✅ Git

## Option 1: Quick Start with Docker (Recommended)

The fastest way to get started is using Docker:

```bash
# 1. Clone the repository
git clone <repository-url>
cd geo-ops-platform

# 2. Start all services
docker-compose up -d

# 3. Check services are running
docker-compose ps

# Expected output:
# geo-ops-backend   running   0.0.0.0:3000->3000/tcp
# geo-ops-postgres  running   0.0.0.0:5432->5432/tcp

# 4. View logs
docker-compose logs -f backend

# 5. Test the API
curl http://localhost:3000/health
```

The backend API is now running at `http://localhost:3000`

### Setup Mobile App

```bash
# 1. Navigate to mobile directory
cd mobile

# 2. Install dependencies
npm install

# 3. Start Expo development server
npm start

# 4. Scan QR code with Expo Go app (iOS/Android)
# OR press 'i' for iOS simulator, 'a' for Android emulator
```

## Option 2: Manual Setup

### Backend Setup

```bash
# 1. Navigate to backend directory
cd backend

# 2. Install dependencies
npm install

# 3. Setup environment variables
cp .env.example .env

# Edit .env with your settings (if needed)
nano .env

# 4. Create PostgreSQL database
createdb geo_ops_platform

# 5. Run database migrations
psql -d geo_ops_platform -f src/database/migrations/001_initial_schema.sql

# 6. Start development server
npm run dev

# Server should start on http://localhost:3000
```

### Mobile App Setup

```bash
# 1. Navigate to mobile directory
cd mobile

# 2. Install dependencies
npm install

# 3. Create .env file (optional)
echo "EXPO_PUBLIC_API_URL=http://localhost:3000/api/v1" > .env

# For Android emulator, use:
# echo "EXPO_PUBLIC_API_URL=http://10.0.2.2:3000/api/v1" > .env

# 4. Start Expo development server
npm start

# 5. Run on device/emulator
# - Press 'i' for iOS simulator
# - Press 'a' for Android emulator
# - Scan QR code with Expo Go app
```

## Verify Installation

### Test Backend API

```bash
# Health check
curl http://localhost:3000/health

# Expected response:
# {"status":"ok","timestamp":"...","uptime":...}

# Register a test user
curl -X POST http://localhost:3000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "firstName": "John",
    "lastName": "Doe",
    "phone": "+94771234567"
  }'

# Expected response:
# {
#   "status": "success",
#   "data": {
#     "user": {...},
#     "token": "eyJhbGc..."
#   }
# }
```

### Test Mobile App

1. Open Expo Go app on your phone
2. Scan the QR code from terminal
3. App should load and show login screen
4. Register with a test account
5. You should see the home dashboard

## Common Issues and Solutions

### Issue: Cannot connect to database

```bash
# Check if PostgreSQL is running
sudo service postgresql status

# Start PostgreSQL if not running
sudo service postgresql start

# Test connection
psql -U postgres -d geo_ops_platform -c "SELECT 1"
```

### Issue: Port 3000 already in use

```bash
# Find process using port 3000
lsof -i :3000

# Kill the process
kill -9 <PID>

# OR change port in backend/.env
PORT=3001
```

### Issue: Mobile app cannot connect to API

**For Android Emulator:**
```bash
# Use emulator IP instead of localhost
# In mobile/.env or app.json:
EXPO_PUBLIC_API_URL=http://10.0.2.2:3000/api/v1
```

**For iOS Simulator:**
```bash
# localhost should work
EXPO_PUBLIC_API_URL=http://localhost:3000/api/v1
```

**For Physical Device:**
```bash
# Use your computer's local IP
# Find your IP: ifconfig (Mac/Linux) or ipconfig (Windows)
EXPO_PUBLIC_API_URL=http://192.168.1.X:3000/api/v1
```

### Issue: Expo app not loading

```bash
# Clear cache and restart
cd mobile
rm -rf node_modules
npm install
npx expo start -c
```

## Default Credentials

After registration, you can use these test accounts:

| Email | Password | Role |
|-------|----------|------|
| admin@geoops.com | admin123 | Admin |
| owner@geoops.com | owner123 | Owner |
| driver@geoops.com | driver123 | Driver |

**Note:** Create these accounts through the registration API or app.

## Next Steps

1. ✅ **Explore the API**
   - Check `/backend/README.md` for API documentation
   - Test endpoints with Postman or cURL

2. ✅ **Use the Mobile App**
   - Register an account
   - Try the measurement feature (UI ready)
   - Check your measurement history
   - View your profile

3. ✅ **Development**
   - Backend: `cd backend && npm run dev`
   - Mobile: `cd mobile && npm start`
   - Watch logs for errors

4. ✅ **Read Documentation**
   - `README.md` - Project overview
   - `ARCHITECTURE.md` - System architecture
   - `backend/README.md` - Backend API docs
   - `mobile/README.md` - Mobile app docs

## Useful Commands

### Backend

```bash
cd backend

# Development
npm run dev          # Start dev server with hot reload
npm run build        # Build for production
npm start            # Start production server

# Testing
npm test             # Run tests
npm run test:watch   # Watch mode
npm run test:cov     # Coverage report

# Code Quality
npm run lint         # Lint code
```

### Mobile

```bash
cd mobile

# Development
npm start            # Start Expo dev server
npm run ios          # Run on iOS
npm run android      # Run on Android

# Code Quality
npm run lint         # Lint code
npm run type-check   # TypeScript check
```

### Docker

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Rebuild
docker-compose build --no-cache

# Clean up
docker-compose down -v
```

## Support

For issues or questions:

1. Check the documentation in respective README files
2. Review `ARCHITECTURE.md` for system design
3. Check logs: `docker-compose logs -f` or `npm run dev` output
4. Verify environment variables are set correctly

## Development Workflow

```bash
# 1. Start backend
cd backend && npm run dev

# 2. In another terminal, start mobile app
cd mobile && npm start

# 3. Make changes and test
# - Backend: Changes auto-reload
# - Mobile: Shake device or Cmd+M (Android) / Cmd+D (iOS) to reload

# 4. Commit changes
git add .
git commit -m "Description of changes"
git push
```

## Production Deployment

See individual README files for production deployment:
- Backend: `backend/README.md`
- Mobile: `mobile/README.md`

Enjoy building with Geo Ops Platform! 🚀
