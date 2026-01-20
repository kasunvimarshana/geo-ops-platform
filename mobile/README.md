# Mobile Application for GeoOps Platform

Welcome to the GeoOps Platform mobile application! This application is designed to provide farmers and agricultural service providers with a comprehensive tool for managing field services, land measurements, and job tracking.

## Overview

The mobile app is built using TypeScript and Expo, ensuring a smooth and efficient user experience. It integrates seamlessly with the Laravel backend API to provide real-time data and functionalities.

## Key Features

- **User Authentication**: Secure login and registration for users.
- **Job Management**: Create, track, and manage jobs with real-time updates.
- **Land Measurement**: Measure land using GPS and manage land records.
- **Offline Capabilities**: Functionality available even without internet access.
- **Bilingual Support**: Available in Sinhala and English.

## Project Structure

The mobile application is organized into several key directories:

- **app/**: Contains the main application components, including authentication and tab navigation.
- **assets/**: Stores images, fonts, and localization files.
- **src/**: Contains the core logic, including API calls, components, services, and state management.
- **hooks/**: Custom hooks for managing state and side effects.
- **navigation/**: Defines the navigation structure of the app.
- **store/**: Zustand stores for managing global state.

## Getting Started

To get started with the mobile application, follow these steps:

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/kasunvimarshana/geo-ops-platform.git
   cd geo-ops-platform/mobile
   ```

2. **Install Dependencies**:

   ```bash
   npm install
   ```

3. **Run the Application**:

   ```bash
   npm start
   ```

4. **Build for Production**:
   - For Android:
     ```bash
     eas build --platform android --profile production
     ```
   - For iOS:
     ```bash
     eas build --platform ios --profile production
     ```

## Documentation

For detailed documentation, refer to the following files:

- [API Specification](../../API_SPECIFICATION.md)
- [Setup Guide](../../SETUP_GUIDE.md)
- [Deployment Guide](../../DEPLOYMENT_GUIDE.md)

## Contributing

We welcome contributions! Please refer to the [CONTRIBUTING.md](../../CONTRIBUTING.md) file for guidelines on how to contribute to the project.

## License

This project is licensed under the MIT License. See the [LICENSE](../../LICENSE) file for details.

## Support

For support, please contact us at support@geo-ops.lk or open an issue in the GitHub repository.

---

Thank you for using the GeoOps Platform mobile application! We hope it helps you manage your agricultural services efficiently.
