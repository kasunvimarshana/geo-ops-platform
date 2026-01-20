# GeoOps Platform

Production-ready mobile and web platform for GPS-based land measurement and agricultural field services. Built with React Native (Expo, TypeScript) and Laravel (LTS). Supports walk-around and polygon GPS measurement, maps, driver tracking, job management, billing, expenses, payments, subscriptions, and Bluetooth ESC/POS printing with PDF fallback.

## 🚀 Features

### GPS Land Measurement

- **Walk-Around Measurement**: Walk the perimeter of a field while GPS tracks your path
- **Polygon Measurement**: Manually place points on a map to define field boundaries
- **High Accuracy**: Configurable GPS accuracy with battery optimization
- **Real-Time Calculations**: Automatic area and perimeter calculations using geospatial algorithms
- **Offline Support**: Store measurements locally and sync when online

### Field Management

- Create and manage agricultural fields
- Store field boundaries as GeoJSON polygons
- Track crop types and field notes
- View field history and measurements

### Job Management

- Assign tasks to field workers and drivers
- Track job status (pending, in progress, completed)
- Set priorities and due dates
- Link jobs to specific fields

### Driver Tracking

- Real-time GPS tracking of field workers
- Background location updates with foreground service
- Battery-optimized tracking
- Location history

### Billing & Payments

- Subscription management (basic, pro, enterprise plans)
- Invoice generation and tracking
- Multiple payment methods support
- Payment history and receipts

### Printing & Reports

- Bluetooth ESC/POS printer support
- PDF report generation
- Field measurement reports
- Invoice printing

### Multi-Language Support

- English and Sinhala (සිංහල) languages
- Complete UI translations
- Easy to add more languages

## 🏗️ Architecture

### Clean Architecture

- **Domain Layer**: Business entities and rules
- **Application Layer**: Use cases and business logic
- **Infrastructure Layer**: External services (API, GPS, storage)
- **Presentation Layer**: UI components and screens

### SOLID Principles

- Single Responsibility Principle
- Open/Closed Principle
- Liskov Substitution Principle
- Interface Segregation Principle
- Dependency Inversion Principle

### Design Patterns

- Repository Pattern
- Factory Pattern
- Observer Pattern
- Dependency Injection

## 🛠️ Tech Stack

### Mobile (Frontend)

- **Framework**: React Native with Expo SDK 54
- **Language**: TypeScript
- **Navigation**: React Navigation
- **State Management**: Zustand
- **Storage**: SQLite + MMKV (encrypted)
- **HTTP Client**: Axios
- **GPS**: Expo Location
- **Maps**: React Native Maps
- **Printing**: React Native BLE PLX
- **PDF**: Expo Print

### Backend (API)

- **Framework**: Laravel 10 (LTS)
- **Language**: PHP 8.3
- **Authentication**: JWT (tymon/jwt-auth)
- **Authorization**: RBAC (spatie/laravel-permission)
- **Database**: MySQL 8.0+ / PostgreSQL 13+ with spatial support
- **Queue**: Redis / Database
- **Cache**: Redis / File

## 📦 Installation

### Prerequisites

- Node.js 18+
- PHP 8.1+
- Composer
- MySQL 8.0+ or PostgreSQL 13+
- Redis (optional)

### Quick Start

1. **Clone the repository**

   ```bash
   git clone https://github.com/kasunvimarshana/geo-ops-platform.git
   cd geo-ops-platform
   ```

2. **Set up the backend**

   ```bash
   cd backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   php artisan migrate
   php artisan serve
   ```

3. **Set up the mobile app**
   ```bash
   cd mobile
   npm install
   # Update EXPO_PUBLIC_API_URL in .env
   npm start
   ```

For detailed setup instructions, see [docs/SETUP.md](docs/SETUP.md)

## 📚 Documentation

- [Architecture Documentation](docs/ARCHITECTURE.md) - System architecture and design
- [Setup Guide](docs/SETUP.md) - Installation and configuration
- [API Documentation](docs/API.md) - API endpoints and usage

## 🔒 Security

- JWT-based authentication with refresh tokens
- Encrypted local storage (MMKV)
- Role-Based Access Control (RBAC)
- Organization data isolation
- SQL injection protection
- XSS protection
- HTTPS API communication

## 🌍 GPS Features

### Battery Optimization

- Configurable accuracy levels (HIGH, MEDIUM, LOW)
- Dynamic update intervals
- Distance filter to reduce unnecessary updates
- Background tracking with foreground service

### Measurement Algorithms

- **Distance**: Haversine formula for accurate distance between GPS points
- **Area**: Shoelace formula for polygon area calculation
- **Perimeter**: Sum of distances between consecutive boundary points

## 🗺️ Spatial Data

- GeoJSON format for field boundaries
- Spatial database support (MySQL spatial extensions)
- Compatible with GIS software
- Export to KML/GeoJSON formats

## 🧪 Testing

### Backend Tests

```bash
cd backend
php artisan test
```

### Mobile Tests

```bash
cd mobile
npm test
```

## 📱 Screenshots

_Coming soon_

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Expo team for the excellent mobile development framework
- Laravel team for the powerful PHP framework
- Contributors and community members

## 📞 Support

For support, email kasunvmail@gmail.com or open an issue on GitHub.

## 🗓️ Roadmap

### Phase 1: Core Features ✅

- [x] Project structure and setup
- [x] Authentication and authorization
- [x] GPS tracking and measurement
- [x] Field management API
- [x] Multi-language support (English, Sinhala)

### Phase 2: Mobile UI (In Progress)

- [ ] Authentication screens
- [ ] GPS measurement screens
- [ ] Field listing and details
- [ ] Job management screens
- [ ] Maps integration

### Phase 3: Advanced Features

- [ ] Bluetooth printer integration
- [ ] PDF report generation
- [ ] Offline synchronization
- [ ] Payment integration
- [ ] Real-time driver tracking

### Phase 4: Production

- [ ] Comprehensive testing
- [ ] Security audit
- [ ] Performance optimization
- [ ] CI/CD pipeline
- [ ] App Store deployment

## 🔗 Links

- [Repository](https://github.com/kasunvimarshana/geo-ops-platform)
- [Documentation](docs/)
- [Issue Tracker](https://github.com/kasunvimarshana/geo-ops-platform/issues)
