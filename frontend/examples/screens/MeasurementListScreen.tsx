import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  ActivityIndicator,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { Ionicons } from '@expo/vector-icons';
import { useMeasurementStore } from '@/stores/measurementStore';
import { Measurement } from '@/api/measurement.api';

/**
 * Measurement List Screen
 * 
 * Displays list of all measurements with pull-to-refresh
 * and offline support.
 */
export const MeasurementListScreen: React.FC = () => {
  const navigation = useNavigation();
  const {
    measurements,
    isLoading,
    error,
    fetchMeasurements,
    clearError,
  } = useMeasurementStore();

  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadMeasurements();
  }, []);

  const loadMeasurements = async () => {
    try {
      await fetchMeasurements();
    } catch (error) {
      console.error('Error loading measurements:', error);
    }
  };

  const handleRefresh = async () => {
    setRefreshing(true);
    await loadMeasurements();
    setRefreshing(false);
  };

  const handleMeasurementPress = (measurement: Measurement) => {
    navigation.navigate('MeasurementDetail', { id: measurement.id });
  };

  const handleCreatePress = () => {
    navigation.navigate('CreateMeasurement');
  };

  const renderMeasurementCard = ({ item }: { item: Measurement }) => (
    <TouchableOpacity
      style={styles.card}
      onPress={() => handleMeasurementPress(item)}
    >
      <View style={styles.cardHeader}>
        <Text style={styles.customerName}>{item.customer_name}</Text>
        <View style={[styles.statusBadge, getStatusStyle(item.status)]}>
          <Text style={styles.statusText}>{item.status}</Text>
        </View>
      </View>

      <View style={styles.cardContent}>
        <View style={styles.infoRow}>
          <Ionicons name="location-outline" size={16} color="#666" />
          <Text style={styles.infoText}>{item.location_name}</Text>
        </View>

        <View style={styles.infoRow}>
          <Ionicons name="expand-outline" size={16} color="#666" />
          <Text style={styles.infoText}>
            {item.area_acres.toFixed(2)} acres ({item.area_hectares.toFixed(2)} ha)
          </Text>
        </View>

        <View style={styles.infoRow}>
          <Ionicons name="call-outline" size={16} color="#666" />
          <Text style={styles.infoText}>{item.customer_phone}</Text>
        </View>

        <View style={styles.infoRow}>
          <Ionicons name="calendar-outline" size={16} color="#666" />
          <Text style={styles.infoText}>
            {new Date(item.measurement_date).toLocaleDateString()}
          </Text>
        </View>
      </View>

      <View style={styles.cardFooter}>
        <Text style={styles.measuredBy}>
          Measured by: {item.measured_by.name}
        </Text>
        <Ionicons name="chevron-forward" size={20} color="#007AFF" />
      </View>
    </TouchableOpacity>
  );

  const renderEmptyState = () => (
    <View style={styles.emptyState}>
      <Ionicons name="map-outline" size={64} color="#ccc" />
      <Text style={styles.emptyTitle}>No measurements yet</Text>
      <Text style={styles.emptyText}>
        Start by creating your first land measurement
      </Text>
      <TouchableOpacity style={styles.createButton} onPress={handleCreatePress}>
        <Text style={styles.createButtonText}>Create Measurement</Text>
      </TouchableOpacity>
    </View>
  );

  if (isLoading && !refreshing && measurements.length === 0) {
    return (
      <View style={styles.centered}>
        <ActivityIndicator size="large" color="#007AFF" />
        <Text style={styles.loadingText}>Loading measurements...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.centered}>
        <Ionicons name="alert-circle-outline" size={64} color="#FF3B30" />
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={loadMeasurements}>
          <Text style={styles.retryButtonText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <FlatList
        data={measurements}
        renderItem={renderMeasurementCard}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        ListEmptyComponent={renderEmptyState}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={handleRefresh}
            tintColor="#007AFF"
          />
        }
      />

      <TouchableOpacity style={styles.fab} onPress={handleCreatePress}>
        <Ionicons name="add" size={28} color="#fff" />
      </TouchableOpacity>
    </View>
  );
};

const getStatusStyle = (status: string) => {
  switch (status) {
    case 'completed':
      return { backgroundColor: '#34C759' };
    case 'verified':
      return { backgroundColor: '#007AFF' };
    case 'draft':
      return { backgroundColor: '#8E8E93' };
    default:
      return { backgroundColor: '#E5E5EA' };
  }
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F2F2F7',
  },
  listContent: {
    padding: 16,
    paddingBottom: 80,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  customerName: {
    fontSize: 18,
    fontWeight: '600',
    color: '#000',
    flex: 1,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  cardContent: {
    marginBottom: 12,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  infoText: {
    fontSize: 14,
    color: '#666',
    marginLeft: 8,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#E5E5EA',
    paddingTop: 12,
  },
  measuredBy: {
    fontSize: 12,
    color: '#8E8E93',
  },
  fab: {
    position: 'absolute',
    right: 20,
    bottom: 20,
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: '#007AFF',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 6,
    elevation: 8,
  },
  centered: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  loadingText: {
    marginTop: 12,
    fontSize: 16,
    color: '#666',
  },
  errorText: {
    marginTop: 12,
    fontSize: 16,
    color: '#FF3B30',
    textAlign: 'center',
  },
  retryButton: {
    marginTop: 16,
    paddingHorizontal: 24,
    paddingVertical: 12,
    backgroundColor: '#007AFF',
    borderRadius: 8,
  },
  retryButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  emptyState: {
    alignItems: 'center',
    paddingTop: 60,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: '600',
    color: '#000',
    marginTop: 16,
  },
  emptyText: {
    fontSize: 14,
    color: '#666',
    marginTop: 8,
    textAlign: 'center',
  },
  createButton: {
    marginTop: 24,
    paddingHorizontal: 24,
    paddingVertical: 12,
    backgroundColor: '#007AFF',
    borderRadius: 8,
  },
  createButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
