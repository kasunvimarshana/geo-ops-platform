import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ScrollView,
  SafeAreaView,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useFieldStore } from '../../stores/fieldStore';
import { FieldCreateData } from '../../../domain/entities/Field';

export default function CreateFieldScreen({ navigation, route }: any) {
  const { t } = useTranslation();
  const { createField, isLoading } = useFieldStore();
  
  // Get measurement data from route params if available
  const measurement = route.params?.measurement;
  
  const [formData, setFormData] = useState<Partial<FieldCreateData>>({
    name: '',
    location: '',
    crop_type: '',
    measurement_type: measurement?.measurement_type || 'walk_around',
    boundary: measurement?.boundary || { type: 'Polygon', coordinates: [[]] },
    area: measurement?.area,
    perimeter: measurement?.perimeter,
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!formData.name || formData.name.trim() === '') {
      newErrors.name = 'Field name is required';
    }

    if (!formData.location || formData.location.trim() === '') {
      newErrors.location = 'Location is required';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async () => {
    if (!validateForm()) {
      Alert.alert('Validation Error', 'Please fill in all required fields');
      return;
    }

    try {
      await createField(formData as FieldCreateData);
      Alert.alert(
        'Success',
        'Field created successfully',
        [
          {
            text: 'OK',
            onPress: () => navigation.goBack(),
          },
        ]
      );
    } catch (error: any) {
      Alert.alert('Error', error.message || 'Failed to create field');
    }
  };

  const navigateToGPSMeasurement = () => {
    navigation.navigate('GPSMeasurement', {
      onMeasurementComplete: (boundary: any, area: number, perimeter: number) => {
        setFormData({
          ...formData,
          boundary,
          area,
          perimeter,
        });
      },
    });
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.backButton}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Create Field</Text>
        <View style={{ width: 60 }} />
      </View>

      <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Field Information</Text>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Field Name *</Text>
            <TextInput
              style={[styles.input, errors.name && styles.inputError]}
              placeholder="Enter field name"
              value={formData.name}
              onChangeText={(text) => {
                setFormData({ ...formData, name: text });
                if (errors.name) setErrors({ ...errors, name: '' });
              }}
            />
            {errors.name && <Text style={styles.errorText}>{errors.name}</Text>}
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Location *</Text>
            <TextInput
              style={[styles.input, errors.location && styles.inputError]}
              placeholder="Enter location"
              value={formData.location}
              onChangeText={(text) => {
                setFormData({ ...formData, location: text });
                if (errors.location) setErrors({ ...errors, location: '' });
              }}
            />
            {errors.location && <Text style={styles.errorText}>{errors.location}</Text>}
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Crop Type</Text>
            <TextInput
              style={styles.input}
              placeholder="Enter crop type (e.g., Rice, Wheat)"
              value={formData.crop_type}
              onChangeText={(text) => setFormData({ ...formData, crop_type: text })}
            />
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Measurement</Text>

          <TouchableOpacity
            style={styles.measurementButton}
            onPress={navigateToGPSMeasurement}
          >
            <Text style={styles.measurementIcon}>📍</Text>
            <View style={styles.measurementInfo}>
              <Text style={styles.measurementTitle}>GPS Measurement</Text>
              <Text style={styles.measurementSubtitle}>
                {formData.area
                  ? `Area: ${(formData.area / 10000).toFixed(2)} ha`
                  : 'Tap to measure field boundaries'}
              </Text>
            </View>
            <Text style={styles.measurementArrow}>→</Text>
          </TouchableOpacity>

          {formData.area && formData.perimeter && (
            <View style={styles.measurementResults}>
              <View style={styles.resultItem}>
                <Text style={styles.resultLabel}>Area</Text>
                <Text style={styles.resultValue}>
                  {(formData.area / 10000).toFixed(2)} ha
                </Text>
              </View>
              <View style={styles.resultItem}>
                <Text style={styles.resultLabel}>Perimeter</Text>
                <Text style={styles.resultValue}>
                  {(formData.perimeter / 1000).toFixed(2)} km
                </Text>
              </View>
            </View>
          )}
        </View>

        <TouchableOpacity
          style={[styles.submitButton, isLoading && styles.submitButtonDisabled]}
          onPress={handleSubmit}
          disabled={isLoading}
        >
          {isLoading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.submitButtonText}>Create Field</Text>
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
    fontSize: 16,
    color: '#27ae60',
    fontWeight: '600',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  content: {
    flex: 1,
  },
  contentContainer: {
    padding: 15,
  },
  section: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 15,
    marginBottom: 15,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 15,
  },
  inputGroup: {
    marginBottom: 15,
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#34495e',
    marginBottom: 5,
  },
  input: {
    backgroundColor: '#f8f9fa',
    borderWidth: 1,
    borderColor: '#e0e0e0',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    color: '#2c3e50',
  },
  inputError: {
    borderColor: '#e74c3c',
  },
  errorText: {
    color: '#e74c3c',
    fontSize: 12,
    marginTop: 5,
  },
  measurementButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f8f9fa',
    borderWidth: 1,
    borderColor: '#27ae60',
    borderRadius: 12,
    padding: 15,
  },
  measurementIcon: {
    fontSize: 32,
    marginRight: 15,
  },
  measurementInfo: {
    flex: 1,
  },
  measurementTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 3,
  },
  measurementSubtitle: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  measurementArrow: {
    fontSize: 20,
    color: '#27ae60',
  },
  measurementResults: {
    flexDirection: 'row',
    marginTop: 15,
    gap: 15,
  },
  resultItem: {
    flex: 1,
    backgroundColor: '#e8f5e9',
    padding: 12,
    borderRadius: 8,
  },
  resultLabel: {
    fontSize: 12,
    color: '#7f8c8d',
    marginBottom: 3,
  },
  resultValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#27ae60',
  },
  submitButton: {
    backgroundColor: '#27ae60',
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
    marginTop: 15,
    marginBottom: 30,
  },
  submitButtonDisabled: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
