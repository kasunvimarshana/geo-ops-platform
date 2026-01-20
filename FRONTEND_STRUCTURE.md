# Frontend Structure Documentation

The frontend of the GeoOps Platform is built using TypeScript and Expo, providing a robust and scalable mobile application. Below is an overview of the structure of the frontend application, detailing the organization of files and directories.

## Frontend Directory Structure

```
mobile/
├── app/                          # Main application components
│   ├── (auth)/                   # Authentication-related screens
│   │   ├── login.tsx             # Login screen component
│   │   └── register.tsx          # Registration screen component
│   ├── (tabs)/                   # Tab navigation screens
│   │   ├── _layout.tsx           # Layout for tab navigation
│   │   ├── dashboard.tsx         # Dashboard screen component
│   │   ├── jobs.tsx              # Jobs management screen component
│   │   ├── lands.tsx             # Lands management screen component
│   │   └── profile.tsx           # User profile management screen component
│   ├── _layout.tsx                # Main layout for the mobile app
│   ├── index.tsx                  # Entry point for the mobile application
│   ├── jobs/                      # Job-related components
│   │   ├── [id].tsx              # Job detail screen component
│   │   └── create.tsx            # Job creation screen component
│   └── lands/                     # Land-related components
│       ├── [id].tsx              # Land detail screen component
│       └── measure.tsx           # Land measurement screen component
├── assets/                        # Static assets
│   ├── fonts                      # Custom fonts
│   ├── images                     # Images used in the app
│   └── locales/                  # Localization files
│       ├── en.json               # English translations
│       └── si.json               # Sinhala translations
├── src/                           # Source code
│   ├── api/                       # API calls
│   │   ├── auth.ts               # Authentication API calls
│   │   ├── client.ts             # API client setup
│   │   ├── interceptors.ts       # API request/response interceptors
│   │   ├── jobs.ts               # Job management API calls
│   │   └── lands.ts              # Land management API calls
│   ├── components/                # Reusable components
│   │   ├── common/               # Common UI components
│   │   │   ├── Button.tsx        # Reusable button component
│   │   │   ├── Card.tsx          # Reusable card component
│   │   │   ├── Input.tsx         # Reusable input component
│   │   │   └── Loading.tsx       # Loading spinner component
│   │   ├── jobs/                 # Job-specific components
│   │   │   ├── JobCard.tsx       # Job card component
│   │   │   └── JobTracker.tsx    # Job tracking component
│   │   ├── lands/                # Land-specific components
│   │   │   ├── LandCard.tsx      # Land card component
│   │   │   └── MapPolygon.tsx    # Component for visualizing land measurements
│   │   └── maps/                 # Map-related components
│   │       ├── GPSTracker.tsx    # GPS tracking component
│   │       └── MapView.tsx       # Map display component
│   ├── constants/                 # Constants used in the app
│   │   ├── Colors.ts             # Color constants
│   │   └── Config.ts             # Configuration constants
│   ├── database/                  # Database-related files
│   │   ├── migrations/            # Database migrations
│   │   │   ├── 001_create_lands_table.ts  # Migration for lands table
│   │   │   ├── 002_create_jobs_table.ts   # Migration for jobs table
│   │   │   └── 003_create_sync_queue_table.ts # Migration for sync queue table
│   │   ├── models/                # Database models
│   │   │   ├── Job.ts             # Job model
│   │   │   ├── Land.ts            # Land model
│   │   │   └── SyncQueue.ts       # SyncQueue model
│   │   └── sqlite.ts              # SQLite database setup
│   ├── hooks/                     # Custom hooks
│   │   ├── useAuth.ts            # Hook for authentication state
│   │   ├── useGPS.ts             # Hook for GPS functionality
│   │   ├── useJobs.ts            # Hook for job management
│   │   └── useOfflineSync.ts      # Hook for offline data synchronization
│   ├── navigation/                # Navigation setup
│   │   ├── AuthNavigator.tsx      # Navigation for authentication screens
│   │   ├── RootNavigator.tsx      # Main navigation structure
│   │   └── TabNavigator.tsx       # Tab navigation structure
│   ├── services/                  # Service layer for business logic
│   │   ├── GPSService.ts          # GPS-related functionalities
│   │   ├── LandMeasurementService.ts # Land measurement functionalities
│   │   ├── NotificationService.ts  # Notification management
│   │   └── SyncService.ts         # Offline data synchronization
│   ├── store/                     # State management
│   │   ├── authStore.ts           # Zustand store for authentication
│   │   ├── jobStore.ts            # Zustand store for jobs
│   │   ├── landStore.ts           # Zustand store for lands
│   │   └── syncStore.ts           # Zustand store for synchronization
│   ├── types/                     # Type definitions
│   │   ├── api.ts                 # API response types
│   │   ├── job.ts                 # Job-related types
│   │   ├── land.ts                # Land-related types
│   │   └── user.ts                # User-related types
│   └── utils/                     # Utility functions
│       ├── calculations.ts        # Calculation utilities
│       ├── date.ts                # Date utilities
│       ├── gps.ts                 # GPS utilities
│       └── validation.ts          # Validation utilities
├── app.json                       # Expo app configuration
├── babel.config.js                # Babel configuration
├── eas.json                       # EAS Build configuration
├── package.json                   # JavaScript dependencies and scripts
├── tsconfig.json                  # TypeScript configuration
└── README.md                      # Documentation for the mobile project
```

## Key Components

- **Authentication**: Handles user login and registration.
- **Dashboard**: Displays key metrics and insights for users.
- **Job Management**: Allows users to create, view, and manage jobs.
- **Land Management**: Enables users to measure and manage land parcels.
- **Offline Support**: Ensures the app functions without an internet connection, syncing data when online.

## Best Practices

- **Modular Structure**: Components are organized by feature, promoting reusability and maintainability.
- **Type Safety**: TypeScript is used throughout the application to ensure type safety and reduce runtime errors.
- **State Management**: Zustand is utilized for state management, providing a simple and efficient way to manage application state.

This structure is designed to facilitate scalability and reliability, ensuring that the application can grow and adapt to future requirements.
