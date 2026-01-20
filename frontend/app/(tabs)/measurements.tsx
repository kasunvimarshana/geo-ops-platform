import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, ActivityIndicator, RefreshControl, Alert } from 'react-native';
import { measurementApi } from '../../src/services/api/measurements';
import { useRouter } from 'expo-router';

interface Measurement {
  id: number;
  name: string;
  area_sqm: number;
  area_acres: number;
  area_hectares: number;
  coordinates: any[];
  measured_at: string;
  created_at: string;
}

export default function MeasurementsScreen() {
  const router = useRouter();
  const [measurements, setMeasurements] = useState<Measurement[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchMeasurements = async () => {
    try {
      setError(null);
      const response = await measurementApi.getAll();
      setMeasurements(response.data.data || []);
    } catch (err: any) {
      console.error('Failed to fetch measurements:', err);
      setError(err.response?.data?.message || 'Failed to load measurements');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchMeasurements();
  }, []);

  const onRefresh = () => {
    setRefreshing(true);
    fetchMeasurements();
  };

  const handleDelete = async (id: number, name: string) => {
    Alert.alert(
      'Delete Measurement',
      `Are you sure you want to delete "${name}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await measurementApi.delete(id);
              setMeasurements(prev => prev.filter(m => m.id !== id));
            } catch (err: any) {
              Alert.alert('Error', err.response?.data?.message || 'Failed to delete measurement');
            }
          },
        },
      ]
    );
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#2e7d32" />
        <Text style={styles.loadingText}>Loading measurements...</Text>
      </View>
    );
  }

  if (!loading && measurements.length === 0) {
    return (
      <ScrollView 
        style={styles.container}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#2e7d32']} />
        }
      >
        <View style={styles.emptyState}>
          <Text style={styles.emptyIcon}>📏</Text>
          <Text style={styles.emptyTitle}>No Measurements Yet</Text>
          <Text style={styles.emptyText}>
            Start measuring land by walking around the perimeter or marking points on the map.
          </Text>
          {error && <Text style={styles.errorText}>{error}</Text>}
        </View>
      </ScrollView>
    );
  }

  return (
    <ScrollView 
      style={styles.container}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#2e7d32']} />
      }
    >
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Land Measurements</Text>
        <Text style={styles.headerSubtitle}>{measurements.length} total</Text>
      </View>

      {error && (
        <View style={styles.errorBanner}>
          <Text style={styles.errorBannerText}>⚠️ {error}</Text>
        </View>
      )}

      {measurements.map((measurement) => (
        <View key={measurement.id} style={styles.measurementCard}>
          <View style={styles.cardHeader}>
            <View style={styles.cardTitleContainer}>
              <Text style={styles.cardIcon}>📏</Text>
              <Text style={styles.cardTitle}>{measurement.name}</Text>
            </View>
            <TouchableOpacity 
              onPress={() => handleDelete(measurement.id, measurement.name)}
              style={styles.deleteButton}
            >
              <Text style={styles.deleteButtonText}>🗑️</Text>
            </TouchableOpacity>
          </View>

          <View style={styles.cardDetails}>
            <View style={styles.detailRow}>
              <Text style={styles.detailLabel}>Area:</Text>
              <Text style={styles.detailValue}>
                {measurement.area_acres?.toFixed(2)} acres / {measurement.area_hectares?.toFixed(2)} ha
              </Text>
            </View>
            <View style={styles.detailRow}>
              <Text style={styles.detailLabel}>Points:</Text>
              <Text style={styles.detailValue}>{measurement.coordinates?.length || 0} coordinates</Text>
            </View>
            <View style={styles.detailRow}>
              <Text style={styles.detailLabel}>Measured:</Text>
              <Text style={styles.detailValue}>
                {new Date(measurement.measured_at || measurement.created_at).toLocaleDateString()}
              </Text>
            </View>
          </View>

          <TouchableOpacity 
            style={styles.viewButton}
            onPress={() => Alert.alert('View Details', `Measurement ID: ${measurement.id}`)}
          >
            <Text style={styles.viewButtonText}>View Details</Text>
          </TouchableOpacity>
        </View>
      ))}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f5f5f5',
  },
  loadingText: {
    marginTop: 12,
    fontSize: 14,
    color: '#666',
  },
  header: {
    backgroundColor: '#2e7d32',
    padding: 20,
    paddingBottom: 24,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 4,
  },
  headerSubtitle: {
    fontSize: 14,
    color: '#a5d6a7',
  },
  errorBanner: {
    backgroundColor: '#ffebee',
    padding: 12,
    margin: 16,
    marginBottom: 8,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#c62828',
  },
  errorBannerText: {
    color: '#c62828',
    fontSize: 13,
  },
  measurementCard: {
    backgroundColor: '#fff',
    margin: 16,
    marginTop: 8,
    marginBottom: 8,
    borderRadius: 12,
    padding: 16,
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
  cardTitleContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  cardIcon: {
    fontSize: 24,
    marginRight: 8,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
    flex: 1,
  },
  deleteButton: {
    padding: 8,
  },
  deleteButtonText: {
    fontSize: 20,
  },
  cardDetails: {
    marginBottom: 16,
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 6,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  detailLabel: {
    fontSize: 14,
    color: '#666',
    fontWeight: '500',
  },
  detailValue: {
    fontSize: 14,
    color: '#333',
    fontWeight: '600',
  },
  viewButton: {
    backgroundColor: '#2e7d32',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  viewButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
  emptyState: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 40,
    marginTop: 100,
  },
  emptyIcon: {
    fontSize: 64,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 14,
    color: '#666',
    textAlign: 'center',
    lineHeight: 20,
  },
  errorText: {
    fontSize: 12,
    color: '#c62828',
    marginTop: 8,
    textAlign: 'center',
  },
});
