# GeoOps Mobile App

React Native mobile application for GPS-based land measurement and field operations management.

## Features

### 🗺️ Land Measurement
- **Walk-Around Mode**: Automatically track GPS points while walking around land boundaries
- **Point-Based Mode**: Manually capture individual GPS points at corners
- Real-time area calculation (acres & hectares)
- GPS accuracy monitoring
- Offline measurement storage

### 📍 Maps & Location
- Interactive map view with land boundaries
- Job location markers
- Driver tracking
- Google Maps integration

### 🚜 Job Management
- Create and assign jobs to drivers
- Track job status (pending, in-progress, completed)
- Real-time location tracking during jobs
- Customer information management

### 💰 Billing & Invoicing
- Generate invoices from measurements and jobs
- Track payment status
- Bluetooth printer support (ESC/POS)
- PDF generation and export

### 🔄 Offline Sync
- Full offline functionality
- Automatic background synchronization
- Conflict resolution
- Pending items tracking

### 👤 User Management
- Multi-language support (English & Sinhala)
- Role-based access control
- Profile management
- Organization settings

## Architecture

```
mobile/
├── src/
│   ├── components/          # Reusable UI components
│   │   ├── Button.tsx
│   │   ├── Input.tsx
│   │   ├── Card.tsx
│   │   ├── List.tsx
│   │   ├── Modal.tsx
│   │   ├── Loading.tsx
│   │   ├── Header.tsx
│   │   └── MapView.tsx
│   ├── features/           # Feature-based screens
│   │   ├── auth/          # Login, Register, Profile
│   │   ├── measurement/   # GPS measurement screens
│   │   ├── maps/          # Map view
│   │   ├── jobs/          # Job management
│   │   ├── billing/       # Invoices
│   │   └── sync/          # Sync status
│   ├── navigation/        # Navigation configuration
│   │   ├── AppNavigator.tsx
│   │   ├── AuthNavigator.tsx
│   │   └── MainNavigator.tsx
│   ├── services/          # Business logic & APIs
│   │   ├── api/           # API client
│   │   ├── gps/           # GPS tracking
│   │   ├── storage/       # SQLite database
│   │   ├── sync/          # Background sync
│   │   ├── printer/       # Bluetooth printing
│   │   └── location/      # Location utilities
│   ├── stores/            # State management (Zustand)
│   │   ├── authStore.ts
│   │   ├── measurementStore.ts
│   │   └── syncStore.ts
│   ├── types/             # TypeScript types
│   ├── constants/         # App constants
│   ├── i18n/              # Translations
│   └── utils/             # Helper functions
├── App.tsx                # App entry point
└── package.json
```

## Installation

1. **Install dependencies**:
```bash
cd mobile
npm install
```

2. **Configure environment**:
```bash
cp .env.example .env
# Edit .env with your API URL
```

3. **Run the app**:

For iOS:
```bash
npm run ios
```

For Android:
```bash
npm run android
```

For development:
```bash
npm start
```

## Technologies

- **Framework**: React Native (Expo)
- **Language**: TypeScript
- **Navigation**: React Navigation (Stack + Bottom Tabs)
- **State Management**: Zustand
- **Data Fetching**: TanStack Query (React Query)
- **Maps**: react-native-maps (Google Maps)
- **Location**: expo-location
- **Database**: SQLite (expo-sqlite)
- **Storage**: AsyncStorage, SecureStore
- **Printing**: react-native-bluetooth-escpos-printer
- **i18n**: react-i18next

## Key Components

### GPS Service
- High-accuracy GPS tracking
- Area calculation using Shoelace formula
- Background location tracking
- Distance calculation (Haversine formula)

### Database Service
- SQLite local database
- Offline data storage
- Transaction support
- Indexed queries for performance

### Sync Service
- Automatic background sync (every 5 minutes)
- Batch upload of pending items
- Conflict detection and resolution
- Retry mechanism with exponential backoff

### Printer Service
- Bluetooth device scanning and connection
- ESC/POS command printing
- Invoice and measurement receipt printing
- Support for various thermal printers

## Offline Capabilities

The app is designed for offline-first operation:

1. **Local Storage**: All data stored in SQLite database
2. **Sync Queue**: Changes tracked and queued for sync
3. **Automatic Sync**: Background sync when online
4. **Conflict Resolution**: Server-side conflict detection
5. **Manual Sync**: User-initiated sync option

## Permissions Required

### Android
- `ACCESS_FINE_LOCATION` - GPS tracking
- `ACCESS_COARSE_LOCATION` - Location services
- `ACCESS_BACKGROUND_LOCATION` - Background tracking
- `BLUETOOTH` - Printer connection
- `BLUETOOTH_ADMIN` - Bluetooth management

### iOS
- `NSLocationWhenInUseUsageDescription` - Location access
- `NSLocationAlwaysAndWhenInUseUsageDescription` - Background location
- `NSBluetoothAlwaysUsageDescription` - Bluetooth access

## Development

### Type Checking
```bash
npm run type-check
```

### Linting
```bash
npm run lint
```

### Testing
```bash
npm test
```

## Build & Deployment

### Android APK
```bash
eas build --platform android
```

### iOS IPA
```bash
eas build --platform ios
```

### OTA Updates
```bash
eas update
```

## Best Practices

1. **Component Structure**: Small, reusable components with clear responsibilities
2. **Type Safety**: Strict TypeScript with proper interfaces
3. **Error Handling**: Try-catch blocks with user-friendly messages
4. **Loading States**: Loading indicators for async operations
5. **Offline Support**: All features work offline with sync
6. **Accessibility**: Proper labels and touch targets
7. **Performance**: Optimized rendering with React.memo and useMemo
8. **Security**: Secure storage for tokens, encrypted data

## Troubleshooting

### GPS Not Working
- Check location permissions
- Ensure GPS is enabled on device
- Test in open area with clear sky view

### Sync Issues
- Check internet connectivity
- Verify API URL in .env
- Review sync errors in Sync screen

### Bluetooth Printer
- Ensure Bluetooth is enabled
- Pair printer in device settings first
- Check printer is ESC/POS compatible

## Support

For issues or questions:
- Create an issue in the repository
- Check existing documentation
- Contact support team

## License

Copyright © 2024 GeoOps Platform. All rights reserved.
