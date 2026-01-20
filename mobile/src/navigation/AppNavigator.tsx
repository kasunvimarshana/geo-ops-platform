import React, { useEffect } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { useAuthStore } from '@/stores/authStore';
import { syncService } from '@/services/sync';
import { AuthNavigator } from './AuthNavigator';
import { MainNavigator } from './MainNavigator';
import { Loading } from '@/components';

export const AppNavigator: React.FC = () => {
  const { isAuthenticated } = useAuthStore();
  const [isReady, setIsReady] = React.useState(false);

  useEffect(() => {
    const initialize = async () => {
      try {
        if (isAuthenticated) {
          await syncService.start();
        }
      } catch (error) {
        console.error('Initialization error:', error);
      } finally {
        setIsReady(true);
      }
    };

    initialize();

    return () => {
      syncService.stop();
    };
  }, [isAuthenticated]);

  if (!isReady) {
    return <Loading message="Initializing..." />;
  }

  return (
    <NavigationContainer>
      {isAuthenticated ? <MainNavigator /> : <AuthNavigator />}
    </NavigationContainer>
  );
};
