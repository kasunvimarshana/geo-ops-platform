import React, { useEffect } from 'react';
import { StatusBar } from 'react-native';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { GestureHandlerRootView } from 'react-native-gesture-handler';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { I18nextProvider } from 'react-i18next';
import * as SplashScreen from 'expo-splash-screen';
import i18n from './src/i18n';
import { AppNavigator } from './src/navigation';
import { useAuthStore } from './src/stores/authStore';
import { initDatabase } from './src/services/storage/database';

// Keep splash screen visible while we fetch resources
SplashScreen.preventAutoHideAsync();

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 2,
      staleTime: 1000 * 60 * 5, // 5 minutes
      cacheTime: 1000 * 60 * 10, // 10 minutes
    },
  },
});

export default function App() {
  const [isReady, setIsReady] = React.useState(false);
  const loadUser = useAuthStore((state) => state.loadUser);

  useEffect(() => {
    async function prepare() {
      try {
        // Initialize SQLite database
        await initDatabase();
        
        // Load user from secure storage
        await loadUser();
        
        // Pre-load any other resources here
        await new Promise(resolve => setTimeout(resolve, 500));
      } catch (error) {
        console.error('Error during app initialization:', error);
      } finally {
        setIsReady(true);
        await SplashScreen.hideAsync();
      }
    }

    prepare();
  }, []);

  if (!isReady) {
    return null;
  }

  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <SafeAreaProvider>
        <QueryClientProvider client={queryClient}>
          <I18nextProvider i18n={i18n}>
            <StatusBar barStyle="light-content" />
            <AppNavigator />
          </I18nextProvider>
        </QueryClientProvider>
      </SafeAreaProvider>
    </GestureHandlerRootView>
  );
}
