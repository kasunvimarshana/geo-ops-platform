import React, { useEffect } from 'react';
import { StatusBar } from 'expo-status-bar';
import { ActivityIndicator, View, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import './src/config/i18n';
import { useAuthStore } from './src/presentation/stores/authStore';
import ErrorBoundary from './src/components/ErrorBoundary';
import LoginScreen from './src/presentation/screens/Auth/LoginScreen';
import RegisterScreen from './src/presentation/screens/Auth/RegisterScreen';
import HomeScreen from './src/presentation/screens/Home/HomeScreen';
import FieldsListScreen from './src/presentation/screens/Fields/FieldsListScreen';
import FieldDetailScreen from './src/presentation/screens/Fields/FieldDetailScreen';
import CreateFieldScreen from './src/presentation/screens/Fields/CreateFieldScreen';
import GPSMeasurementScreen from './src/presentation/screens/GPS/GPSMeasurementScreen';
import WalkAroundMeasurementScreen from './src/presentation/screens/GPS/WalkAroundMeasurementScreen';
import JobsListScreen from './src/presentation/screens/Jobs/JobsListScreen';
import JobDetailScreen from './src/presentation/screens/Jobs/JobDetailScreen';
import CreateJobScreen from './src/presentation/screens/Jobs/CreateJobScreen';
import SettingsScreen from './src/presentation/screens/Settings/SettingsScreen';

const Stack = createNativeStackNavigator();

export default function App() {
  const { isAuthenticated, isLoading, checkAuth } = useAuthStore();

  useEffect(() => {
    // Check if user is already authenticated
    checkAuth();
  }, []);

  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#27ae60" />
      </View>
    );
  }

  return (
    <ErrorBoundary>
      <NavigationContainer>
        <StatusBar style="auto" />
        <Stack.Navigator screenOptions={{ headerShown: false }}>
          {!isAuthenticated ? (
            <>
              <Stack.Screen name="Login" component={LoginScreen} />
              <Stack.Screen name="Register" component={RegisterScreen} />
            </>
          ) : (
            <>
              <Stack.Screen name="Home" component={HomeScreen} />
              <Stack.Screen name="Fields" component={FieldsListScreen} />
              <Stack.Screen name="FieldDetail" component={FieldDetailScreen} />
              <Stack.Screen name="CreateField" component={CreateFieldScreen} />
              <Stack.Screen name="GPSMeasurement" component={GPSMeasurementScreen} />
              <Stack.Screen name="WalkAroundMeasurement" component={WalkAroundMeasurementScreen} />
              <Stack.Screen name="Jobs" component={JobsListScreen} />
              <Stack.Screen name="JobDetail" component={JobDetailScreen} />
              <Stack.Screen name="CreateJob" component={CreateJobScreen} />
              <Stack.Screen name="Settings" component={SettingsScreen} />
            </>
          )}
        </Stack.Navigator>
      </NavigationContainer>
    </ErrorBoundary>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f5f5f5',
  },
});

