# GeoOps Platform - Mobile App

Production-ready React Native Expo mobile application for GPS field management with offline-first capabilities.

## Features

✅ **Offline-First Architecture**

- Local SQLite database for data persistence
- MMKV storage for secure token management
- Background sync when online
- Conflict resolution with last-write-wins strategy

✅ **GPS Measurement**

- Real-time location tracking
- Walk-around polygon drawing
- Point-by-point measurement
- Area calculation (sq.m and acres)
- Perimeter calculation

✅ **Job Management**

- Create, edit, and track field jobs
- Status management (pending, in progress, completed, cancelled)
- Customer information
- Offline job creation with auto-sync

✅ **Authentication**

- JWT token-based authentication
- Secure token storage
- Auto-login on app start
- Token refresh mechanism

✅ **Bilingual Support**

- English (en)
- Sinhala (සිංහල - si)
- Easy language switching

✅ **Type-Safe Development**

- Full TypeScript coverage
- Strict mode enabled
- Comprehensive type definitions

## Tech Stack

- **Framework**: React Native with Expo
- **Language**: TypeScript
- **Navigation**: React Navigation (Stack & Bottom Tabs)
- **State Management**: Zustand
- **API Client**: Axios
- **Maps**: React Native Maps
- **Location**: Expo Location
- **Offline Storage**: SQLite + MMKV
- **Forms**: React Hook Form
- **i18n**: i18next
- **UI Components**: React Native Paper

## Project Structure

```
src/
├── features/              # Feature-based modules
│   ├── auth/             # Authentication
│   ├── gps/              # GPS measurement
│   ├── jobs/             # Job management
│   ├── invoices/         # Invoice management
│   └── tracking/         # Real-time tracking
├── shared/               # Shared resources
│   ├── components/       # Reusable components
│   ├── services/         # API, storage, sync services
│   ├── hooks/            # Custom hooks
│   ├── utils/            # Utility functions
│   ├── types/            # Type definitions
│   └── constants/        # App constants
├── navigation/           # Navigation configuration
├── store/               # Zustand stores
├── locales/             # i18n translations
└── theme/               # Theme configuration
```

## Installation

```bash
# Install dependencies
npm install

# Start development server
npm start

# Run on Android
npm run android

# Run on iOS
npm run ios
```

## Environment Configuration

Create a `.env` file in the mobile directory:

```env
EXPO_PUBLIC_API_URL=http://your-backend-url:8000/api
```

## Key Services

### API Client

- Automatic token injection
- Token refresh on 401
- Error handling
- Retry logic

### SQLite Service

- Local database for jobs and plots
- Sync queue management
- Offline data persistence

### Location Service

- High-accuracy GPS tracking
- Area and perimeter calculations
- Distance calculations
- Walk-around mode support

### Sync Service

- Background auto-sync every 5 minutes
- Network status monitoring
- Batch processing
- Retry mechanism with exponential backoff

## Offline Capabilities

1. **Job Management**: Create and edit jobs offline
2. **GPS Measurements**: Record measurements without connection
3. **Sync Queue**: Operations queued and synced when online
4. **Local Storage**: All data persisted locally
5. **Conflict Resolution**: Server-wins strategy for conflicts

## State Management

Using Zustand for global state:

- **authStore**: User authentication state
- **jobsStore**: Job management state
- **plotsStore**: GPS measurement state
- **syncStore**: Sync status and network state

## Best Practices Implemented

- Feature-based folder structure
- Separation of concerns
- Type-safe API calls
- Error boundaries
- Loading states
- Optimistic updates
- Proper memory cleanup
- Battery-optimized GPS tracking

## Performance Optimizations

- Lazy loading of screens
- Memoized calculations
- Debounced inputs
- Efficient re-renders with Zustand
- Optimized map rendering

## Security

- Secure token storage with MMKV
- No sensitive data in logs
- API token in headers only
- SQLite encryption ready

## Testing

```bash
# Run linter
npm run lint
```

## Building for Production

```bash
# Build Android APK
eas build --platform android

# Build iOS
eas build --platform ios
```

## Troubleshooting

### Location permissions not working

Make sure to add location permissions in `app.json`

### Database initialization fails

Check SQLite initialization in AppNavigator

### Maps not rendering

Verify Google Maps API key configuration

## Next Steps

1. Add unit tests with Jest
2. Add E2E tests with Detox
3. Implement push notifications
4. Add photo capture for jobs
5. Implement invoice PDF preview
6. Add real-time driver tracking
7. Implement biometric authentication
8. Add analytics tracking

## License

Proprietary - GeoOps Platform
