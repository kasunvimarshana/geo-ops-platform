# GeoOps Mobile App - React Native (Expo)

Production-ready React Native mobile application for GeoOps Platform with offline-first architecture.

## Features

- 📱 Cross-platform (iOS & Android)
- 🗺️ GPS land measurement with walk-around and point-based methods
- 📍 Real-time driver tracking
- 💼 Job management
- 💰 Billing and invoicing
- 📊 Financial reports
- 🔄 Offline-first with background sync
- 🌍 Bilingual support (Sinhala/English)
- 🔐 JWT authentication

## Requirements

- Node.js 18+ and npm/yarn
- Expo CLI (`npm install -g expo-cli`)
- iOS Simulator (macOS) or Android Studio
- Expo Go app for testing on physical devices

## Installation

### 1. Install Dependencies

```bash
cd frontend
npm install
```

### 2. Environment Configuration

Create `.env` file:

```bash
cp .env.example .env
```

Configure environment variables:

```env
EXPO_PUBLIC_API_URL=http://localhost:8000/api
EXPO_PUBLIC_GOOGLE_MAPS_API_KEY=your_api_key_here
EXPO_PUBLIC_APP_ENV=development
```

### 3. Start Development Server

```bash
# Start Expo dev server
npm start

# Run on iOS simulator
npm run ios

# Run on Android emulator
npm run android

# Run on web
npm run web
```

## Project Structure

```
frontend/
├── app/                      # Expo Router - File-based routing
│   ├── (auth)/              # Authentication routes
│   │   ├── login.tsx
│   │   └── register.tsx
│   ├── (tabs)/              # Main app tabs
│   │   ├── index.tsx        # Dashboard
│   │   ├── measurements.tsx
│   │   ├── jobs.tsx
│   │   ├── tracking.tsx
│   │   └── profile.tsx
│   ├── _layout.tsx          # Root layout
│   └── +not-found.tsx
├── src/
│   ├── components/          # Reusable UI components
│   │   ├── ui/              # Base components
│   │   │   ├── Button.tsx
│   │   │   ├── Input.tsx
│   │   │   ├── Card.tsx
│   │   │   └── ...
│   │   ├── forms/           # Form components
│   │   │   ├── MeasurementForm.tsx
│   │   │   ├── JobForm.tsx
│   │   │   └── ...
│   │   └── maps/            # Map components
│   │       ├── MapView.tsx
│   │       ├── MeasurementMap.tsx
│   │       └── TrackingMap.tsx
│   ├── features/            # Feature modules
│   │   ├── auth/
│   │   │   ├── hooks/
│   │   │   ├── screens/
│   │   │   └── types.ts
│   │   ├── measurements/
│   │   │   ├── hooks/
│   │   │   ├── screens/
│   │   │   ├── utils/
│   │   │   └── types.ts
│   │   ├── jobs/
│   │   ├── billing/
│   │   ├── expenses/
│   │   ├── tracking/
│   │   └── subscriptions/
│   ├── services/            # External services
│   │   ├── api/             # API client
│   │   │   ├── client.ts
│   │   │   ├── auth.ts
│   │   │   ├── measurements.ts
│   │   │   ├── jobs.ts
│   │   │   └── ...
│   │   ├── storage/         # Local storage
│   │   │   ├── database.ts  # SQLite
│   │   │   └── cache.ts     # MMKV
│   │   ├── location/        # GPS services
│   │   │   ├── tracker.ts
│   │   │   └── calculator.ts
│   │   └── sync/            # Offline sync
│   │       ├── syncManager.ts
│   │       └── conflictResolver.ts
│   ├── store/               # State management (Zustand)
│   │   ├── authStore.ts
│   │   ├── measurementStore.ts
│   │   ├── jobStore.ts
│   │   ├── syncStore.ts
│   │   └── settingsStore.ts
│   ├── hooks/               # Custom hooks
│   │   ├── useLocation.ts
│   │   ├── useOfflineSync.ts
│   │   ├── usePermissions.ts
│   │   └── ...
│   ├── utils/               # Utility functions
│   │   ├── calculations.ts
│   │   ├── validators.ts
│   │   ├── formatters.ts
│   │   └── constants.ts
│   ├── locales/             # i18n translations
│   │   ├── en.json
│   │   └── si.json
│   └── types/               # TypeScript types
│       ├── api.ts
│       ├── models.ts
│       └── navigation.ts
├── assets/                  # Static assets
│   ├── images/
│   ├── icons/
│   └── fonts/
├── .env.example
├── app.json
├── babel.config.js
├── package.json
└── tsconfig.json
```

## Architecture

### Feature-Based Structure

Each feature module is self-contained:

```typescript
features/measurements/
├── hooks/
│   ├── useMeasurement.ts      # Measurement CRUD operations
│   ├── useGPSTracking.ts      # GPS tracking logic
│   └── useAreaCalculation.ts  # Area calculation
├── screens/
│   ├── MeasurementList.tsx
│   ├── CreateMeasurement.tsx
│   └── MeasurementDetail.tsx
├── utils/
│   ├── areaCalculator.ts      # Polygon area calculation
│   └── coordinateUtils.ts
└── types.ts                    # Feature-specific types
```

### State Management (Zustand)

```typescript
// store/measurementStore.ts
import create from 'zustand';

interface MeasurementStore {
  measurements: Measurement[];
  loading: boolean;
  fetchMeasurements: () => Promise<void>;
  createMeasurement: (data: CreateMeasurementDTO) => Promise<void>;
  updateMeasurement: (id: number, data: Partial<Measurement>) => Promise<void>;
  deleteMeasurement: (id: number) => Promise<void>;
}

export const useMeasurementStore = create<MeasurementStore>((set, get) => ({
  measurements: [],
  loading: false,

  fetchMeasurements: async () => {
    set({ loading: true });
    try {
      const data = await measurementApi.getAll();
      set({ measurements: data, loading: false });
    } catch (error) {
      set({ loading: false });
      throw error;
    }
  },

  // ... other actions
}));
```

### API Service

```typescript
// services/api/client.ts
import axios from 'axios';
import { getToken } from '../storage/cache';

const apiClient = axios.create({
  baseURL: process.env.EXPO_PUBLIC_API_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor
apiClient.interceptors.request.use(
  async (config) => {
    const token = await getToken();
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor
apiClient.interceptors.response.use(
  (response) => response.data,
  async (error) => {
    if (error.response?.status === 401) {
      // Handle token expiration
      await handleTokenExpiration();
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

### Offline Storage (SQLite)

```typescript
// services/storage/database.ts
import * as SQLite from 'expo-sqlite';

const db = SQLite.openDatabase('geo-ops.db');

export const initDatabase = () => {
  db.transaction((tx) => {
    // Measurements table
    tx.executeSql(
      `CREATE TABLE IF NOT EXISTS measurements (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        server_id INTEGER,
        name TEXT NOT NULL,
        coordinates TEXT NOT NULL,
        area_acres REAL,
        area_hectares REAL,
        measured_at TEXT,
        synced INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )`
    );

    // Jobs table
    tx.executeSql(
      `CREATE TABLE IF NOT EXISTS jobs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        server_id INTEGER,
        customer_id INTEGER,
        status TEXT,
        notes TEXT,
        synced INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )`
    );

    // Tracking logs table
    tx.executeSql(
      `CREATE TABLE IF NOT EXISTS tracking_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        latitude REAL NOT NULL,
        longitude REAL NOT NULL,
        accuracy REAL,
        recorded_at TEXT NOT NULL,
        synced INTEGER DEFAULT 0
      )`
    );
  });
};

export const saveMeasurement = (measurement: LocalMeasurement): Promise<number> => {
  return new Promise((resolve, reject) => {
    db.transaction((tx) => {
      tx.executeSql(
        'INSERT INTO measurements (name, coordinates, area_acres, area_hectares, measured_at, synced) VALUES (?, ?, ?, ?, ?, ?)',
        [
          measurement.name,
          JSON.stringify(measurement.coordinates),
          measurement.area_acres,
          measurement.area_hectares,
          measurement.measured_at,
          0,
        ],
        (_, result) => resolve(result.insertId!),
        (_, error) => {
          reject(error);
          return false;
        }
      );
    });
  });
};
```

### GPS Tracking

```typescript
// services/location/tracker.ts
import * as Location from 'expo-location';
import * as TaskManager from 'expo-task-manager';

const LOCATION_TASK_NAME = 'background-location-task';

export const startLocationTracking = async () => {
  const { status } = await Location.requestForegroundPermissionsAsync();
  if (status !== 'granted') {
    throw new Error('Location permission not granted');
  }

  const backgroundStatus = await Location.requestBackgroundPermissionsAsync();
  if (backgroundStatus.status !== 'granted') {
    throw new Error('Background location permission not granted');
  }

  await Location.startLocationUpdatesAsync(LOCATION_TASK_NAME, {
    accuracy: Location.Accuracy.High,
    timeInterval: 60000, // 1 minute
    distanceInterval: 50, // 50 meters
    foregroundService: {
      notificationTitle: 'GeoOps Tracking',
      notificationBody: 'Recording your location',
    },
  });
};

TaskManager.defineTask(LOCATION_TASK_NAME, async ({ data, error }) => {
  if (error) {
    console.error(error);
    return;
  }
  if (data) {
    const { locations } = data as any;
    // Save to local database
    await saveTrackingLogs(locations);
  }
});

export const stopLocationTracking = async () => {
  await Location.stopLocationUpdatesAsync(LOCATION_TASK_NAME);
};
```

### Area Calculation

```typescript
// features/measurements/utils/areaCalculator.ts

interface Coordinate {
  latitude: number;
  longitude: number;
}

/**
 * Calculate polygon area using Shoelace formula
 * Returns area in square meters
 */
export const calculatePolygonArea = (coordinates: Coordinate[]): number => {
  if (coordinates.length < 3) return 0;

  const earthRadius = 6378137; // meters
  let area = 0;

  for (let i = 0; i < coordinates.length; i++) {
    const j = (i + 1) % coordinates.length;
    const xi = (coordinates[i].longitude * Math.PI) / 180;
    const yi = (coordinates[i].latitude * Math.PI) / 180;
    const xj = (coordinates[j].longitude * Math.PI) / 180;
    const yj = (coordinates[j].latitude * Math.PI) / 180;

    area += (xj - xi) * (2 + Math.sin(yi) + Math.sin(yj));
  }

  area = Math.abs((area * earthRadius * earthRadius) / 2);
  return area;
};

/**
 * Convert square meters to acres
 */
export const metersToAcres = (squareMeters: number): number => {
  return squareMeters / 4046.86;
};

/**
 * Convert square meters to hectares
 */
export const metersToHectares = (squareMeters: number): number => {
  return squareMeters / 10000;
};

/**
 * Calculate area in both units
 */
export const calculateArea = (coordinates: Coordinate[]) => {
  const areaInMeters = calculatePolygonArea(coordinates);
  return {
    squareMeters: areaInMeters,
    acres: metersToAcres(areaInMeters),
    hectares: metersToHectares(areaInMeters),
  };
};
```

### Offline Sync

```typescript
// services/sync/syncManager.ts
import NetInfo from '@react-native-community/netinfo';

class SyncManager {
  private isOnline = false;
  private syncQueue: SyncTask[] = [];
  private isSyncing = false;

  constructor() {
    this.initNetworkListener();
  }

  private initNetworkListener() {
    NetInfo.addEventListener((state) => {
      this.isOnline = state.isConnected ?? false;
      if (this.isOnline && !this.isSyncing) {
        this.processSyncQueue();
      }
    });
  }

  async addToQueue(task: SyncTask) {
    this.syncQueue.push(task);
    if (this.isOnline) {
      await this.processSyncQueue();
    }
  }

  private async processSyncQueue() {
    if (this.isSyncing || this.syncQueue.length === 0) return;

    this.isSyncing = true;

    while (this.syncQueue.length > 0) {
      const task = this.syncQueue[0];
      try {
        await this.executeTask(task);
        this.syncQueue.shift(); // Remove completed task
      } catch (error) {
        console.error('Sync failed:', error);
        // Retry logic with exponential backoff
        await this.handleSyncError(task, error);
        break;
      }
    }

    this.isSyncing = false;
  }

  private async executeTask(task: SyncTask) {
    switch (task.type) {
      case 'CREATE_MEASUREMENT':
        return await measurementApi.create(task.data);
      case 'UPDATE_JOB':
        return await jobApi.update(task.id, task.data);
      case 'SYNC_TRACKING':
        return await trackingApi.batchCreate(task.data);
      default:
        throw new Error(`Unknown task type: ${task.type}`);
    }
  }
}

export const syncManager = new SyncManager();
```

## Localization

```typescript
// locales/en.json
{
  "common": {
    "save": "Save",
    "cancel": "Cancel",
    "delete": "Delete",
    "edit": "Edit",
    "submit": "Submit"
  },
  "auth": {
    "login": "Login",
    "email": "Email",
    "password": "Password",
    "forgotPassword": "Forgot Password?"
  },
  "measurements": {
    "title": "Land Measurements",
    "create": "New Measurement",
    "walkAround": "Walk Around",
    "pointBased": "Point Based",
    "area": "Area",
    "acres": "Acres",
    "hectares": "Hectares"
  }
}
```

```typescript
// locales/si.json (Sinhala)
{
  "common": {
    "save": "සුරකින්න",
    "cancel": "අවලංගු කරන්න",
    "delete": "මකන්න",
    "edit": "සංස්කරණය",
    "submit": "ඉදිරිපත් කරන්න"
  },
  "measurements": {
    "title": "ඉඩම් මැනීම්",
    "create": "නව මැනීම",
    "walkAround": "වටා ඇවිදීම",
    "pointBased": "ලක්ෂ්‍ය පදනම",
    "area": "ප්‍රදේශය",
    "acres": "අක්කර",
    "hectares": "හෙක්ටයාර්"
  }
}
```

## Testing

```bash
# Run tests
npm test

# Run tests with coverage
npm test -- --coverage

# Run specific test file
npm test -- MeasurementScreen.test.tsx
```

## Building for Production

### Android

```bash
# Build APK
eas build --platform android --profile production

# Build AAB for Play Store
eas build --platform android --profile production --type app-bundle
```

### iOS

```bash
# Build for iOS
eas build --platform ios --profile production
```

## Over-the-Air Updates

```bash
# Publish update
eas update --branch production --message "Bug fixes and improvements"
```

## Performance Optimization

- Lazy loading of screens
- Image optimization with expo-image
- Virtualized lists (FlashList)
- Memoization of expensive calculations
- Debouncing search inputs
- Background task optimization

## Best Practices

1. **Clean Code**: Follow TypeScript best practices
2. **Component Reusability**: Create reusable components
3. **State Management**: Keep global state minimal
4. **Error Handling**: Implement proper error boundaries
5. **Testing**: Write unit and integration tests
6. **Performance**: Optimize renders and API calls
7. **Security**: Never store sensitive data in plain text
8. **Accessibility**: Support screen readers and large text

## License

Proprietary - All rights reserved
