import { View, Text, StyleSheet, FlatList, TouchableOpacity } from 'react-native';
import { useState, useEffect } from 'react';
import { useRouter } from 'expo-router';
import { landMeasurementService } from '../../src/services/land-measurement.service';
import { LandMeasurement } from '../../src/types';
import { COLORS } from '../../src/constants';
import { MapPreview } from '../../src/components';

export default function HistoryTab() {
  const router = useRouter();
  const [measurements, setMeasurements] = useState<LandMeasurement[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadMeasurements();
  }, []);

  const loadMeasurements = async () => {
    try {
      const data = await landMeasurementService.getAll();
      setMeasurements(data);
    } catch (error) {
      console.error('Failed to load measurements:', error);
    } finally {
      setLoading(false);
    }
  };

  const renderItem = ({ item }: { item: LandMeasurement }) => (
    <TouchableOpacity 
      style={styles.card}
      onPress={() => {
        // TODO: Navigate to detail screen in future
        console.log('View measurement:', item.id);
      }}
      activeOpacity={0.7}
    >
      {/* Map Preview */}
      <MapPreview coordinates={item.coordinates} height={150} />
      
      {/* Measurement Details */}
      <View style={styles.cardContent}>
        <Text style={styles.cardTitle}>{item.name}</Text>
        <Text style={styles.cardDetail}>Area: {item.area.toFixed(2)} {item.unit}</Text>
        <Text style={styles.cardDetail}>Points: {item.coordinates.length}</Text>
        {item.address && <Text style={styles.cardDetail}>Location: {item.address}</Text>}
        <Text style={styles.cardDate}>
          {new Date(item.createdAt).toLocaleDateString()}
        </Text>
      </View>
    </TouchableOpacity>
  );

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <Text>Loading...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {measurements.length === 0 ? (
        <View style={styles.centerContainer}>
          <Text style={styles.emptyText}>No measurements yet</Text>
          <Text style={styles.emptySubtext}>
            Start measuring land to see your history here
          </Text>
        </View>
      ) : (
        <FlatList
          data={measurements}
          renderItem={renderItem}
          keyExtractor={(item) => item.id}
          contentContainerStyle={styles.listContent}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 20,
  },
  listContent: {
    padding: 15,
  },
  card: {
    backgroundColor: COLORS.surface,
    borderRadius: 12,
    marginBottom: 15,
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.15,
    shadowRadius: 4,
    overflow: 'hidden',
  },
  cardContent: {
    padding: 15,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.text,
    marginBottom: 8,
  },
  cardDetail: {
    fontSize: 14,
    color: COLORS.textSecondary,
    marginBottom: 4,
  },
  cardDate: {
    fontSize: 12,
    color: COLORS.textSecondary,
    marginTop: 8,
    fontStyle: 'italic',
  },
  emptyText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.text,
    marginBottom: 10,
  },
  emptySubtext: {
    fontSize: 14,
    color: COLORS.textSecondary,
    textAlign: 'center',
  },
});
