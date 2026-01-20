import React, { useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { useAuthStore } from '../store/authStore';
import { useSyncStore } from '../store/syncStore';
import { usePrinterStore } from '../store/printerStore';
import { AuthNavigator } from './AuthNavigator';
import { MainNavigator } from './MainNavigator';
import { LoadingSpinner } from '../shared/components/LoadingSpinner';
import { sqliteService } from '../shared/services/storage/sqlite.service';

export const AppNavigator: React.FC = () => {
  const { isAuthenticated, isLoading, loadStoredAuth } = useAuthStore();
  const { initSync } = useSyncStore();
  const { initialize: initPrinter } = usePrinterStore();

  useEffect(() => {
    initApp();
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      initSync();
      initPrinter();
    }
  }, [isAuthenticated]);

  const initApp = async () => {
    await sqliteService.init();
    await loadStoredAuth();
  };

  if (isLoading) {
    return <LoadingSpinner fullScreen />;
  }

  return (
    <NavigationContainer>
      {isAuthenticated ? <MainNavigator /> : <AuthNavigator />}
    </NavigationContainer>
  );
};
