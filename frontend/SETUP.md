# GeoOps Mobile Frontend - Setup Guide

## Project Structure

The GeoOps mobile frontend is built with **Expo SDK 50** and **React Native** using **TypeScript**. It uses file-based routing with **Expo Router**.

### Directory Structure

```
frontend/
├── app/                          # Expo Router pages (file-based routing)
│   ├── _layout.tsx              # Root layout with navigation stack
│   ├── index.tsx                # Home screen
│   └── +not-found.tsx           # 404 screen
├── src/
│   ├── App.tsx                  # Root app component with providers
│   ├── components/              # Shared UI components
│   │   ├── Button.tsx
│   │   ├── ErrorMessage.tsx
│   │   ├── LoadingSpinner.tsx
│   │   └── index.ts
│   ├── features/                # Feature modules (auth, fields, mapping, etc.)
│   ├── hooks/                   # Custom React hooks
│   │   ├── useApi.ts           # API calls wrapper
│   │   ├── useLocation.ts      # Location tracking
│   │   └── index.ts
│   ├── services/                # API services
│   │   ├── api.ts              # Axios instance with interceptors
│   │   ├── authService.ts      # Authentication endpoints
│   │   ├── fieldService.ts     # Field management endpoints
│   │   └── index.ts
│   ├── store/                   # Zustand state management
│   │   ├── userStore.ts        # User state
│   │   ├── fieldStore.ts       # Fields state
│   │   └── index.ts
│   ├── types/                   # TypeScript type definitions
│   │   └── index.ts
│   ├── utils/                   # Utility functions
│   │   ├── formatting.ts       # Date and format utilities
│   │   ├── validation.ts       # Input validation
│   │   ├── storage.ts          # AsyncStorage wrapper
│   │   └── index.ts
│   └── locales/                 # Internationalization (i18n)
│       ├── index.ts            # Translation strings
│       └── i18n.ts             # i18next configuration
├── assets/                      # Static assets
│   ├── icon.png
│   ├── splash.png
│   ├── adaptive-icon.png
│   ├── favicon.png
│   └── notification-icon.png
├── index.js                     # Expo entry point
├── babel.config.js              # Babel configuration with path aliases
├── tsconfig.json                # TypeScript configuration
├── tsconfig.app.json           # App-specific TypeScript config
├── jest.config.js              # Jest testing configuration
├── jest.setup.js               # Jest setup file
├── .eslintrc.json              # ESLint configuration
├── .prettierrc                 # Prettier code formatting config
├── .gitignore                  # Git ignore rules
├── app.json                    # Expo app configuration
├── package.json                # Dependencies and scripts
└── README.md                   # Project documentation
```

## Installation

### Prerequisites

- Node.js v18+ and npm v9+
- Expo CLI: `npm install -g expo-cli`
- For iOS: Xcode and CocoaPods
- For Android: Android Studio and Android SDK

### Setup Steps

1. **Install Dependencies**

   ```bash
   npm install
   ```

2. **Configure Environment**
   - Copy `.env.example` to `.env`
   - Update environment variables as needed

3. **Start Development Server**

   ```bash
   npm run start
   ```

4. **Run on Platform**
   - **iOS**: `npm run ios`
   - **Android**: `npm run android`
   - **Web**: `npm run web`

## Available Scripts

- `npm run start` - Start Expo development server
- `npm run ios` - Build and run on iOS simulator
- `npm run android` - Build and run on Android emulator
- `npm run web` - Run on web (development)
- `npm run test` - Run tests with Jest
- `npm run lint` - Run ESLint
- `npm run format` - Format code with Prettier

## Key Technologies

### Core

- **React Native** - Cross-platform mobile framework
- **Expo** - Managed React Native platform
- **Expo Router** - File-based routing (v3.4)
- **TypeScript** - Static typing

### State Management

- **Zustand** - Lightweight state management

### Networking & Storage

- **Axios** - HTTP client with interceptors
- **Expo Secure Store** - Secure credential storage
- **AsyncStorage** - Local data persistence
- **Expo Location** - Location services

### UI & Animations

- **React Native** - Core components
- **React Native Reanimated** - Smooth animations
- **React Native Gesture Handler** - Gesture recognition
- **React Native Safe Area Context** - Safe area handling

### Maps & Geolocation

- **React Native Maps** - Map visualization
- **Expo Location** - Geolocation APIs

### Internationalization

- **i18next** - i18n framework
- **react-i18next** - React bindings for i18next

### Development Tools

- **ESLint** - Code linting
- **Prettier** - Code formatting
- **Jest** - Testing framework
- **TypeScript** - Type checking

## Project Configuration

### Path Aliases

The project uses TypeScript path aliases for cleaner imports:

```typescript
import { Button } from '@components/Button';
import { useLocation } from '@hooks/useLocation';
import { fieldService } from '@services/fieldService';
import { useFieldStore } from '@store/fieldStore';
import { User } from '@types/index';
import { formatDateString } from '@utils/formatting';
import i18n from '@locales/i18n';
```

These are configured in:

- `tsconfig.json` - TypeScript paths
- `babel.config.js` - Babel module resolver
- `jest.config.js` - Jest module name mapping

### Environment Variables

Create a `.env` file based on `.env.example`:

```env
API_URL=http://localhost:3000/api
ENV=development
```

Access in code:

```typescript
import Constants from 'expo-constants';
const apiUrl = Constants.expoConfig?.extra?.apiUrl;
```

## Code Style

### TypeScript Conventions

- Use strict TypeScript mode
- Define interfaces for all data structures
- Use type-safe API responses

### Component Conventions

- Use functional components with hooks
- Export components from index files for cleaner imports
- Separate styles using StyleSheet

### File Naming

- Components: PascalCase (e.g., `Button.tsx`)
- Hooks: camelCase with `use` prefix (e.g., `useApi.ts`)
- Utils/Services: camelCase (e.g., `formatting.ts`)
- Types: lowercase with descriptive names (e.g., `index.ts`)

## Testing

Run tests with:

```bash
npm run test
```

Tests are configured with Jest and should be placed alongside source files with `.test.ts` or `.test.tsx` extensions.

## Building for Production

### Web Build

```bash
expo build:web
```

### EAS Build (Recommended for iOS/Android)

1. Install EAS CLI: `npm install -g eas-cli`
2. Login: `eas login`
3. Configure: `eas build:configure`
4. Build: `eas build --platform ios` or `--platform android`

## Troubleshooting

### Clear Cache

```bash
expo start --clear
```

### Reset Node Modules

```bash
rm -rf node_modules && npm install
```

### Clear Watchman

```bash
watchman watch-del-all
```

## Next Steps

1. **Create Feature Modules** - Add authentication, fields, and mapping features
2. **Add Asset Images** - Place icons and splash screens in `assets/`
3. **Configure API** - Update `.env` with backend API URL
4. **Implement Screens** - Add new screens in the `app/` directory
5. **Add Tests** - Create test files for components and utilities

## Documentation

- [Expo Documentation](https://docs.expo.dev/)
- [React Native Documentation](https://reactnative.dev/)
- [Expo Router Guide](https://docs.expo.dev/routing/introduction/)
- [Zustand Documentation](https://github.com/pmndrs/zustand)
- [TypeScript React](https://react-typescript-cheatsheet.netlify.app/)

## Support

For issues and questions:

- Check the [Expo Discord Community](https://discord.com/invite/4gtbvdV)
- Review [Common Expo Issues](https://docs.expo.dev/troubleshooting/)
- Check project GitHub issues
