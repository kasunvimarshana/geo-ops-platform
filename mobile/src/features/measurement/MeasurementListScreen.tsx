import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Header, List, Card } from '@/components';
import { Land } from '@/types';
import { apiClient } from '@/services/api/client';
import { format } from 'date-fns';

interface MeasurementListScreenProps {
  navigation: any;
}

export const MeasurementListScreen: React.FC<MeasurementListScreenProps> = ({
  navigation,
}) => {
  const { t } = useTranslation();
  const [lands, setLands] = useState<Land[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    fetchLands();
  }, []);

  const fetchLands = async () => {
    try {
      setLoading(true);
      const response = await apiClient.get('/lands');
      if (response.success && response.data) {
        setLands(response.data);
      }
    } catch (error) {
      console.error('Error fetching lands:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    fetchLands();
  };

  const handleNewMeasurement = () => {
    Alert.alert(
      t('measurement.select_mode'),
      '',
      [
        {
          text: t('measurement.walk_around'),
          onPress: () => navigation.navigate('Measurement', { mode: 'walk-around' }),
        },
        {
          text: t('measurement.point_based'),
          onPress: () => navigation.navigate('Measurement', { mode: 'point-based' }),
        },
        {
          text: t('common.cancel'),
          style: 'cancel',
        },
      ]
    );
  };

  const renderLandItem = (land: Land) => {
    const statusColor = {
      draft: '#FFC107',
      confirmed: '#4CAF50',
      archived: '#9E9E9E',
    }[land.status];

    return (
      <Card
        onPress={() => navigation.navigate('MeasurementDetail', { landId: land.id })}
      >
        <View style={styles.landHeader}>
          <Text style={styles.landName}>{land.name}</Text>
          <View style={[styles.statusBadge, { backgroundColor: statusColor }]}>
            <Text style={styles.statusText}>
              {t(`measurement.status_${land.status}`)}
            </Text>
          </View>
        </View>

        <View style={styles.landInfo}>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('measurement.area')}:</Text>
            <Text style={styles.infoValue}>
              {land.area_acres.toFixed(2)} {t('measurement.acres')} / {' '}
              {land.area_hectares.toFixed(2)} {t('measurement.hectares')}
            </Text>
          </View>

          {land.customer_name && (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>{t('measurement.customer')}:</Text>
              <Text style={styles.infoValue}>{land.customer_name}</Text>
            </View>
          )}

          {land.location_name && (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>{t('measurement.location')}:</Text>
              <Text style={styles.infoValue}>{land.location_name}</Text>
            </View>
          )}

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('measurement.measured_at')}:</Text>
            <Text style={styles.infoValue}>
              {format(new Date(land.measured_at), 'MMM dd, yyyy')}
            </Text>
          </View>
        </View>

        {land.sync_status === 'pending' && (
          <View style={styles.syncBadge}>
            <Text style={styles.syncText}>⚠ {t('sync.pending')}</Text>
          </View>
        )}
      </Card>
    );
  };

  return (
    <View style={styles.container}>
      <Header
        title={t('measurement.title')}
        rightAction={{
          label: '+',
          onPress: handleNewMeasurement,
        }}
      />

      <List
        data={lands}
        renderItem={renderLandItem}
        keyExtractor={(item) => item.id.toString()}
        loading={loading}
        refreshing={refreshing}
        onRefresh={handleRefresh}
        emptyMessage={t('measurement.no_measurements')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  landHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  landName: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
    flex: 1,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#FFFFFF',
  },
  landInfo: {
    gap: 8,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  infoLabel: {
    fontSize: 14,
    color: '#666',
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '500',
    color: '#333',
  },
  syncBadge: {
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#EEE',
  },
  syncText: {
    fontSize: 12,
    color: '#FF9800',
    fontWeight: '500',
  },
});
