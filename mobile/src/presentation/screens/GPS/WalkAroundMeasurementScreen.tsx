import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  Alert,
  ActivityIndicator,
  ScrollView,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import * as Location from 'expo-location';

// Constants for GPS calculations
const EARTH_RADIUS_M = 6371000; // Earth radius in meters
const DEGREES_TO_METERS = 111320; // Approximate conversion at equator

interface GeoPoint {
  latitude: number;
  longitude: number;
  accuracy?: number;
  timestamp?: number;
}

export default function WalkAroundMeasurementScreen({ navigation }: any) {
  const { t } = useTranslation();
  const [isTracking, setIsTracking] = useState(false);
  const [points, setPoints] = useState<GeoPoint[]>([]);
  const [currentLocation, setCurrentLocation] = useState<GeoPoint | null>(null);
  const [distance, setDistance] = useState(0);
  const [area, setArea] = useState(0);

  useEffect(() => {
    requestLocationPermission();
  }, []);

  useEffect(() => {
    let locationSubscription: Location.LocationSubscription | null = null;

    if (isTracking) {
      startTracking();
    } else {
      if (locationSubscription) {
        locationSubscription.remove();
      }
    }

    async function startTracking() {
      locationSubscription = await Location.watchPositionAsync(
        {
          accuracy: Location.Accuracy.BestForNavigation,
          timeInterval: 2000,
          distanceInterval: 5,
        },
        (location) => {
          const point: GeoPoint = {
            latitude: location.coords.latitude,
            longitude: location.coords.longitude,
            accuracy: location.coords.accuracy || undefined,
            timestamp: location.timestamp,
          };
          
          setCurrentLocation(point);
          
          if (points.length === 0 || calculateDistance(points[points.length - 1], point) > 2) {
            setPoints((prev) => [...prev, point]);
          }
        }
      );
    }

    return () => {
      if (locationSubscription) {
        locationSubscription.remove();
      }
    };
  }, [isTracking]);

  useEffect(() => {
    if (points.length > 0) {
      calculateMetrics();
    }
  }, [points]);

  const requestLocationPermission = async () => {
    const { status } = await Location.requestForegroundPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permission Denied', 'Location permission is required for GPS measurement');
      navigation.goBack();
    }
  };

  const calculateDistance = (point1: GeoPoint, point2: GeoPoint): number => {
    const dLat = toRad(point2.latitude - point1.latitude);
    const dLon = toRad(point2.longitude - point1.longitude);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(toRad(point1.latitude)) *
        Math.cos(toRad(point2.latitude)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return EARTH_RADIUS_M * c;
  };

  const toRad = (deg: number): number => {
    return deg * (Math.PI / 180);
  };

  const calculateMetrics = () => {
    if (points.length < 2) {
      setDistance(0);
      setArea(0);
      return;
    }

    // Calculate perimeter
    let totalDistance = 0;
    for (let i = 0; i < points.length - 1; i++) {
      totalDistance += calculateDistance(points[i], points[i + 1]);
    }
    setDistance(totalDistance);

    // Calculate area using Shoelace formula
    if (points.length >= 3) {
      let areaSum = 0;
      for (let i = 0; i < points.length; i++) {
        const j = (i + 1) % points.length;
        areaSum +=
          points[i].longitude * points[j].latitude -
          points[j].longitude * points[i].latitude;
      }
      const calculatedArea = Math.abs(areaSum / 2) * (DEGREES_TO_METERS * DEGREES_TO_METERS);
      setArea(calculatedArea);
    }
  };

  const toggleTracking = () => {
    if (!isTracking) {
      setPoints([]);
      setDistance(0);
      setArea(0);
    }
    setIsTracking(!isTracking);
  };

  const finishMeasurement = () => {
    if (points.length < 3) {
      Alert.alert('Insufficient Points', 'You need at least 3 points to create a field');
      return;
    }

    // Prepare GeoJSON boundary
    const coordinates = points.map(p => [p.longitude, p.latitude]);
    // Close the polygon by adding the first point at the end
    coordinates.push(coordinates[0]);
    
    const boundary = {
      type: 'Polygon',
      coordinates: [coordinates],
    };

    Alert.alert(
      'Save Field',
      `Area: ${(area / 10000).toFixed(2)} ha\nPerimeter: ${(distance / 1000).toFixed(2)} km\n\nSave this measurement?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Save',
          onPress: () => {
            // Navigate to CreateField screen with measurements
            navigation.navigate('CreateField', {
              measurement: {
                boundary,
                area,
                perimeter: distance,
                measurement_type: 'walk_around',
              },
            });
          },
        },
      ]
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity
          style={styles.backButton}
          onPress={() => {
            if (isTracking) {
              Alert.alert('Stop Tracking', 'Stop tracking before going back');
            } else {
              navigation.goBack();
            }
          }}
        >
          <Text style={styles.backButtonText}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Walk Around</Text>
        <View style={{ width: 50 }} />
      </View>

      <ScrollView style={styles.content}>
        <View style={styles.statsContainer}>
          <View style={styles.statBox}>
            <Text style={styles.statLabel}>Points</Text>
            <Text style={styles.statValue}>{points.length}</Text>
          </View>
          <View style={styles.statBox}>
            <Text style={styles.statLabel}>Distance</Text>
            <Text style={styles.statValue}>{(distance / 1000).toFixed(2)} km</Text>
          </View>
          <View style={styles.statBox}>
            <Text style={styles.statLabel}>Area</Text>
            <Text style={styles.statValue}>{(area / 10000).toFixed(2)} ha</Text>
          </View>
        </View>

        {currentLocation && (
          <View style={styles.locationInfo}>
            <Text style={styles.locationTitle}>Current Location</Text>
            <Text style={styles.locationText}>
              Lat: {currentLocation.latitude.toFixed(6)}
            </Text>
            <Text style={styles.locationText}>
              Lon: {currentLocation.longitude.toFixed(6)}
            </Text>
            {currentLocation.accuracy && (
              <Text style={styles.locationText}>
                Accuracy: ±{currentLocation.accuracy.toFixed(1)}m
              </Text>
            )}
          </View>
        )}

        <View style={styles.instructions}>
          <Text style={styles.instructionsTitle}>Instructions:</Text>
          <Text style={styles.instructionsText}>1. Tap "Start Tracking" button</Text>
          <Text style={styles.instructionsText}>2. Walk around the field perimeter</Text>
          <Text style={styles.instructionsText}>3. Keep your device visible to the sky</Text>
          <Text style={styles.instructionsText}>4. Tap "Stop Tracking" when complete</Text>
          <Text style={styles.instructionsText}>5. Review and save your measurement</Text>
        </View>
      </ScrollView>

      <View style={styles.footer}>
        <TouchableOpacity
          style={[
            styles.trackButton,
            isTracking ? styles.trackButtonStop : styles.trackButtonStart,
          ]}
          onPress={toggleTracking}
        >
          <Text style={styles.trackButtonText}>
            {isTracking ? '⏸ Stop Tracking' : '▶ Start Tracking'}
          </Text>
        </TouchableOpacity>

        {!isTracking && points.length >= 3 && (
          <TouchableOpacity
            style={styles.finishButton}
            onPress={finishMeasurement}
          >
            <Text style={styles.finishButtonText}>Finish & Save</Text>
          </TouchableOpacity>
        )}
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#fff',
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  backButton: {
    padding: 5,
  },
  backButtonText: {
    fontSize: 16,
    color: '#3498db',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  content: {
    flex: 1,
    padding: 15,
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 20,
  },
  statBox: {
    flex: 1,
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 12,
    alignItems: 'center',
    marginHorizontal: 5,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  statLabel: {
    fontSize: 12,
    color: '#95a5a6',
    textTransform: 'uppercase',
    marginBottom: 5,
  },
  statValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#27ae60',
  },
  locationInfo: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 12,
    marginBottom: 20,
  },
  locationTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 10,
  },
  locationText: {
    fontSize: 14,
    color: '#7f8c8d',
    marginBottom: 3,
  },
  instructions: {
    backgroundColor: '#e8f5e9',
    padding: 20,
    borderRadius: 12,
    marginBottom: 20,
  },
  instructionsTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#27ae60',
    marginBottom: 10,
  },
  instructionsText: {
    fontSize: 14,
    color: '#2c3e50',
    marginBottom: 5,
  },
  footer: {
    padding: 15,
    backgroundColor: '#fff',
    borderTopWidth: 1,
    borderTopColor: '#e0e0e0',
  },
  trackButton: {
    padding: 18,
    borderRadius: 12,
    alignItems: 'center',
    marginBottom: 10,
  },
  trackButtonStart: {
    backgroundColor: '#27ae60',
  },
  trackButtonStop: {
    backgroundColor: '#e74c3c',
  },
  trackButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  finishButton: {
    backgroundColor: '#3498db',
    padding: 18,
    borderRadius: 12,
    alignItems: 'center',
  },
  finishButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
});
