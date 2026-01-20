import { View, Text, StyleSheet, TouchableOpacity, Alert } from 'react-native';
import { useState, useEffect, useMemo } from 'react';
import { useRouter } from 'expo-router';
import * as Location from 'expo-location';
import { COLORS, MEASUREMENT_UNITS, MAP_CONSTANTS } from '../../src/constants';
import { GpsCoordinate } from '../../src/types';
import { landMeasurementService } from '../../src/services/land-measurement.service';
import { useMeasurementStore } from '../../src/store/measurement.store';
import { MeasurementMap } from '../../src/components';

export default function MeasureTab() {
  const router = useRouter();
  const { addMeasurement } = useMeasurementStore();
  const [isRecording, setIsRecording] = useState(false);
  const [coordinates, setCoordinates] = useState<GpsCoordinate[]>([]);
  const [locationSubscription, setLocationSubscription] = useState<Location.LocationSubscription | null>(null);
  const [distance, setDistance] = useState(0);
  const [hasPermission, setHasPermission] = useState(false);

  // Memoize to prevent unnecessary recalculations on each render
  const shouldShowInstructions = useMemo(
    () => !isRecording && coordinates.length === 0,
    [isRecording, coordinates.length]
  );

  useEffect(() => {
    requestLocationPermission();
  }, []);

  useEffect(() => {
    // Cleanup on unmount
    return () => {
      if (locationSubscription) {
        locationSubscription.remove();
      }
    };
  }, [locationSubscription]);

  const requestLocationPermission = async () => {
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      setHasPermission(status === 'granted');
      
      if (status !== 'granted') {
        Alert.alert(
          'Permission Required',
          'Location permission is required to measure land areas. Please enable it in your device settings.',
          [{ text: 'OK' }]
        );
      }
    } catch (error) {
      console.error('Error requesting location permission:', error);
    }
  };

  const calculateDistance = (coords: GpsCoordinate[]): number => {
    if (coords.length < 2) return 0;
    
    let totalDistance = 0;
    for (let i = 1; i < coords.length; i++) {
      const lat1 = coords[i - 1].latitude;
      const lon1 = coords[i - 1].longitude;
      const lat2 = coords[i].latitude;
      const lon2 = coords[i].longitude;
      
      // Haversine formula for calculating distance between two GPS points
      const R = 6371e3; // Earth's radius in meters
      const φ1 = lat1 * Math.PI / 180;
      const φ2 = lat2 * Math.PI / 180;
      const Δφ = (lat2 - lat1) * Math.PI / 180;
      const Δλ = (lon2 - lon1) * Math.PI / 180;
      
      const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      
      totalDistance += R * c;
    }
    
    return totalDistance;
  };

  const startGPSTracking = async () => {
    try {
      const subscription = await Location.watchPositionAsync(
        {
          accuracy: Location.Accuracy.BestForNavigation,
          timeInterval: 2000, // Update every 2 seconds
          distanceInterval: 5, // Update when moved 5 meters
        },
        (location) => {
          const newCoordinate: GpsCoordinate = {
            latitude: location.coords.latitude,
            longitude: location.coords.longitude,
            timestamp: new Date(location.timestamp),
          };
          
          setCoordinates((prev) => {
            const updated = [...prev, newCoordinate];
            setDistance(calculateDistance(updated));
            return updated;
          });
        }
      );
      
      setLocationSubscription(subscription);
    } catch (error) {
      console.error('Error starting GPS tracking:', error);
      Alert.alert('Error', 'Failed to start GPS tracking. Please try again.');
      setIsRecording(false);
    }
  };

  const stopGPSTracking = () => {
    if (locationSubscription) {
      locationSubscription.remove();
      setLocationSubscription(null);
    }
  };

  const handleStartMeasurement = () => {
    if (!hasPermission) {
      Alert.alert(
        'Permission Required',
        'Location permission is required to measure land areas.',
        [
          { text: 'Cancel', style: 'cancel' },
          { text: 'Open Settings', onPress: requestLocationPermission }
        ]
      );
      return;
    }

    Alert.alert(
      'Start Measurement',
      'Walk around the perimeter of the land to measure. Your GPS coordinates will be recorded.',
      [
        { text: 'Cancel', style: 'cancel' },
        { 
          text: 'Start',
          onPress: () => {
            setIsRecording(true);
            setCoordinates([]);
            setDistance(0);
            startGPSTracking();
          }
        },
      ]
    );
  };

  const handleStopMeasurement = () => {
    if (coordinates.length < MAP_CONSTANTS.MIN_POLYGON_POINTS) {
      Alert.alert(
        'Insufficient Points',
        `You need at least ${MAP_CONSTANTS.MIN_POLYGON_POINTS} points to create a valid measurement. Continue walking around the perimeter.`,
        [{ text: 'OK' }]
      );
      return;
    }

    Alert.alert(
      'Stop Measurement',
      `You have recorded ${coordinates.length} points covering ${distance.toFixed(2)}m. Save this measurement?`,
      [
        { text: 'Continue Recording', style: 'cancel' },
        { 
          text: 'Save',
          onPress: () => {
            stopGPSTracking();
            setIsRecording(false);
            promptSaveMeasurement();
          }
        },
      ]
    );
  };

  const promptSaveMeasurement = () => {
    Alert.prompt(
      'Save Measurement',
      'Enter a name for this measurement:',
      [
        { text: 'Cancel', style: 'cancel', onPress: () => {
          // Reset state on cancel
          setCoordinates([]);
          setDistance(0);
        }},
        {
          text: 'Save',
          onPress: async (name) => {
            if (!name || name.trim() === '') {
              Alert.alert('Error', 'Please enter a valid name');
              promptSaveMeasurement(); // Re-prompt
              return;
            }
            await saveMeasurement(name.trim());
          }
        }
      ],
      'plain-text',
      '',
      'default'
    );
  };

  const saveMeasurement = async (name: string) => {
    try {
      const measurement = await landMeasurementService.create({
        name,
        coordinates,
        unit: MEASUREMENT_UNITS.SQUARE_METERS,
        description: `Measured on ${new Date().toLocaleDateString()} with ${coordinates.length} GPS points`,
        metadata: {
          distance: distance,
          pointCount: coordinates.length,
          measuredAt: new Date().toISOString(),
        }
      });

      // Add to store
      addMeasurement(measurement);

      // Reset state
      setCoordinates([]);
      setDistance(0);

      // Show success message
      Alert.alert(
        'Success',
        `Measurement "${name}" saved successfully!\nArea: ${measurement.area.toFixed(2)} ${measurement.unit}`,
        [
          {
            text: 'View History',
            onPress: () => router.push('/(tabs)/history')
          },
          { text: 'OK' }
        ]
      );
    } catch (error) {
      console.error('Failed to save measurement:', error);
      
      let errorMessage = 'Failed to save measurement. Please try again.';
      if (error && typeof error === 'object' && 'response' in error) {
        const axiosError = error as { response?: { data?: { message?: string } } };
        errorMessage = axiosError.response?.data?.message || errorMessage;
      }
      
      Alert.alert(
        'Error',
        errorMessage,
        [
          { text: 'Cancel', style: 'cancel' },
          { text: 'Retry', onPress: () => promptSaveMeasurement() }
        ]
      );
    }
  };

  return (
    <View style={styles.container}>
      {/* Map View - takes up most of the screen */}
      <View style={styles.mapContainer}>
        <MeasurementMap
          coordinates={coordinates}
          isRecording={isRecording}
          showPolygon={!isRecording && coordinates.length >= MAP_CONSTANTS.MIN_POLYGON_POINTS}
        />
      </View>

      {/* Overlay with controls and stats */}
      <View style={styles.overlay}>
        <View style={styles.header}>
          <Text style={styles.title}>Land Measurement</Text>
          <Text style={styles.subtitle}>
            {isRecording 
              ? 'Walk around the perimeter'
              : 'Tap start to begin measuring'}
          </Text>
        </View>

        {isRecording && (
          <View style={styles.stats}>
            <View style={styles.statItem}>
              <Text style={styles.statLabel}>Points</Text>
              <Text style={styles.statValue}>{coordinates.length}</Text>
            </View>
            <View style={styles.statItem}>
              <Text style={styles.statLabel}>Distance</Text>
              <Text style={styles.statValue}>{distance.toFixed(0)}m</Text>
            </View>
          </View>
        )}

        <View style={styles.buttonContainer}>
          <TouchableOpacity
            style={[
              styles.button,
              isRecording && styles.buttonStop,
            ]}
            onPress={isRecording ? handleStopMeasurement : handleStartMeasurement}
          >
            <Text style={styles.buttonText}>
              {isRecording ? 'Stop Recording' : 'Start Measurement'}
            </Text>
          </TouchableOpacity>
        </View>

        {shouldShowInstructions && (
          <View style={styles.infoBox}>
            <Text style={styles.infoTitle}>How to measure:</Text>
            <Text style={styles.infoText}>1. Walk to the starting point</Text>
            <Text style={styles.infoText}>2. Tap "Start Measurement"</Text>
            <Text style={styles.infoText}>3. Walk around the perimeter</Text>
            <Text style={styles.infoText}>4. Return to starting point</Text>
            <Text style={styles.infoText}>5. Tap "Stop Recording"</Text>
          </View>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  mapContainer: {
    flex: 1,
  },
  overlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    pointerEvents: 'box-none',
  },
  header: {
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    padding: 20,
    paddingTop: 60,
    alignItems: 'center',
    borderBottomLeftRadius: 20,
    borderBottomRightRadius: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.text,
    marginBottom: 5,
  },
  subtitle: {
    fontSize: 14,
    color: COLORS.textSecondary,
    textAlign: 'center',
  },
  stats: {
    position: 'absolute',
    top: 180,
    left: 20,
    right: 20,
    flexDirection: 'row',
    justifyContent: 'space-around',
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    padding: 15,
    borderRadius: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  statItem: {
    alignItems: 'center',
  },
  statLabel: {
    fontSize: 12,
    color: COLORS.textSecondary,
    marginBottom: 5,
  },
  statValue: {
    fontSize: 28,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  buttonContainer: {
    position: 'absolute',
    bottom: 40,
    left: 0,
    right: 0,
    alignItems: 'center',
  },
  button: {
    backgroundColor: COLORS.primary,
    paddingVertical: 18,
    paddingHorizontal: 50,
    borderRadius: 30,
    elevation: 5,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
  },
  buttonStop: {
    backgroundColor: COLORS.error,
  },
  buttonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  infoBox: {
    position: 'absolute',
    bottom: 120,
    left: 20,
    right: 20,
    padding: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    borderRadius: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  infoTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.text,
    marginBottom: 10,
  },
  infoText: {
    fontSize: 13,
    color: COLORS.textSecondary,
    marginBottom: 5,
  },
});
