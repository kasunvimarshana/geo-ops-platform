import { useEffect, useState } from 'react';
import * as Location from 'expo-location';
import { Location as LocationType } from '@types/index';

interface UseLocationState {
  location: LocationType | null;
  isLoading: boolean;
  error: string | null;
}

interface UseLocationOptions {
  watch?: boolean;
  accuracy?: Location.Accuracy;
}

export function useLocation(options?: UseLocationOptions) {
  const [state, setState] = useState<UseLocationState>({
    location: null,
    isLoading: true,
    error: null,
  });

  useEffect(() => {
    let unsubscribe: (() => void) | null = null;

    const startLocationTracking = async () => {
      try {
        // Request permission
        const { status } = await Location.requestForegroundPermissionsAsync();
        if (status !== 'granted') {
          setState((prev) => ({
            ...prev,
            error: 'Location permission denied',
            isLoading: false,
          }));
          return;
        }

        if (options?.watch) {
          // Watch location
          unsubscribe = await Location.watchPositionAsync(
            {
              accuracy: options.accuracy || Location.Accuracy.Highest,
              timeInterval: 1000,
              distanceInterval: 1,
            },
            (location) => {
              setState({
                location: {
                  latitude: location.coords.latitude,
                  longitude: location.coords.longitude,
                  accuracy: location.coords.accuracy || undefined,
                },
                isLoading: false,
                error: null,
              });
            }
          );
        } else {
          // Get single location
          const location = await Location.getCurrentPositionAsync({
            accuracy: options?.accuracy || Location.Accuracy.Highest,
          });
          setState({
            location: {
              latitude: location.coords.latitude,
              longitude: location.coords.longitude,
              accuracy: location.coords.accuracy || undefined,
            },
            isLoading: false,
            error: null,
          });
        }
      } catch (error) {
        setState((prev) => ({
          ...prev,
          error: error instanceof Error ? error.message : 'Unknown error',
          isLoading: false,
        }));
      }
    };

    startLocationTracking();

    return () => {
      if (unsubscribe) {
        unsubscribe();
      }
    };
  }, [options?.watch, options?.accuracy]);

  return state;
}
