# Geo Ops Platform - Mobile App

Production-ready React Native mobile application for GPS land measurement and field service management.

## Features

- ✅ GPS land measurement with walk-around tracking
- ✅ Real-time area calculation
- ✅ Offline data storage & sync
- ✅ Map visualization
- ✅ Job management
- ✅ Invoice generation & PDF export
- ✅ Multi-language support (English, Sinhala)
- ✅ Role-based access control

## Technology Stack

- **Framework**: React Native with Expo
- **Language**: TypeScript
- **Navigation**: Expo Router
- **State Management**: Zustand
- **HTTP Client**: Axios
- **Maps**: React Native Maps
- **Storage**: AsyncStorage, SQLite

## Prerequisites

- Node.js >= 18.0.0
- Expo CLI
- iOS Simulator (Mac) or Android Studio (for Android development)

## Getting Started

### 1. Install Dependencies

```bash
npm install
```

### 2. Configure Environment

Create a `.env` file in the mobile directory:

```bash
cp .env.example .env
```

Edit the `.env` file:

```
EXPO_PUBLIC_API_URL=http://localhost:3000/api/v1
```

For Android emulator, use `http://10.0.2.2:3000/api/v1`
For iOS simulator, use `http://localhost:3000/api/v1`

**Optional**: Add Google Maps API keys for production builds (see MAP_IMPLEMENTATION.md)

### 3. Start Development Server

```bash
npm start
```

This will open Expo Dev Tools in your browser.

### 4. Run on Device/Emulator

- **iOS**: Press `i` to open iOS simulator
- **Android**: Press `a` to open Android emulator
- **Device**: Scan QR code with Expo Go app

## Project Structure

```
mobile/
├── app/                    # Expo Router pages
│   ├── (tabs)/            # Tab navigation screens
│   ├── auth/              # Authentication screens
│   └── _layout.tsx        # Root layout
├── src/
│   ├── components/        # Reusable UI components
│   ├── constants/         # App constants
│   ├── navigation/        # Navigation config
│   ├── screens/           # Screen components
│   ├── services/          # API services
│   ├── store/             # Zustand stores
│   ├── types/             # TypeScript types
│   └── utils/             # Helper functions
└── assets/                # Images, fonts, etc.
```

## Key Features

### GPS Land Measurement

- Walk-around measurement using device GPS
- Real-time coordinate tracking
- **Live map visualization with polygon rendering**
- Automatic area calculation (acres/hectares)
- Save measurements with metadata
- **View measurements on interactive map**
- **Map previews in history with polygon overlay**

### Offline Support

- Store measurements locally when offline
- Auto-sync when connection restored
- SQLite for local data persistence
- Conflict resolution strategy

### Authentication

- JWT-based authentication
- Role-based access control
- Secure token storage
- Auto token refresh

### Map Features

- **Real-time map visualization during measurement**
- **Display measured polygons and GPS points**
- **Interactive map with zoom and pan controls**
- **Map previews in measurement history**
- **Support for hybrid, satellite, and standard map types**
- **Auto-fit map to show all coordinates**
- **User location tracking with blue dot**
- Track driver movement (planned)
- Color-coded markers (implemented)

## API Integration

The app connects to the backend API. Make sure the backend server is running and accessible.

Default API URL: `http://localhost:3000/api/v1`

## Building for Production

### iOS

```bash
expo build:ios
```

### Android

```bash
expo build:android
```

## Testing

```bash
npm run lint
npm run type-check
```

## Permissions

The app requires the following permissions:

- **Location**: For GPS measurement and tracking
- **Storage**: For saving PDFs and images
- **Camera**: For capturing receipts (optional)

## Troubleshooting

### Cannot connect to API

- Check if backend server is running
- Verify API URL in environment config
- For Android emulator, use `10.0.2.2` instead of `localhost`

### GPS not working

- Enable location services on device
- Grant location permissions to app
- Ensure you're testing on a physical device (simulator GPS is limited)

### Map not displaying

- Check internet connection (map tiles need to download)
- Verify location permissions are granted
- For production builds, ensure Google Maps API keys are configured
- See MAP_IMPLEMENTATION.md for detailed troubleshooting

## Documentation

- **MAP_IMPLEMENTATION.md** - Comprehensive guide to map features and setup
- **Main README** - Project overview (../README.md)
- **API Reference** - Backend API documentation (../API_REFERENCE.md)

## License

Proprietary
