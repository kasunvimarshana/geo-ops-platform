import { useEffect } from 'react';
import { useRouter } from 'expo-router';
import { View, ActivityIndicator, StyleSheet } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { authService } from '../src/services/auth.service';
import { useAuthStore } from '../src/store/auth.store';
import { STORAGE_KEYS } from '../src/constants';

export default function Index() {
  const router = useRouter();
  const setAuth = useAuthStore((state) => state.setAuth);

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    try {
      const isAuthenticated = await authService.isAuthenticated();
      
      if (isAuthenticated) {
        const user = await authService.getCurrentUser();
        const token = await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
        if (user && token) {
          setAuth(user, token);
          router.replace('/(tabs)');
        } else {
          router.replace('/auth/login');
        }
      } else {
        router.replace('/auth/login');
      }
    } catch (error) {
      console.error('Auth check failed:', error);
      router.replace('/auth/login');
    }
  };

  return (
    <View style={styles.container}>
      <ActivityIndicator size="large" color="#2196F3" />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#fff',
  },
});
