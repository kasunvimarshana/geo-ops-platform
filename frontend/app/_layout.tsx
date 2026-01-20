import React, { useEffect } from 'react';
import { Stack, useRouter, useSegments } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useAuthStore } from '../src/store/authStore';
import { initDatabase } from '../src/database/database';
import { startBackgroundSync, stopBackgroundSync } from '../src/services/syncService';

export default function RootLayout() {
  const { isAuthenticated, loadUser } = useAuthStore();
  const segments = useSegments();
  const router = useRouter();

  // Initialize database and load user on app start
  useEffect(() => {
    const initialize = async () => {
      try {
        // Initialize SQLite database
        await initDatabase();
        console.log('Database initialized');

        // Load user from storage
        await loadUser();

        // Start background sync if authenticated
        if (isAuthenticated) {
          startBackgroundSync();
        }
      } catch (error) {
        console.error('Initialization error:', error);
      }
    };

    initialize();

    // Cleanup on unmount
    return () => {
      stopBackgroundSync();
    };
  }, []);

  // Start/stop sync based on authentication
  useEffect(() => {
    if (isAuthenticated) {
      startBackgroundSync();
    } else {
      stopBackgroundSync();
    }
  }, [isAuthenticated]);

  // Redirect based on authentication status
  useEffect(() => {
    const inAuthGroup = segments[0] === '(auth)';
    const inTabsGroup = segments[0] === '(tabs)';

    if (!isAuthenticated && inTabsGroup) {
      // User is not authenticated but trying to access protected routes
      router.replace('/(auth)/login');
    } else if (isAuthenticated && inAuthGroup) {
      // User is authenticated but on auth screen
      router.replace('/(tabs)');
    }
  }, [isAuthenticated, segments]);

  return (
    <>
      <StatusBar barStyle="dark-content" />
      <Stack
        screenOptions={{
          headerShown: false,
        }}
      >
        <Stack.Screen name="index" options={{ title: 'Home' }} />
        <Stack.Screen name="(auth)" options={{ headerShown: false }} />
        <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
        <Stack.Screen name="+not-found" options={{ title: 'Not Found' }} />
      </Stack>
    </>
  );
}
