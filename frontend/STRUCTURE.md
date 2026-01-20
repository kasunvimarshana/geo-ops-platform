# Frontend - React Native (Expo) + TypeScript

## Directory Structure

```
frontend/
├── .expo/
├── assets/
│   ├── fonts/
│   ├── images/
│   ├── icons/
│   └── locales/
│       ├── en.json
│       └── si.json
├── src/
│   ├── api/                      # API service layer
│   │   ├── client.ts            # Axios instance with interceptors
│   │   ├── endpoints.ts         # API endpoint constants
│   │   ├── auth.api.ts
│   │   ├── measurement.api.ts
│   │   ├── job.api.ts
│   │   ├── tracking.api.ts
│   │   ├── invoice.api.ts
│   │   ├── payment.api.ts
│   │   ├── expense.api.ts
│   │   ├── machine.api.ts
│   │   └── sync.api.ts
│   ├── components/               # Reusable UI components
│   │   ├── common/
│   │   │   ├── Button.tsx
│   │   │   ├── Card.tsx
│   │   │   ├── Input.tsx
│   │   │   ├── Picker.tsx
│   │   │   ├── DatePicker.tsx
│   │   │   ├── Loading.tsx
│   │   │   ├── ErrorBoundary.tsx
│   │   │   └── EmptyState.tsx
│   │   ├── layout/
│   │   │   ├── Screen.tsx
│   │   │   ├── Header.tsx
│   │   │   ├── TabBar.tsx
│   │   │   └── SafeArea.tsx
│   │   ├── map/
│   │   │   ├── MapView.tsx
│   │   │   ├── Marker.tsx
│   │   │   ├── Polygon.tsx
│   │   │   └── LocationButton.tsx
│   │   └── measurement/
│   │       ├── MeasurementCard.tsx
│   │       ├── PolygonDrawer.tsx
│   │       └── AreaDisplay.tsx
│   ├── features/                 # Feature-based modules
│   │   ├── auth/
│   │   │   ├── screens/
│   │   │   │   ├── LoginScreen.tsx
│   │   │   │   ├── RegisterScreen.tsx
│   │   │   │   └── ForgotPasswordScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── LoginForm.tsx
│   │   │   │   └── SocialLogin.tsx
│   │   │   └── hooks/
│   │   │       └── useAuth.ts
│   │   ├── measurement/
│   │   │   ├── screens/
│   │   │   │   ├── MeasurementListScreen.tsx
│   │   │   │   ├── MeasurementDetailScreen.tsx
│   │   │   │   ├── CreateMeasurementScreen.tsx
│   │   │   │   └── WalkAroundScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── MeasurementList.tsx
│   │   │   │   ├── PolygonMap.tsx
│   │   │   │   └── MeasurementStats.tsx
│   │   │   └── hooks/
│   │   │       ├── useMeasurement.ts
│   │   │       ├── useGPSTracking.ts
│   │   │       └── useAreaCalculation.ts
│   │   ├── jobs/
│   │   │   ├── screens/
│   │   │   │   ├── JobListScreen.tsx
│   │   │   │   ├── JobDetailScreen.tsx
│   │   │   │   ├── CreateJobScreen.tsx
│   │   │   │   └── JobMapScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── JobCard.tsx
│   │   │   │   ├── JobStatusBadge.tsx
│   │   │   │   ├── AssignmentCard.tsx
│   │   │   │   └── JobTimeline.tsx
│   │   │   └── hooks/
│   │   │       └── useJobs.ts
│   │   ├── tracking/
│   │   │   ├── screens/
│   │   │   │   ├── LiveTrackingScreen.tsx
│   │   │   │   ├── TrackingHistoryScreen.tsx
│   │   │   │   └── DriverMapScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── DriverMarker.tsx
│   │   │   │   ├── RoutePolyline.tsx
│   │   │   │   └── TrackingStats.tsx
│   │   │   └── hooks/
│   │   │       └── useTracking.ts
│   │   ├── billing/
│   │   │   ├── screens/
│   │   │   │   ├── InvoiceListScreen.tsx
│   │   │   │   ├── InvoiceDetailScreen.tsx
│   │   │   │   ├── CreateInvoiceScreen.tsx
│   │   │   │   └── PaymentScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── InvoiceCard.tsx
│   │   │   │   ├── InvoiceItems.tsx
│   │   │   │   ├── PaymentForm.tsx
│   │   │   │   └── InvoicePDF.tsx
│   │   │   └── hooks/
│   │   │       ├── useInvoices.ts
│   │   │       └── usePayments.ts
│   │   ├── expenses/
│   │   │   ├── screens/
│   │   │   │   ├── ExpenseListScreen.tsx
│   │   │   │   ├── ExpenseDetailScreen.tsx
│   │   │   │   └── AddExpenseScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── ExpenseCard.tsx
│   │   │   │   ├── ExpenseForm.tsx
│   │   │   │   └── ExpenseChart.tsx
│   │   │   └── hooks/
│   │   │       └── useExpenses.ts
│   │   ├── dashboard/
│   │   │   ├── screens/
│   │   │   │   ├── DashboardScreen.tsx
│   │   │   │   └── ReportsScreen.tsx
│   │   │   ├── components/
│   │   │   │   ├── StatsCard.tsx
│   │   │   │   ├── RecentActivity.tsx
│   │   │   │   └── QuickActions.tsx
│   │   │   └── hooks/
│   │   │       └── useDashboard.ts
│   │   └── profile/
│   │       ├── screens/
│   │       │   ├── ProfileScreen.tsx
│   │       │   ├── SettingsScreen.tsx
│   │       │   └── SubscriptionScreen.tsx
│   │       ├── components/
│   │       │   ├── ProfileHeader.tsx
│   │       │   ├── SettingItem.tsx
│   │       │   └── LanguageSelector.tsx
│   │       └── hooks/
│   │           └── useProfile.ts
│   ├── navigation/               # Navigation configuration
│   │   ├── AppNavigator.tsx
│   │   ├── AuthNavigator.tsx
│   │   ├── MainNavigator.tsx
│   │   ├── TabNavigator.tsx
│   │   └── types.ts
│   ├── stores/                   # State management (Zustand)
│   │   ├── authStore.ts
│   │   ├── measurementStore.ts
│   │   ├── jobStore.ts
│   │   ├── trackingStore.ts
│   │   ├── invoiceStore.ts
│   │   ├── expenseStore.ts
│   │   ├── syncStore.ts
│   │   └── settingsStore.ts
│   ├── services/                 # Business logic services
│   │   ├── storage/
│   │   │   ├── database.ts      # SQLite setup
│   │   │   ├── mmkv.ts          # MMKV setup
│   │   │   └── secure.ts        # Secure storage
│   │   ├── location/
│   │   │   ├── gps.service.ts
│   │   │   ├── geofence.service.ts
│   │   │   └── area-calculator.service.ts
│   │   ├── sync/
│   │   │   ├── sync.service.ts
│   │   │   ├── conflict-resolver.service.ts
│   │   │   └── queue.service.ts
│   │   ├── background/
│   │   │   ├── tracking.task.ts
│   │   │   └── sync.task.ts
│   │   ├── pdf/
│   │   │   └── pdf-generator.service.ts
│   │   └── notification/
│   │       └── notification.service.ts
│   ├── utils/                    # Utility functions
│   │   ├── formatters.ts
│   │   ├── validators.ts
│   │   ├── date.ts
│   │   ├── number.ts
│   │   ├── geo.ts
│   │   └── constants.ts
│   ├── hooks/                    # Custom React hooks
│   │   ├── useNetwork.ts
│   │   ├── useLocation.ts
│   │   ├── usePermissions.ts
│   │   ├── useDebounce.ts
│   │   └── useThrottle.ts
│   ├── types/                    # TypeScript type definitions
│   │   ├── api.types.ts
│   │   ├── models.types.ts
│   │   ├── navigation.types.ts
│   │   └── index.ts
│   ├── theme/                    # Theming and styles
│   │   ├── colors.ts
│   │   ├── typography.ts
│   │   ├── spacing.ts
│   │   └── theme.ts
│   ├── i18n/                     # Internationalization
│   │   ├── index.ts
│   │   ├── en.ts
│   │   └── si.ts
│   ├── config/                   # App configuration
│   │   ├── api.config.ts
│   │   ├── map.config.ts
│   │   └── app.config.ts
│   └── App.tsx                   # Root component
├── .env.example
├── .gitignore
├── app.json
├── babel.config.js
├── metro.config.js
├── tsconfig.json
├── package.json
└── README.md
```

## Key Architectural Decisions

### 1. Feature-Based Architecture

Each feature is self-contained with:
- **Screens**: Full-page components
- **Components**: Feature-specific reusable components
- **Hooks**: Custom hooks for feature logic
- **API integration**: Via centralized API layer

Benefits:
- Easy to find related code
- Scalable as features grow
- Clear separation of concerns
- Team can work on features independently

### 2. State Management - Zustand

Simple, lightweight state management:
```typescript
// stores/authStore.ts
import create from 'zustand';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  login: (credentials: LoginDTO) => Promise<void>;
  logout: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  login: async (credentials) => {
    const { user, tokens } = await authApi.login(credentials);
    set({ user, token: tokens.access_token, isAuthenticated: true });
  },
  logout: () => {
    set({ user: null, token: null, isAuthenticated: false });
  },
}));
```

### 3. API Layer Architecture

Centralized HTTP client with interceptors:
```typescript
// api/client.ts
import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
});

// Request interceptor - add auth token
apiClient.interceptors.request.use((config) => {
  const token = useAuthStore.getState().token;
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Response interceptor - handle errors
apiClient.interceptors.response.use(
  (response) => response.data,
  async (error) => {
    if (error.response?.status === 401) {
      // Token expired, try refresh
      await refreshToken();
    }
    return Promise.reject(error);
  }
);
```

### 4. Offline-First Architecture

**Data Flow:**
```
User Action → Local SQLite → Sync Queue → Background Sync → Server
                    ↓
              Immediate UI Update (Optimistic)
```

**Implementation:**
- SQLite for structured offline data
- MMKV for key-value storage (faster)
- Sync queue tracks pending changes
- Background task syncs when online
- Conflict resolution on server response

### 5. GPS Tracking Service

Optimized for battery and accuracy:
```typescript
// services/location/gps.service.ts
class GPSService {
  // Start tracking with options
  async startTracking(options: {
    accuracy: 'high' | 'balanced' | 'low';
    interval: number; // milliseconds
    distanceFilter: number; // meters
  }) {
    return await Location.startLocationUpdatesAsync(TASK_NAME, {
      accuracy: this.mapAccuracy(options.accuracy),
      timeInterval: options.interval,
      distanceInterval: options.distanceFilter,
      foregroundService: {
        notificationTitle: 'GPS Tracking',
        notificationBody: 'Tracking your location',
      },
    });
  }
}
```

### 6. Navigation Structure

Stack + Tab Navigation:
```
App
├── Auth Stack (if not authenticated)
│   ├── Login
│   ├── Register
│   └── Forgot Password
└── Main Stack (if authenticated)
    └── Tab Navigator
        ├── Dashboard Tab
        ├── Measurements Tab
        ├── Jobs Tab
        ├── Billing Tab
        └── Profile Tab
```

### 7. Type Safety

Strict TypeScript configuration:
```json
{
  "compilerOptions": {
    "strict": true,
    "noImplicitAny": true,
    "strictNullChecks": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true
  }
}
```

### 8. Custom Hooks Pattern

Reusable business logic:
```typescript
// hooks/useMeasurement.ts
export const useMeasurement = () => {
  const [isTracking, setIsTracking] = useState(false);
  const [points, setPoints] = useState<GPSPoint[]>([]);
  const measurementStore = useMeasurementStore();
  
  const startMeasurement = async () => {
    const hasPermission = await requestLocationPermission();
    if (!hasPermission) return;
    
    setIsTracking(true);
    await GPSService.startTracking({
      accuracy: 'high',
      interval: 1000,
      distanceFilter: 1,
    });
  };
  
  const stopMeasurement = async () => {
    setIsTracking(false);
    await GPSService.stopTracking();
    
    const area = calculateArea(points);
    await measurementStore.save({
      points,
      area,
    });
  };
  
  return {
    isTracking,
    points,
    startMeasurement,
    stopMeasurement,
  };
};
```

### 9. Localization Strategy

i18n with dynamic switching:
```typescript
// i18n/index.ts
import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import en from './en';
import si from './si';

i18n.use(initReactI18next).init({
  resources: {
    en: { translation: en },
    si: { translation: si },
  },
  lng: 'en',
  fallbackLng: 'en',
  interpolation: {
    escapeValue: false,
  },
});

// Usage in component
const { t } = useTranslation();
<Text>{t('measurement.startTracking')}</Text>
```

### 10. Performance Optimizations

**React Native Best Practices:**
- FlatList for long lists with `keyExtractor` and `getItemLayout`
- React.memo for expensive components
- useMemo/useCallback to prevent re-renders
- Image caching with expo-image
- Lazy loading for screens
- Throttle/debounce for GPS updates

**Map Performance:**
- Limit visible markers
- Cluster markers for high density
- Simplify polygons for distant zoom
- Use native map components

## Design Patterns

### 1. Container/Presenter Pattern

Separate logic from UI:
```typescript
// MeasurementListContainer.tsx (logic)
const MeasurementListContainer = () => {
  const { measurements, loading, refresh } = useMeasurements();
  
  return (
    <MeasurementListPresenter
      measurements={measurements}
      loading={loading}
      onRefresh={refresh}
    />
  );
};

// MeasurementListPresenter.tsx (UI)
const MeasurementListPresenter = ({ measurements, loading, onRefresh }) => {
  return (
    <FlatList
      data={measurements}
      renderItem={({ item }) => <MeasurementCard item={item} />}
      refreshing={loading}
      onRefresh={onRefresh}
    />
  );
};
```

### 2. Service Layer Pattern

Business logic in services:
- GPS calculations in GPSService
- Area calculations in AreaCalculatorService
- Sync logic in SyncService
- PDF generation in PDFService

### 3. Repository Pattern (Offline)

Abstract data source:
```typescript
class MeasurementRepository {
  async getAll(): Promise<Measurement[]> {
    // Try network first
    if (isOnline) {
      const data = await api.measurements.getAll();
      await db.measurements.replaceAll(data);
      return data;
    }
    // Fallback to local
    return await db.measurements.getAll();
  }
}
```

## Security Measures

1. **Secure Storage**
   - Tokens in expo-secure-store
   - Sensitive data encrypted
   - No plain text passwords

2. **API Security**
   - JWT tokens in headers
   - Token refresh on expiry
   - Logout clears all tokens

3. **Input Validation**
   - Form validation with Yup
   - Type checking with TypeScript
   - Sanitize user input

4. **Permissions**
   - Request location permission
   - Request camera permission (for receipts)
   - Request storage permission

## Testing Strategy

1. **Component Tests**
   - Jest + React Native Testing Library
   - Test user interactions
   - Snapshot tests for UI

2. **Hook Tests**
   - Test custom hooks in isolation
   - Mock API calls
   - Test state updates

3. **Integration Tests**
   - Test feature flows
   - Mock backend responses
   - Test offline scenarios

## Build & Deployment

1. **Development**
   - Expo Go for quick testing
   - Hot reload for fast iteration

2. **Staging**
   - Development builds with Expo
   - TestFlight (iOS) / Internal Testing (Android)

3. **Production**
   - Production builds with EAS
   - OTA updates for quick fixes
   - App Store & Play Store

---

This architecture ensures maintainability, performance, offline reliability, and excellent user experience for rural users in Sri Lanka.
