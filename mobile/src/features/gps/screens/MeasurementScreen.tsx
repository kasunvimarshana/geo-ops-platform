import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Alert,
  Dimensions,
} from 'react-native';
import MapView, { Marker, Polygon } from 'react-native-maps';
import { useTranslation } from 'react-i18next';
import { usePlotsStore } from '../../../store/plotsStore';
import { locationService } from '../../../shared/services/location/locationService';
import { Button } from '../../../shared/components/Button';
import { Card } from '../../../shared/components/Card';
import { colors } from '../../../theme/colors';
import { typography } from '../../../theme/typography';
import { spacing } from '../../../theme/spacing';
import { Coordinates } from '../../../shared/types/api.types';

const { width, height } = Dimensions.get('window');

interface MeasurementScreenProps {
  route?: any;
}

export const MeasurementScreen: React.FC<MeasurementScreenProps> = ({ route }) => {
  const { t } = useTranslation();
  const {
    currentMeasurement,
    isTracking,
    startMeasurement,
    addPoint,
    removeLastPoint,
    clearMeasurement,
    saveMeasurement,
  } = usePlotsStore();

  const [currentLocation, setCurrentLocation] = useState<Coordinates | null>(null);
  const [mapRegion, setMapRegion] = useState({
    latitude: 7.8731,
    longitude: 80.7718,
    latitudeDelta: 0.01,
    longitudeDelta: 0.01,
  });

  const jobId = route?.params?.jobId;

  useEffect(() => {
    initLocation();
    return () => {
      locationService.stopWatching();
    };
  }, []);

  const initLocation = async () => {
    const location = await locationService.getCurrentLocation();
    if (location) {
      setCurrentLocation(location);
      setMapRegion({
        latitude: location.latitude,
        longitude: location.longitude,
        latitudeDelta: 0.01,
        longitudeDelta: 0.01,
      });
    } else {
      Alert.alert(t('common.error'), t('gps.locationPermissionRequired'));
    }
  };

  const handleStartTracking = () => {
    startMeasurement();
    locationService.startWatching(
      (location) => {
        setCurrentLocation(location);
        if (isTracking) {
          addPoint(location);
        }
      },
      (error) => {
        Alert.alert(t('common.error'), error.message);
      }
    );
  };

  const handleAddPoint = () => {
    if (currentLocation) {
      addPoint(currentLocation);
    }
  };

  const handleSave = async () => {
    if (currentMeasurement.length < 3) {
      Alert.alert(t('common.error'), t('gps.needMorePoints'));
      return;
    }

    try {
      await saveMeasurement(jobId);
      Alert.alert(t('common.success'), t('gps.savePlot'));
      clearMeasurement();
    } catch (error: any) {
      Alert.alert(t('common.error'), error.message);
    }
  };

  const calculateStats = () => {
    if (currentMeasurement.length < 3) return null;
    
    const area_sqm = locationService.calculateArea(currentMeasurement);
    const area_acres = locationService.sqmToAcres(area_sqm);
    const perimeter_m = locationService.calculatePerimeter(currentMeasurement);

    return {
      area_sqm: area_sqm.toFixed(2),
      area_acres: area_acres.toFixed(3),
      perimeter_m: perimeter_m.toFixed(2),
    };
  };

  const stats = calculateStats();

  return (
    <View style={styles.container}>
      <MapView
        style={styles.map}
        region={mapRegion}
        showsUserLocation
        showsMyLocationButton
        onRegionChangeComplete={setMapRegion}
      >
        {currentMeasurement.length > 0 && (
          <>
            {currentMeasurement.map((coord, index) => (
              <Marker
                key={index}
                coordinate={coord}
                pinColor={colors.primary}
              />
            ))}
            {currentMeasurement.length > 2 && (
              <Polygon
                coordinates={currentMeasurement}
                fillColor="rgba(46, 125, 50, 0.3)"
                strokeColor={colors.primary}
                strokeWidth={2}
              />
            )}
          </>
        )}
      </MapView>

      <View style={styles.overlay}>
        {stats && (
          <Card style={styles.statsCard}>
            <Text style={styles.statsLabel}>{t('gps.points')}: {currentMeasurement.length}</Text>
            <Text style={styles.statsValue}>
              {t('gps.area')}: {stats.area_sqm} {t('gps.sqm')} ({stats.area_acres} {t('gps.acres')})
            </Text>
            <Text style={styles.statsValue}>
              {t('gps.perimeter')}: {stats.perimeter_m} {t('gps.meters')}
            </Text>
          </Card>
        )}

        <View style={styles.controls}>
          {!isTracking ? (
            <Button
              title={t('gps.startMeasurement')}
              onPress={handleStartTracking}
              style={styles.controlButton}
            />
          ) : (
            <>
              <Button
                title={t('gps.addPoint')}
                onPress={handleAddPoint}
                style={styles.controlButton}
              />
              <Button
                title={t('gps.stopMeasurement')}
                onPress={() => locationService.stopWatching()}
                variant="secondary"
                style={styles.controlButton}
              />
            </>
          )}

          {currentMeasurement.length > 0 && (
            <View style={styles.actionRow}>
              <Button
                title={t('gps.removePoint')}
                onPress={removeLastPoint}
                variant="outline"
                style={styles.smallButton}
              />
              <Button
                title={t('gps.clearPoints')}
                onPress={clearMeasurement}
                variant="outline"
                style={styles.smallButton}
              />
            </View>
          )}

          {stats && (
            <Button
              title={t('gps.savePlot')}
              onPress={handleSave}
              style={styles.saveButton}
            />
          )}
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  map: {
    width,
    height,
  },
  overlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: spacing.md,
  },
  statsCard: {
    backgroundColor: colors.background,
    marginBottom: spacing.md,
  },
  statsLabel: {
    ...typography.body1,
    color: colors.text.primary,
    marginBottom: spacing.xs,
  },
  statsValue: {
    ...typography.body2,
    color: colors.text.secondary,
    marginBottom: spacing.xs,
  },
  controls: {
    gap: spacing.sm,
  },
  controlButton: {
    marginBottom: spacing.sm,
  },
  actionRow: {
    flexDirection: 'row',
    gap: spacing.sm,
    marginBottom: spacing.sm,
  },
  smallButton: {
    flex: 1,
  },
  saveButton: {
    backgroundColor: colors.success,
  },
});
