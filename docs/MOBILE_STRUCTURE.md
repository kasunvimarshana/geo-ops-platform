# Mobile Project Structure

## GeoOps Platform - React Native Expo Mobile App

### Feature-Based Modular Structure

```
mobile/
├── src/
│   ├── features/                    # Feature-based modules
│   │   ├── auth/
│   │   │   ├── screens/
│   │   │   │   ├── LoginScreen.tsx
│   │   │   │   ├── RegisterScreen.tsx
│   │   │   │   └── ForgotPasswordScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── LoginForm.tsx
│   │   │   │   └── SocialLogin.tsx
│   │   │   ├── hooks/
│   │   │   │   ├── useAuth.ts
│   │   │   │   └── useLogin.ts
│   │   │   ├── store/
│   │   │   │   └── authStore.ts
│   │   │   ├── services/
│   │   │   │   └── authService.ts
│   │   │   └── types/
│   │   │       └── auth.types.ts
│   │   │
│   │   ├── measurement/
│   │   │   ├── screens/
│   │   │   │   ├── MeasurementScreen.tsx
│   │   │   │   ├── MeasurementHistoryScreen.tsx
│   │   │   │   └── MeasurementDetailScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── MeasurementMap.tsx
│   │   │   │   ├── GPSTracker.tsx
│   │   │   │   ├── AreaCalculator.tsx
│   │   │   │   ├── MeasurementControls.tsx
│   │   │   │   └── CoordinateList.tsx
│   │   │   ├── hooks/
│   │   │   │   ├── useMeasurement.ts
│   │   │   │   ├── useGPSTracking.ts
│   │   │   │   └── useAreaCalculation.ts
│   │   │   ├── store/
│   │   │   │   └── measurementStore.ts
│   │   │   ├── services/
│   │   │   │   ├── measurementService.ts
│   │   │   │   └── gpsService.ts
│   │   │   ├── utils/
│   │   │   │   ├── areaCalculation.ts
│   │   │   │   └── coordinateUtils.ts
│   │   │   └── types/
│   │   │       └── measurement.types.ts
│   │   │
│   │   ├── lands/
│   │   │   ├── screens/
│   │   │   │   ├── LandListScreen.tsx
│   │   │   │   ├── LandDetailScreen.tsx
│   │   │   │   └── CreateLandScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── LandCard.tsx
│   │   │   │   ├── LandMap.tsx
│   │   │   │   └── LandForm.tsx
│   │   │   ├── hooks/
│   │   │   │   └── useLands.ts
│   │   │   ├── store/
│   │   │   │   └── landStore.ts
│   │   │   ├── services/
│   │   │   │   └── landService.ts
│   │   │   └── types/
│   │   │       └── land.types.ts
│   │   │
│   │   ├── jobs/
│   │   │   ├── screens/
│   │   │   │   ├── JobListScreen.tsx
│   │   │   │   ├── JobDetailScreen.tsx
│   │   │   │   ├── CreateJobScreen.tsx
│   │   │   │   └── ActiveJobScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── JobCard.tsx
│   │   │   │   ├── JobStatusBadge.tsx
│   │   │   │   ├── JobForm.tsx
│   │   │   │   └── JobTimeline.tsx
│   │   │   ├── hooks/
│   │   │   │   └── useJobs.ts
│   │   │   ├── store/
│   │   │   │   └── jobStore.ts
│   │   │   ├── services/
│   │   │   │   └── jobService.ts
│   │   │   └── types/
│   │   │       └── job.types.ts
│   │   │
│   │   ├── tracking/
│   │   │   ├── screens/
│   │   │   │   ├── TrackingMapScreen.tsx
│   │   │   │   └── TrackingHistoryScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── DriverMarker.tsx
│   │   │   │   ├── TrackingRoute.tsx
│   │   │   │   └── TrackingStats.tsx
│   │   │   ├── hooks/
│   │   │   │   └── useTracking.ts
│   │   │   ├── store/
│   │   │   │   └── trackingStore.ts
│   │   │   ├── services/
│   │   │   │   ├── trackingService.ts
│   │   │   │   └── locationService.ts
│   │   │   └── types/
│   │   │       └── tracking.types.ts
│   │   │
│   │   ├── invoices/
│   │   │   ├── screens/
│   │   │   │   ├── InvoiceListScreen.tsx
│   │   │   │   ├── InvoiceDetailScreen.tsx
│   │   │   │   └── CreateInvoiceScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── InvoiceCard.tsx
│   │   │   │   ├── InvoicePreview.tsx
│   │   │   │   ├── InvoiceForm.tsx
│   │   │   │   └── PDFViewer.tsx
│   │   │   ├── hooks/
│   │   │   │   └── useInvoices.ts
│   │   │   ├── store/
│   │   │   │   └── invoiceStore.ts
│   │   │   ├── services/
│   │   │   │   ├── invoiceService.ts
│   │   │   │   └── pdfService.ts
│   │   │   └── types/
│   │   │       └── invoice.types.ts
│   │   │
│   │   ├── payments/
│   │   │   ├── screens/
│   │   │   │   ├── PaymentListScreen.tsx
│   │   │   │   ├── RecordPaymentScreen.tsx
│   │   │   │   └── LedgerScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── PaymentCard.tsx
│   │   │   │   ├── PaymentForm.tsx
│   │   │   │   └── LedgerSummary.tsx
│   │   │   ├── hooks/
│   │   │   │   └── usePayments.ts
│   │   │   ├── store/
│   │   │   │   └── paymentStore.ts
│   │   │   ├── services/
│   │   │   │   └── paymentService.ts
│   │   │   └── types/
│   │   │       └── payment.types.ts
│   │   │
│   │   ├── expenses/
│   │   │   ├── screens/
│   │   │   │   ├── ExpenseListScreen.tsx
│   │   │   │   ├── CreateExpenseScreen.tsx
│   │   │   │   └── ExpenseSummaryScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── ExpenseCard.tsx
│   │   │   │   ├── ExpenseForm.tsx
│   │   │   │   └── CategoryPicker.tsx
│   │   │   ├── hooks/
│   │   │   │   └── useExpenses.ts
│   │   │   ├── store/
│   │   │   │   └── expenseStore.ts
│   │   │   ├── services/
│   │   │   │   └── expenseService.ts
│   │   │   └── types/
│   │   │       └── expense.types.ts
│   │   │
│   │   ├── reports/
│   │   │   ├── screens/
│   │   │   │   ├── ReportsScreen.tsx
│   │   │   │   ├── FinancialReportScreen.tsx
│   │   │   │   └── JobReportScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── ReportCard.tsx
│   │   │   │   ├── DateRangePicker.tsx
│   │   │   │   ├── Chart.tsx
│   │   │   │   └── ReportExporter.tsx
│   │   │   ├── hooks/
│   │   │   │   └── useReports.ts
│   │   │   ├── services/
│   │   │   │   └── reportService.ts
│   │   │   └── types/
│   │   │       └── report.types.ts
│   │   │
│   │   ├── subscription/
│   │   │   ├── screens/
│   │   │   │   ├── SubscriptionScreen.tsx
│   │   │   │   ├── PackagesScreen.tsx
│   │   │   │   └── UsageScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── PackageCard.tsx
│   │   │   │   ├── UsageBar.tsx
│   │   │   │   └── UpgradePrompt.tsx
│   │   │   ├── hooks/
│   │   │   │   └── useSubscription.ts
│   │   │   ├── store/
│   │   │   │   └── subscriptionStore.ts
│   │   │   ├── services/
│   │   │   │   └── subscriptionService.ts
│   │   │   └── types/
│   │   │       └── subscription.types.ts
│   │   │
│   │   └── profile/
│   │       ├── screens/
│   │       │   ├── ProfileScreen.tsx
│   │       │   ├── EditProfileScreen.tsx
│   │       │   └── SettingsScreen.tsx
│   │       ├── components/
│   │       │   ├── ProfileHeader.tsx
│   │       │   └── SettingsList.tsx
│   │       ├── hooks/
│   │       │   └── useProfile.ts
│   │       ├── services/
│   │       │   └── profileService.ts
│   │       └── types/
│   │           └── profile.types.ts
│   │
│   ├── shared/                      # Shared/common components
│   │   ├── components/
│   │   │   ├── ui/
│   │   │   │   ├── Button.tsx
│   │   │   │   ├── Input.tsx
│   │   │   │   ├── Card.tsx
│   │   │   │   ├── Modal.tsx
│   │   │   │   ├── Loading.tsx
│   │   │   │   ├── EmptyState.tsx
│   │   │   │   ├── ErrorBoundary.tsx
│   │   │   │   └── StatusBadge.tsx
│   │   │   ├── layout/
│   │   │   │   ├── Container.tsx
│   │   │   │   ├── Header.tsx
│   │   │   │   └── BottomSheet.tsx
│   │   │   └── feedback/
│   │   │       ├── Toast.tsx
│   │   │       └── Alert.tsx
│   │   ├── hooks/
│   │   │   ├── useApi.ts
│   │   │   ├── useDebounce.ts
│   │   │   ├── usePermissions.ts
│   │   │   ├── useNetworkStatus.ts
│   │   │   └── useTheme.ts
│   │   ├── utils/
│   │   │   ├── formatters.ts
│   │   │   ├── validators.ts
│   │   │   ├── constants.ts
│   │   │   └── helpers.ts
│   │   └── types/
│   │       ├── api.types.ts
│   │       ├── common.types.ts
│   │       └── navigation.types.ts
│   │
│   ├── services/                    # Core services
│   │   ├── api/
│   │   │   ├── apiClient.ts        # Axios instance with interceptors
│   │   │   ├── endpoints.ts        # API endpoint constants
│   │   │   └── apiHelpers.ts       # Request/response helpers
│   │   ├── storage/
│   │   │   ├── SecureStorage.ts    # Expo SecureStore wrapper
│   │   │   ├── LocalStorage.ts     # AsyncStorage wrapper
│   │   │   └── DatabaseService.ts  # SQLite database
│   │   ├── sync/
│   │   │   ├── SyncManager.ts      # Manages offline sync
│   │   │   ├── SyncQueue.ts        # Queue for pending operations
│   │   │   └── ConflictResolver.ts # Handles sync conflicts
│   │   ├── gps/
│   │   │   ├── LocationService.ts  # Location tracking
│   │   │   ├── GPSManager.ts       # GPS state management
│   │   │   └── BackgroundLocation.ts # Background tracking
│   │   └── notifications/
│   │       └── NotificationService.ts
│   │
│   ├── navigation/
│   │   ├── AppNavigator.tsx        # Root navigator
│   │   ├── AuthNavigator.tsx       # Auth stack
│   │   ├── MainNavigator.tsx       # Main app stack
│   │   ├── TabNavigator.tsx        # Bottom tabs
│   │   └── types.ts                # Navigation types
│   │
│   ├── store/                       # Global state (Zustand)
│   │   ├── index.ts                # Combined store
│   │   ├── authStore.ts            # Auth state
│   │   ├── syncStore.ts            # Sync state
│   │   ├── settingsStore.ts        # App settings
│   │   └── types.ts                # Store types
│   │
│   ├── i18n/                        # Internationalization
│   │   ├── index.ts
│   │   ├── en.json                 # English translations
│   │   └── si.json                 # Sinhala translations
│   │
│   ├── theme/
│   │   ├── colors.ts
│   │   ├── typography.ts
│   │   ├── spacing.ts
│   │   └── index.ts
│   │
│   └── config/
│       ├── env.ts                  # Environment configuration
│       └── constants.ts            # App constants
│
├── assets/
│   ├── fonts/
│   ├── images/
│   ├── icons/
│   └── splash.png
│
├── .expo/
├── App.tsx                         # Root component
├── app.json                        # Expo configuration
├── babel.config.js
├── tsconfig.json
├── package.json
├── .env.example
├── .gitignore
└── README.md
```

---

## Architecture Principles

### Feature-Based Organization

Each feature is self-contained with:

- Screens (UI)
- Components (reusable UI pieces)
- Hooks (logic abstraction)
- Store (state management)
- Services (API/business logic)
- Types (TypeScript definitions)

### State Management Strategy

**Zustand for Global State:**

- Authentication state
- User profile
- Sync queue
- App settings

**React Query for Server State:**

- API data caching
- Automatic refetching
- Optimistic updates
- Background sync

**Local Component State:**

- UI-only state (dropdowns, modals)
- Form inputs

---

## Key Services

### API Client (apiClient.ts)

```typescript
// Axios instance with interceptors
- JWT token injection
- Automatic token refresh
- Error handling
- Request/response logging
- Network status handling
```

### Sync Manager (SyncManager.ts)

```typescript
- Queue offline operations
- Background sync worker
- Conflict resolution
- Retry logic with exponential backoff
- Sync status tracking
```

### Location Service (LocationService.ts)

```typescript
- Request location permissions
- Start/stop GPS tracking
- Get current location
- Track user movement
- Background location updates
- Battery optimization
```

### Database Service (DatabaseService.ts)

```typescript
// SQLite database using expo-sqlite
- Store measurements offline
- Store job data
- Store invoices
- Store expenses
- Query local data
- Sync status tracking
```

---

## Offline-First Strategy

### Write Operations

1. Save to local SQLite database
2. Add to sync queue
3. Show success to user immediately
4. Background task syncs when online
5. Update local record with server ID

### Read Operations

1. Check local database first
2. Display cached data immediately
3. Fetch from API in background
4. Update UI with fresh data
5. Handle conflicts gracefully

### Conflict Resolution

- **Strategy**: Last-write-wins with server authority
- Server data takes precedence
- Local changes are merged when possible
- User is notified of conflicts

---

## GPS Measurement Flow

### Walk-Around Mode

```typescript
1. User starts measurement
2. Request location permissions
3. Start GPS tracking (every 2-5 seconds)
4. Collect coordinates with accuracy
5. Display on map in real-time
6. User completes measurement
7. Calculate area using Turf.js
8. Save to local database
9. Queue for sync
```

### Point-Based Mode

```typescript
1. User taps on map to add points
2. Draw polygon as points are added
3. Minimum 3 points required
4. Calculate area in real-time
5. Save measurement
6. Queue for sync
```

---

## Navigation Structure

```
Auth Stack (if not authenticated)
├── Login
├── Register
└── Forgot Password

Main App (if authenticated)
├── Tab Navigator (Bottom Tabs)
│   ├── Dashboard
│   ├── Measurements
│   ├── Jobs
│   ├── Invoices
│   └── More
│
└── Stack Navigator
    ├── Create Measurement
    ├── Measurement Detail
    ├── Create Job
    ├── Job Detail
    ├── Create Invoice
    ├── Invoice Detail
    ├── Tracking Map
    ├── Reports
    └── Settings
```

---

## Localization (i18n)

### Supported Languages

- **English** (en)
- **Sinhala** (si)

### Usage

```typescript
import { t } from "@/i18n";

const title = t("measurement.title");
const area = t("measurement.area", { value: 2.5, unit: "acres" });
```

### Translation Keys Structure

```json
{
  "common": {
    "save": "Save",
    "cancel": "Cancel",
    "delete": "Delete"
  },
  "measurement": {
    "title": "Land Measurement",
    "start": "Start Measuring",
    "area": "{{value}} {{unit}}"
  },
  "jobs": {
    "title": "Jobs",
    "create": "Create Job",
    "status": {
      "pending": "Pending",
      "in_progress": "In Progress",
      "completed": "Completed"
    }
  }
}
```

---

## Styling & Theming

### Theme System

- Color palette (primary, secondary, background, text)
- Typography (font sizes, weights, line heights)
- Spacing scale (4pt grid system)
- Component styles

### Responsive Design

- Adapt to different screen sizes
- Handle landscape orientation
- Support tablets
- Accessible touch targets (min 44pt)

---

## Performance Optimization

### GPS & Battery

- Use appropriate location accuracy
- Batch GPS updates
- Stop tracking when not needed
- Use significant location changes
- Background location optimization

### Rendering

- Memoize expensive calculations
- Use FlatList for long lists
- Optimize images (compression, caching)
- Lazy load screens
- Remove console.logs in production

### Data

- Paginate API requests
- Cache API responses
- Debounce search inputs
- Virtualize long lists
- Compress images before upload

---

## Security

1. **Secure Storage**: Use Expo SecureStore for tokens
2. **SSL Pinning**: Enforce HTTPS only
3. **Input Validation**: Validate all user inputs
4. **Sensitive Data**: Never log tokens/passwords
5. **Permissions**: Request minimal permissions
6. **Code Obfuscation**: Use ProGuard/R8 for Android

---

## Testing Strategy

### Unit Tests (Jest)

- Test utility functions
- Test services
- Test hooks
- Test state management

### Component Tests (React Testing Library)

- Test component rendering
- Test user interactions
- Test conditional rendering

### E2E Tests (Detox)

- Test critical user flows
- Test offline functionality
- Test sync behavior

---

## Build & Deployment

### Development Build

```bash
cd mobile
npm run start
# or
npx expo start
```

### Production Build (EAS Build)

```bash
# Install EAS CLI
npm install -g eas-cli

# Login to Expo
eas login

# Configure EAS
eas build:configure

# Build for Android
eas build --platform android --profile production

# Build for iOS (requires Apple Developer account)
eas build --platform ios --profile production

# Submit to stores
eas submit --platform android
eas submit --platform ios
```

---

## Environment Configuration

### .env.example

```env
API_BASE_URL=https://api.geo-ops.lk/api/v1
GOOGLE_MAPS_API_KEY=your_google_maps_key
MAPBOX_ACCESS_TOKEN=your_mapbox_token
SENTRY_DSN=your_sentry_dsn
APP_VERSION=1.0.0
```

### env.ts

```typescript
const ENV = {
  API_BASE_URL: process.env.API_BASE_URL,
  GOOGLE_MAPS_API_KEY: process.env.GOOGLE_MAPS_API_KEY,
  // ... other config
};

export default ENV;
```

---

## Dependencies (package.json)

### Core

- react-native
- expo
- typescript

### Navigation

- @react-navigation/native
- @react-navigation/stack
- @react-navigation/bottom-tabs

### State Management

- zustand
- @tanstack/react-query

### UI

- react-native-reanimated
- react-native-gesture-handler
- react-native-safe-area-context

### Maps & Location

- react-native-maps (or expo-maps)
- expo-location
- @turf/turf (area calculation)

### Storage

- expo-secure-store
- @react-native-async-storage/async-storage
- expo-sqlite

### API & Network

- axios
- react-query

### Offline & Sync

- expo-background-fetch
- expo-task-manager
- @react-native-community/netinfo

### PDF & Documents

- expo-print
- expo-sharing
- react-native-pdf

### i18n

- react-i18next
- i18next

### Utils

- date-fns
- lodash

---

## Best Practices

1. **TypeScript**: Use strict mode, type everything
2. **Component Structure**: Keep components small and focused
3. **Custom Hooks**: Extract logic into reusable hooks
4. **Error Handling**: Always handle errors gracefully
5. **Loading States**: Show loading indicators
6. **Empty States**: Handle empty data with meaningful messages
7. **Accessibility**: Use accessible components
8. **Code Style**: Use ESLint + Prettier
9. **Git**: Commit often with meaningful messages
10. **Documentation**: Document complex logic
