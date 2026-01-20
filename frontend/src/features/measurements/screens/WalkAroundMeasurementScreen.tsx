import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  TextInput,
  Alert,
  ScrollView,
  SafeAreaView,
} from 'react-native';
import { useRouter } from 'expo-router';
import { MeasurementMap } from '../components/MeasurementMap';
import { useMeasurementStore } from '../../../store/measurementStore';
import { useLocation } from '../../../hooks/useLocation';
import { formatArea } from '../utils/areaCalculator';

export default function WalkAroundMeasurementScreen() {
  const router = useRouter();
  const [fieldName, setFieldName] = useState('');
  const [notes, setNotes] = useState('');

  const {
    isRecording,
    recordedCoordinates,
    startRecording,
    stopRecording,
    addCoordinate,
    removeLastCoordinate,
    clearCoordinates,
    saveMeasurement,
    calculateCurrentArea,
  } = useMeasurementStore();

  const { location, startWatching, stopWatching, error: locationError } = useLocation();

  const area = calculateCurrentArea();

  useEffect(() => {
    // Start watching location when component mounts
    startWatching();

    return () => {
      stopWatching();
    };
  }, []);

  useEffect(() => {
    // Auto-add coordinates during recording
    if (isRecording && location) {
      const lastCoord = recordedCoordinates[recordedCoordinates.length - 1];
      
      // Only add if location has moved significantly (> 2 meters)
      if (!lastCoord || 
          Math.abs(lastCoord.latitude - location.latitude) > 0.00002 ||
          Math.abs(lastCoord.longitude - location.longitude) > 0.00002) {
        addCoordinate({
          latitude: location.latitude,
          longitude: location.longitude,
        });
      }
    }
  }, [location, isRecording]);

  const handleStartRecording = () => {
    if (!location) {
      Alert.alert('GPS Error', 'Waiting for GPS location...');
      return;
    }
    
    startRecording();
    Alert.alert('Recording Started', 'Walk around the field boundary. Coordinates will be automatically recorded.');
  };

  const handleStopRecording = () => {
    stopRecording();
    Alert.alert('Recording Stopped', `Recorded ${recordedCoordinates.length} points`);
  };

  const handleAddManualPoint = () => {
    if (!location) {
      Alert.alert('GPS Error', 'Cannot get current location');
      return;
    }

    addCoordinate({
      latitude: location.latitude,
      longitude: location.longitude,
    });
  };

  const handleSave = async () => {
    if (!fieldName.trim()) {
      Alert.alert('Validation Error', 'Please enter a field name');
      return;
    }

    if (recordedCoordinates.length < 3) {
      Alert.alert('Validation Error', 'At least 3 points are required');
      return;
    }

    try {
      await saveMeasurement({
        fieldName: fieldName.trim(),
        coordinates: recordedCoordinates,
        notes: notes.trim() || undefined,
      });

      Alert.alert('Success', 'Measurement saved successfully', [
        {
          text: 'OK',
          onPress: () => router.back(),
        },
      ]);
    } catch (error) {
      Alert.alert('Error', 'Failed to save measurement');
      console.error('Save error:', error);
    }
  };

  const handleClear = () => {
    Alert.alert(
      'Clear Measurement',
      'Are you sure you want to clear all recorded points?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Clear',
          style: 'destructive',
          onPress: () => {
            clearCoordinates();
            stopRecording();
          },
        },
      ]
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.mapContainer}>
        <MeasurementMap
          coordinates={recordedCoordinates}
          currentLocation={location}
          editable={false}
          showMarkers
        />
      </View>

      <View style={styles.controlsContainer}>
        <ScrollView style={styles.controls}>
          {/* Status */}
          <View style={styles.statusContainer}>
            <Text style={styles.statusLabel}>Status:</Text>
            <Text style={[styles.statusValue, isRecording && styles.recording]}>
              {isRecording ? '🔴 Recording' : '⚪ Stopped'}
            </Text>
          </View>

          {/* Points Count */}
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Points:</Text>
            <Text style={styles.infoValue}>{recordedCoordinates.length}</Text>
          </View>

          {/* Area */}
          {area && (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Area:</Text>
              <Text style={styles.infoValue}>
                {formatArea(area, 'acres')} / {formatArea(area, 'hectares')}
              </Text>
            </View>
          )}

          {/* GPS Status */}
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>GPS:</Text>
            <Text style={[styles.infoValue, !location && styles.gpsError]}>
              {location ? '✓ Active' : '✗ Waiting...'}
            </Text>
          </View>

          {locationError && (
            <Text style={styles.errorText}>{locationError}</Text>
          )}

          {/* Recording Controls */}
          <View style={styles.buttonRow}>
            {!isRecording ? (
              <TouchableOpacity style={styles.startButton} onPress={handleStartRecording}>
                <Text style={styles.buttonText}>Start Walk-Around</Text>
              </TouchableOpacity>
            ) : (
              <TouchableOpacity style={styles.stopButton} onPress={handleStopRecording}>
                <Text style={styles.buttonText}>Stop Recording</Text>
              </TouchableOpacity>
            )}

            <TouchableOpacity 
              style={styles.manualButton} 
              onPress={handleAddManualPoint}
              disabled={!location}
            >
              <Text style={styles.buttonText}>Add Point</Text>
            </TouchableOpacity>
          </View>

          {/* Undo/Clear */}
          {recordedCoordinates.length > 0 && (
            <View style={styles.buttonRow}>
              <TouchableOpacity 
                style={styles.undoButton} 
                onPress={removeLastCoordinate}
              >
                <Text style={styles.buttonText}>Undo Last</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.clearButton} onPress={handleClear}>
                <Text style={styles.buttonText}>Clear All</Text>
              </TouchableOpacity>
            </View>
          )}

          {/* Field Details */}
          <View style={styles.inputContainer}>
            <Text style={styles.inputLabel}>Field Name *</Text>
            <TextInput
              style={styles.input}
              value={fieldName}
              onChangeText={setFieldName}
              placeholder="Enter field name"
              placeholderTextColor="#999"
            />
          </View>

          <View style={styles.inputContainer}>
            <Text style={styles.inputLabel}>Notes</Text>
            <TextInput
              style={[styles.input, styles.textArea]}
              value={notes}
              onChangeText={setNotes}
              placeholder="Add notes (optional)"
              placeholderTextColor="#999"
              multiline
              numberOfLines={3}
            />
          </View>

          {/* Save Button */}
          <TouchableOpacity
            style={[
              styles.saveButton,
              (recordedCoordinates.length < 3 || !fieldName.trim()) && styles.saveButtonDisabled,
            ]}
            onPress={handleSave}
            disabled={recordedCoordinates.length < 3 || !fieldName.trim()}
          >
            <Text style={styles.saveButtonText}>Save Measurement</Text>
          </TouchableOpacity>
        </ScrollView>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  mapContainer: {
    flex: 1,
  },
  controlsContainer: {
    height: '50%',
    borderTopWidth: 1,
    borderTopColor: '#ddd',
  },
  controls: {
    padding: 16,
  },
  statusContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  statusLabel: {
    fontSize: 16,
    fontWeight: '600',
    marginRight: 8,
  },
  statusValue: {
    fontSize: 16,
    color: '#666',
  },
  recording: {
    color: '#ff3b30',
    fontWeight: '600',
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  infoLabel: {
    fontSize: 14,
    color: '#666',
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '600',
  },
  gpsError: {
    color: '#ff3b30',
  },
  errorText: {
    color: '#ff3b30',
    fontSize: 12,
    marginBottom: 8,
  },
  buttonRow: {
    flexDirection: 'row',
    gap: 8,
    marginVertical: 8,
  },
  startButton: {
    flex: 1,
    backgroundColor: '#34c759',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  stopButton: {
    flex: 1,
    backgroundColor: '#ff3b30',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  manualButton: {
    flex: 1,
    backgroundColor: '#007AFF',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  undoButton: {
    flex: 1,
    backgroundColor: '#ff9500',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  clearButton: {
    flex: 1,
    backgroundColor: '#ff3b30',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  buttonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
  inputContainer: {
    marginTop: 16,
  },
  inputLabel: {
    fontSize: 14,
    fontWeight: '600',
    marginBottom: 4,
  },
  input: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 8,
    padding: 12,
    fontSize: 14,
  },
  textArea: {
    height: 80,
    textAlignVertical: 'top',
  },
  saveButton: {
    backgroundColor: '#007AFF',
    padding: 16,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 16,
  },
  saveButtonDisabled: {
    backgroundColor: '#ccc',
  },
  saveButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
