import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Alert,
  TouchableOpacity,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Button, Header, MapView, Modal, Input } from '@/components';
import { useMeasurementStore } from '@/stores/measurementStore';
import { gpsService } from '../../services/gps/gpsService';
import { GPSPoint, Land } from '@/types';
import { apiClient } from '@/services/api/client';

interface MeasurementScreenProps {
  navigation: any;
  route: {
    params?: {
      mode?: 'walk-around' | 'point-based';
    };
  };
}

export const MeasurementScreen: React.FC<MeasurementScreenProps> = ({
  navigation,
  route,
}) => {
  const { t } = useTranslation();
  const mode = route.params?.mode || 'walk-around';
  
  const {
    isTracking,
    currentPoints,
    startTracking,
    stopTracking,
    addPoint,
    clearPoints,
  } = useMeasurementStore();

  const [accuracy, setAccuracy] = useState<number>(0);
  const [area, setArea] = useState({ acres: 0, hectares: 0 });
  const [showSaveModal, setShowSaveModal] = useState(false);
  const [saving, setSaving] = useState(false);
  const [landData, setLandData] = useState({
    name: '',
    customerName: '',
    customerPhone: '',
    locationName: '',
    description: '',
  });

  useEffect(() => {
    checkPermissions();
    
    return () => {
      if (isTracking) {
        gpsService.stopMeasurementTracking();
        stopTracking();
      }
    };
  }, []);

  useEffect(() => {
    if (currentPoints.length >= 3) {
      const calculatedArea = gpsService.calculatePolygonArea(currentPoints);
      setArea(calculatedArea);
    }
  }, [currentPoints]);

  const checkPermissions = async () => {
    const hasPermission = await gpsService.requestPermissions();
    if (!hasPermission) {
      Alert.alert(
        t('common.error'),
        t('measurement.gps_permission_required'),
        [{ text: t('common.ok'), onPress: () => navigation.goBack() }]
      );
    }
  };

  const handleStartTracking = async () => {
    startTracking();
    
    const started = await gpsService.startMeasurementTracking((point: GPSPoint) => {
      setAccuracy(point.accuracy);
      addPoint(point);
    });

    if (!started) {
      stopTracking();
      Alert.alert(t('common.error'), t('measurement.tracking_start_failed'));
    }
  };

  const handleStopTracking = () => {
    gpsService.stopMeasurementTracking();
    stopTracking();
  };

  const handleAddPoint = async () => {
    const point = await gpsService.getCurrentLocation();
    if (point) {
      addPoint(point);
      setAccuracy(point.accuracy);
    } else {
      Alert.alert(t('common.error'), t('measurement.point_capture_failed'));
    }
  };

  const handleSave = async () => {
    if (currentPoints.length < 3) {
      Alert.alert(t('common.error'), t('measurement.min_points_required'));
      return;
    }

    setShowSaveModal(true);
  };

  const handleConfirmSave = async () => {
    try {
      setSaving(true);

      const landPayload: Partial<Land> = {
        name: landData.name,
        description: landData.description,
        polygon: currentPoints,
        area_acres: area.acres,
        area_hectares: area.hectares,
        measurement_type: mode,
        location_name: landData.locationName,
        customer_name: landData.customerName,
        customer_phone: landData.customerPhone,
        measured_at: new Date().toISOString(),
        status: 'confirmed',
        sync_status: 'pending',
        offline_id: `land_${Date.now()}`,
      };

      const response = await apiClient.post('/lands', landPayload);

      if (response.success) {
        clearPoints();
        setShowSaveModal(false);
        Alert.alert(
          t('common.success'),
          t('measurement.measurement_saved'),
          [{ text: t('common.ok'), onPress: () => navigation.goBack() }]
        );
      }
    } catch (error: any) {
      Alert.alert(t('common.error'), error.message);
    } finally {
      setSaving(false);
    }
  };

  return (
    <View style={styles.container}>
      <Header
        title={t(`measurement.${mode.replace('-', '_')}`)}
        onBack={() => navigation.goBack()}
      />

      <MapView
        style={styles.map}
        polylines={
          currentPoints.length > 0
            ? [{ coordinates: currentPoints, strokeColor: '#2196F3' }]
            : []
        }
        markers={currentPoints.map((point, index) => ({
          coordinate: point,
          title: `Point ${index + 1}`,
        }))}
      />

      <View style={styles.infoPanel}>
        <View style={styles.infoRow}>
          <View style={styles.infoItem}>
            <Text style={styles.infoLabel}>{t('measurement.points')}</Text>
            <Text style={styles.infoValue}>{currentPoints.length}</Text>
          </View>
          <View style={styles.infoItem}>
            <Text style={styles.infoLabel}>{t('measurement.accuracy')}</Text>
            <Text style={styles.infoValue}>
              {accuracy.toFixed(1)}m
            </Text>
          </View>
        </View>

        {currentPoints.length >= 3 && (
          <View style={styles.areaInfo}>
            <View style={styles.areaItem}>
              <Text style={styles.areaLabel}>{t('measurement.acres')}</Text>
              <Text style={styles.areaValue}>{area.acres.toFixed(2)}</Text>
            </View>
            <View style={styles.areaItem}>
              <Text style={styles.areaLabel}>{t('measurement.hectares')}</Text>
              <Text style={styles.areaValue}>{area.hectares.toFixed(2)}</Text>
            </View>
          </View>
        )}
      </View>

      <View style={styles.controls}>
        {mode === 'walk-around' ? (
          <Button
            title={
              isTracking
                ? t('measurement.stop_measurement')
                : t('measurement.start_measurement')
            }
            onPress={isTracking ? handleStopTracking : handleStartTracking}
            variant={isTracking ? 'danger' : 'primary'}
          />
        ) : (
          <Button
            title={t('measurement.add_point')}
            onPress={handleAddPoint}
            variant="primary"
          />
        )}

        {currentPoints.length >= 3 && (
          <Button
            title={t('measurement.save_measurement')}
            onPress={handleSave}
            style={styles.saveButton}
          />
        )}

        {currentPoints.length > 0 && !isTracking && (
          <Button
            title={t('common.clear')}
            onPress={() => {
              Alert.alert(
                t('common.confirm'),
                t('measurement.clear_confirm'),
                [
                  { text: t('common.cancel'), style: 'cancel' },
                  { text: t('common.clear'), onPress: clearPoints },
                ]
              );
            }}
            variant="secondary"
            style={styles.clearButton}
          />
        )}
      </View>

      <Modal
        visible={showSaveModal}
        onClose={() => setShowSaveModal(false)}
        title={t('measurement.save_measurement')}
        actions={[
          {
            label: t('common.cancel'),
            onPress: () => setShowSaveModal(false),
            variant: 'secondary',
          },
          {
            label: t('common.save'),
            onPress: handleConfirmSave,
            variant: 'primary',
            loading: saving,
          },
        ]}
      >
        <Input
          label={t('measurement.land_name')}
          value={landData.name}
          onChangeText={(text) => setLandData({ ...landData, name: text })}
          placeholder={t('measurement.land_name_placeholder')}
        />
        <Input
          label={t('measurement.customer_name')}
          value={landData.customerName}
          onChangeText={(text) => setLandData({ ...landData, customerName: text })}
          placeholder={t('measurement.customer_name_placeholder')}
        />
        <Input
          label={t('measurement.customer_phone')}
          value={landData.customerPhone}
          onChangeText={(text) => setLandData({ ...landData, customerPhone: text })}
          type="phone"
          placeholder="+94771234567"
        />
        <Input
          label={t('measurement.location_name')}
          value={landData.locationName}
          onChangeText={(text) => setLandData({ ...landData, locationName: text })}
          placeholder={t('measurement.location_placeholder')}
        />
        <Input
          label={t('measurement.description')}
          value={landData.description}
          onChangeText={(text) => setLandData({ ...landData, description: text })}
          placeholder={t('measurement.description_placeholder')}
          multiline
          numberOfLines={3}
        />
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  map: {
    flex: 1,
  },
  infoPanel: {
    backgroundColor: '#FFFFFF',
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#DDD',
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  infoItem: {
    alignItems: 'center',
  },
  infoLabel: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  infoValue: {
    fontSize: 20,
    fontWeight: '600',
    color: '#333',
  },
  areaInfo: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: '#EEE',
  },
  areaItem: {
    alignItems: 'center',
  },
  areaLabel: {
    fontSize: 14,
    color: '#666',
    marginBottom: 4,
  },
  areaValue: {
    fontSize: 24,
    fontWeight: '700',
    color: '#2196F3',
  },
  controls: {
    padding: 16,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: '#DDD',
  },
  saveButton: {
    marginTop: 12,
  },
  clearButton: {
    marginTop: 12,
  },
});
