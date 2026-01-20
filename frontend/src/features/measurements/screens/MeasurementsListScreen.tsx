import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  SafeAreaView,
} from 'react-native';
import { useRouter } from 'expo-router';
import { useMeasurementStore, Measurement } from '../../../store/measurementStore';
import { formatArea, calculatePolygonArea } from '../utils/areaCalculator';

export default function MeasurementsListScreen() {
  const router = useRouter();
  const { measurements, loadMeasurements, deleteMeasurement } = useMeasurementStore();
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadMeasurements();
  }, []);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadMeasurements();
    setRefreshing(false);
  };

  const handleNewMeasurement = () => {
    router.push('/measurements/walk-around' as any);
  };

  const handleViewMeasurement = (measurement: Measurement) => {
    router.push(`/measurements/${measurement.id}` as any);
  };

  const renderMeasurementItem = ({ item }: { item: Measurement }) => {
    const area = {
      areaSqm: item.areaSqm,
      areaAcres: item.areaAcres,
      areaHectares: item.areaHectares,
    };

    return (
      <TouchableOpacity
        style={styles.measurementCard}
        onPress={() => handleViewMeasurement(item)}
      >
        <View style={styles.cardHeader}>
          <Text style={styles.fieldName}>{item.fieldName}</Text>
          {!item.synced && (
            <View style={styles.unsyncedBadge}>
              <Text style={styles.unsyncedText}>Not Synced</Text>
            </View>
          )}
        </View>

        <View style={styles.cardContent}>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Area:</Text>
            <Text style={styles.infoValue}>
              {formatArea(area, 'acres')}
            </Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Points:</Text>
            <Text style={styles.infoValue}>{item.coordinates.length}</Text>
          </View>

          {item.notes && (
            <View style={styles.notesContainer}>
              <Text style={styles.notes} numberOfLines={2}>
                {item.notes}
              </Text>
            </View>
          )}

          <Text style={styles.date}>
            {new Date(item.createdAt).toLocaleDateString()}
          </Text>
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Land Measurements</Text>
        <TouchableOpacity style={styles.newButton} onPress={handleNewMeasurement}>
          <Text style={styles.newButtonText}>+ New</Text>
        </TouchableOpacity>
      </View>

      {measurements.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyText}>No measurements yet</Text>
          <Text style={styles.emptySubtext}>
            Create your first measurement by walking around a field
          </Text>
          <TouchableOpacity style={styles.emptyButton} onPress={handleNewMeasurement}>
            <Text style={styles.emptyButtonText}>Start Measuring</Text>
          </TouchableOpacity>
        </View>
      ) : (
        <FlatList
          data={measurements}
          renderItem={renderMeasurementItem}
          keyExtractor={(item) => item.id?.toString() || Math.random().toString()}
          contentContainerStyle={styles.list}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
          }
        />
      )}
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
    padding: 16,
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#ddd',
  },
  title: {
    fontSize: 24,
    fontWeight: '700',
  },
  newButton: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
  },
  newButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  list: {
    padding: 16,
  },
  measurementCard: {
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
  fieldName: {
    fontSize: 18,
    fontWeight: '600',
    flex: 1,
  },
  unsyncedBadge: {
    backgroundColor: '#ff9500',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
  },
  unsyncedText: {
    color: '#fff',
    fontSize: 10,
    fontWeight: '600',
  },
  cardContent: {},
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  infoLabel: {
    fontSize: 14,
    color: '#666',
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '600',
  },
  notesContainer: {
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#eee',
  },
  notes: {
    fontSize: 13,
    color: '#666',
    fontStyle: 'italic',
  },
  date: {
    fontSize: 12,
    color: '#999',
    marginTop: 8,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 32,
  },
  emptyText: {
    fontSize: 20,
    fontWeight: '600',
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#666',
    textAlign: 'center',
    marginBottom: 24,
  },
  emptyButton: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  emptyButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
