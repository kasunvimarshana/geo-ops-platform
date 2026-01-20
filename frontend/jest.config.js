module.exports = {
  preset: 'jest-expo',
  testEnvironment: 'node',
  testPathIgnorePatterns: ['/node_modules/', '/.expo/'],
  moduleNameMapper: {
    '^@/(.*)$': '<rootDir>/src/$1',
    '^@components/(.*)$': '<rootDir>/src/components/$1',
    '^@features/(.*)$': '<rootDir>/src/features/$1',
    '^@services/(.*)$': '<rootDir>/src/services/$1',
    '^@store/(.*)$': '<rootDir>/src/store/$1',
    '^@hooks/(.*)$': '<rootDir>/src/hooks/$1',
    '^@utils/(.*)$': '<rootDir>/src/utils/$1',
    '^@locales/(.*)$': '<rootDir>/src/locales/$1',
    '^@types/(.*)$': '<rootDir>/src/types/$1',
  },
  transformIgnorePatterns: [
    'node_modules/(?!(expo|expo-router|expo-constants|expo-modules-autolinking|@react-native-community|react-native-gesture-handler|react-native-reanimated|react-native-screens|react-native-safe-area-context|react-native-svg|@react-navigation|@react-native-async-storage)/)',
  ],
  setupFilesAfterEnv: ['<rootDir>/jest.setup.js'],
};
