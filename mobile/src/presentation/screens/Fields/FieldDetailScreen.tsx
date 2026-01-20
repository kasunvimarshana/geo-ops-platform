import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  SafeAreaView,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Field } from '../../../domain/entities/Field';
import { useFieldStore } from '../../stores/fieldStore';

export default function FieldDetailScreen({ route, navigation }: any) {
  const { t } = useTranslation();
  const { field } = route.params as { field: Field };
  const { deleteField, isLoading } = useFieldStore();

  const handleEdit = () => {
    navigation.navigate('EditField', { field });
  };

  const handleDelete = async () => {
    Alert.alert(
      'Delete Field',
      'Are you sure you want to delete this field? This action cannot be undone.',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await deleteField(field.id);
              Alert.alert(
                'Success',
                'Field deleted successfully',
                [
                  {
                    text: 'OK',
                    onPress: () => navigation.navigate('Fields'),
                  },
                ]
              );
            } catch (error: any) {
              Alert.alert('Error', error.message || 'Failed to delete field');
            }
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
          onPress={() => navigation.goBack()}
        >
          <Text style={styles.backButtonText}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Field Details</Text>
        <TouchableOpacity style={styles.editButton} onPress={handleEdit}>
          <Text style={styles.editButtonText}>Edit</Text>
        </TouchableOpacity>
      </View>

      <ScrollView style={styles.content}>
        <View style={styles.section}>
          <Text style={styles.fieldName}>{field.name}</Text>
          {field.location && (
            <Text style={styles.fieldLocation}>📍 {field.location}</Text>
          )}
        </View>

        <View style={styles.infoGrid}>
          <View style={styles.infoCard}>
            <Text style={styles.infoLabel}>Area</Text>
            <Text style={styles.infoValue}>
              {field.area ? `${(field.area / 10000).toFixed(2)} ha` : 'N/A'}
            </Text>
            <Text style={styles.infoSubtext}>
              {field.area ? `${field.area.toFixed(0)} m²` : ''}
            </Text>
          </View>

          <View style={styles.infoCard}>
            <Text style={styles.infoLabel}>Perimeter</Text>
            <Text style={styles.infoValue}>
              {field.perimeter ? `${(field.perimeter / 1000).toFixed(2)} km` : 'N/A'}
            </Text>
            <Text style={styles.infoSubtext}>
              {field.perimeter ? `${field.perimeter.toFixed(0)} m` : ''}
            </Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Details</Text>
          
          <View style={styles.detailRow}>
            <Text style={styles.detailLabel}>Crop Type</Text>
            <Text style={styles.detailValue}>{field.crop_type || 'Not specified'}</Text>
          </View>

          <View style={styles.detailRow}>
            <Text style={styles.detailLabel}>Measurement Type</Text>
            <Text style={styles.detailValue}>
              {field.measurement_type?.replace('_', ' ') || 'N/A'}
            </Text>
          </View>

          {field.notes && (
            <View style={styles.detailRow}>
              <Text style={styles.detailLabel}>Notes</Text>
              <Text style={styles.detailValue}>{field.notes}</Text>
            </View>
          )}

          {field.boundary && (
            <View style={styles.detailRow}>
              <Text style={styles.detailLabel}>Boundary Points</Text>
              <Text style={styles.detailValue}>
                {(() => {
                  try {
                    const boundary = JSON.parse(field.boundary);
                    return boundary.coordinates?.[0]?.length || 0;
                  } catch (e) {
                    return 'Invalid';
                  }
                })()} points
              </Text>
            </View>
          )}
        </View>

        <TouchableOpacity
          style={[styles.deleteButton, isLoading && styles.deleteButtonDisabled]}
          onPress={handleDelete}
          disabled={isLoading}
        >
          {isLoading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.deleteButtonText}>Delete Field</Text>
          )}
        </TouchableOpacity>
      </ScrollView>
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
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  editButton: {
    padding: 5,
  },
  editButtonText: {
    fontSize: 16,
    color: '#27ae60',
    fontWeight: '600',
  },
  content: {
    flex: 1,
  },
  section: {
    backgroundColor: '#fff',
    padding: 20,
    marginBottom: 15,
  },
  fieldName: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 10,
  },
  fieldLocation: {
    fontSize: 16,
    color: '#7f8c8d',
  },
  infoGrid: {
    flexDirection: 'row',
    padding: 15,
    gap: 15,
  },
  infoCard: {
    flex: 1,
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  infoLabel: {
    fontSize: 12,
    color: '#95a5a6',
    textTransform: 'uppercase',
    marginBottom: 8,
  },
  infoValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#27ae60',
    marginBottom: 5,
  },
  infoSubtext: {
    fontSize: 12,
    color: '#7f8c8d',
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 15,
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#ecf0f1',
  },
  detailLabel: {
    fontSize: 16,
    color: '#7f8c8d',
  },
  detailValue: {
    fontSize: 16,
    color: '#2c3e50',
    fontWeight: '500',
    flex: 1,
    textAlign: 'right',
  },
  deleteButton: {
    backgroundColor: '#e74c3c',
    margin: 15,
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
  },
  deleteButtonDisabled: {
    opacity: 0.6,
  },
  deleteButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
