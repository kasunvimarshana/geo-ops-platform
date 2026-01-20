import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  SafeAreaView,
} from 'react-native';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { MeasurementMap } from '../components/MeasurementMap';
import { useMeasurementStore } from '../../../store/measurementStore';
import { formatArea } from '../utils/areaCalculator';

export default function MeasurementDetailScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const measurementId = parseInt(params.id as string, 10);
  
  const { measurements, deleteMeasurement, setCurrentMeasurement } = useMeasurementStore();
  const measurement = measurements.find(m => m.id === measurementId);

  if (!measurement) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>Measurement not found</Text>
          <TouchableOpacity 
            style={styles.backButton}
            onPress={() => router.back()}
          >
            <Text style={styles.backButtonText}>Go Back</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  const handleEdit = () => {
    // Edit functionality not yet implemented - hide button for now
    Alert.alert('Coming Soon', 'Edit functionality will be available in a future update');
  };

  const handleDelete = () => {
    Alert.alert(
      'Delete Measurement',
      `Are you sure you want to delete "${measurement.fieldName}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await deleteMeasurement(measurement.id!);
              router.back();
            } catch (error) {
              Alert.alert('Error', 'Failed to delete measurement');
            }
          },
        },
      ]
    );
  };

  const area = {
    areaSqm: measurement.areaSqm,
    areaAcres: measurement.areaAcres,
    areaHectares: measurement.areaHectares,
  };

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView style={styles.scrollView}>
        {/* Map View */}
        <View style={styles.mapContainer}>
          <MeasurementMap
            coordinates={measurement.coordinates}
            editable={false}
            showMarkers
          />
        </View>

        {/* Details Section */}
        <View style={styles.detailsContainer}>
          <View style={styles.header}>
            <Text style={styles.title}>{measurement.fieldName}</Text>
            {!measurement.synced && (
              <View style={styles.unsyncedBadge}>
                <Text style={styles.unsyncedText}>Not Synced</Text>
              </View>
            )}
          </View>

          {/* Area Information */}
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Area</Text>
            <View style={styles.areaContainer}>
              <View style={styles.areaItem}>
                <Text style={styles.areaLabel}>Acres</Text>
                <Text style={styles.areaValue}>{formatArea(area, 'acres')}</Text>
              </View>
              <View style={styles.areaItem}>
                <Text style={styles.areaLabel}>Hectares</Text>
                <Text style={styles.areaValue}>{formatArea(area, 'hectares')}</Text>
              </View>
              <View style={styles.areaItem}>
                <Text style={styles.areaLabel}>Sq. Meters</Text>
                <Text style={styles.areaValue}>{area.areaSqm.toFixed(2)}</Text>
              </View>
            </View>
          </View>

          {/* Coordinates Information */}
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Coordinates</Text>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Total Points:</Text>
              <Text style={styles.infoValue}>{measurement.coordinates.length}</Text>
            </View>
          </View>

          {/* Notes */}
          {measurement.notes && (
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>Notes</Text>
              <Text style={styles.notes}>{measurement.notes}</Text>
            </View>
          )}

          {/* Date Information */}
          <View style={styles.section}>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Created:</Text>
              <Text style={styles.infoValue}>
                {new Date(measurement.createdAt).toLocaleString()}
              </Text>
            </View>
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Updated:</Text>
              <Text style={styles.infoValue}>
                {new Date(measurement.updatedAt).toLocaleString()}
              </Text>
            </View>
          </View>

          {/* Actions */}
          <View style={styles.actionsContainer}>
            <TouchableOpacity style={styles.editButton} onPress={handleEdit}>
              <Text style={styles.editButtonText}>Edit</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.deleteButton} onPress={handleDelete}>
              <Text style={styles.deleteButtonText}>Delete</Text>
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  scrollView: {
    flex: 1,
  },
  mapContainer: {
    height: 300,
  },
  detailsContainer: {
    padding: 16,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  title: {
    fontSize: 24,
    fontWeight: '700',
    flex: 1,
  },
  unsyncedBadge: {
    backgroundColor: '#ff9500',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 4,
  },
  unsyncedText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  section: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 12,
    color: '#333',
  },
  areaContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  areaItem: {
    alignItems: 'center',
  },
  areaLabel: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  areaValue: {
    fontSize: 18,
    fontWeight: '700',
    color: '#007AFF',
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 6,
  },
  infoLabel: {
    fontSize: 14,
    color: '#666',
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
  },
  notes: {
    fontSize: 14,
    color: '#333',
    lineHeight: 20,
  },
  actionsContainer: {
    flexDirection: 'row',
    gap: 12,
    marginTop: 20,
  },
  editButton: {
    flex: 1,
    backgroundColor: '#007AFF',
    padding: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  editButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  deleteButton: {
    flex: 1,
    backgroundColor: '#ff3b30',
    padding: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  deleteButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 32,
  },
  errorText: {
    fontSize: 18,
    color: '#ff3b30',
    marginBottom: 20,
  },
  backButton: {
    backgroundColor: '#007AFF',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  backButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
