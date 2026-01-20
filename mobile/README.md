# GeoOps Platform - Mobile App

React Native mobile application built with Expo for agricultural field management.

## Tech Stack

- **Framework**: React Native with Expo 54
- **Language**: TypeScript (strict mode)
- **Navigation**: React Navigation v7
- **State Management**: Zustand
- **Data Fetching**: TanStack Query (React Query)
- **API Client**: Axios
- **Offline Storage**: Expo SQLite
- **Secure Storage**: Expo Secure Store
- **Maps**: React Native Maps
- **GPS**: Expo Location
- **i18n**: i18next & react-i18next
- **Date Utils**: date-fns

## Project Structure

```
src/
├── features/           # Feature modules
│   ├── auth/          # Authentication screens and logic
│   ├── lands/         # Land management
│   ├── measurements/  # Field measurements
│   ├── jobs/          # Job management
│   ├── invoices/      # Invoice management
│   ├── payments/      # Payment tracking
│   ├── expenses/      # Expense tracking
│   └── tracking/      # GPS tracking
├── services/          # Core services
│   ├── api/          # API client and endpoints
│   ├── storage/      # SQLite database and secure storage
│   ├── gps/          # GPS service
│   └── sync/         # Offline sync service
├── shared/           # Shared components and utilities
│   ├── components/   # Reusable UI components
│   ├── hooks/        # Custom React hooks
│   ├── utils/        # Utility functions
│   └── types/        # TypeScript type definitions
├── navigation/       # Navigation setup
├── store/           # Global state (Zustand)
├── i18n/            # Internationalization
│   └── locales/     # Translation files (en, si)
├── theme/           # Theme configuration
└── config/          # App configuration
```

## Features

### Implemented

- ✅ Authentication (Login/Register)
- ✅ Token-based authentication with auto-refresh
- ✅ Offline-first architecture with SQLite
- ✅ Secure token storage
- ✅ Multi-language support (English, Sinhala)
- ✅ Navigation setup (Auth flow + Main tabs)
- ✅ API client with interceptors
- ✅ GPS service for location tracking
- ✅ Offline sync queue
- ✅ Reusable UI components
- ✅ Type-safe navigation
- ✅ Theme system

### To Be Implemented

- 🔲 Feature screens (Lands, Jobs, Financial, etc.)
- 🔲 Map integration for land boundaries
- 🔲 GPS-based measurements
- 🔲 Offline data synchronization
- 🔲 Photo capture for receipts/documentation
- 🔲 Push notifications
- 🔲 Report generation

## Getting Started

### Prerequisites

- Node.js 18+
- npm or yarn
- Expo CLI
- iOS Simulator (for iOS development)
- Android Studio (for Android development)

### Installation

1. Install dependencies:

```bash
cd mobile
npm install
```

2. Create environment file:

```bash
cp .env.example .env
```

3. Update `.env` with your API endpoint:

```
API_BASE_URL=http://localhost:8080/api/v1
```

### Running the App

Start the development server:

```bash
npm start
```

Run on iOS:

```bash
npm run ios
```

Run on Android:

```bash
npm run android
```

Run on web:

```bash
npm run web
```

## Architecture

### State Management

- **Zustand** for global state (auth, user)
- **React Query** for server state management
- **SQLite** for offline local state

### API Integration

- Axios client with automatic token refresh
- Request/response interceptors
- Error handling middleware
- Offline queue for failed requests

### Offline Support

- SQLite database for local data persistence
- Sync queue for pending operations
- Automatic sync when connection is restored
- Optimistic UI updates

### Navigation Flow

```
App
├── Auth Stack (Unauthenticated)
│   ├── Login Screen
│   └── Register Screen
└── Main Tabs (Authenticated)
    ├── Home
    ├── Lands
    ├── Jobs
    ├── Financial
    └── Profile
```

## API Endpoints

The app connects to the backend API with the following endpoints:

- `POST /auth/login` - User login
- `POST /auth/register` - User registration
- `POST /auth/logout` - User logout
- `POST /auth/refresh` - Refresh access token
- `GET /auth/profile` - Get user profile
- `PUT /auth/profile` - Update user profile
- `GET /lands` - Get all lands
- `POST /lands` - Create land
- `GET /measurements` - Get measurements
- `POST /measurements` - Create measurement
- `GET /jobs` - Get jobs
- `POST /jobs` - Create job
- `GET /invoices` - Get invoices
- `POST /invoices` - Create invoice
- `GET /payments` - Get payments
- `POST /payments` - Create payment
- `GET /expenses` - Get expenses
- `POST /expenses` - Create expense

## Localization

The app supports multiple languages:

- English (en)
- Sinhala (si)

Translation files are located in `src/i18n/locales/`.

To add a new language:

1. Create a new JSON file in `src/i18n/locales/`
2. Add translations following the existing structure
3. Register the language in `src/i18n/index.ts`

## Security

- JWT tokens stored in Expo Secure Store
- Automatic token refresh before expiry
- Secure API communication
- Input validation on all forms
- SQL injection prevention with parameterized queries

## Testing

```bash
# Type checking
npm run type-check

# Linting
npm run lint
```

## Building for Production

### iOS

```bash
eas build --platform ios
```

### Android

```bash
eas build --platform android
```

## Contributing

1. Follow TypeScript strict mode guidelines
2. Use functional components with hooks
3. Follow the existing file structure
4. Add proper type definitions
5. Write clean, self-documenting code
6. Use the shared components and utilities

## License

Private - All rights reserved
