import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { useTranslation } from 'react-i18next';

export default function GPSMeasurementScreen({ navigation }: any) {
  const { t } = useTranslation();
  const [selectedMode, setSelectedMode] = useState<string | null>(null);

  const measurementModes = [
    {
      id: 'walk_around',
      title: 'Walk Around',
      description: 'Walk around the field perimeter while GPS tracks your path',
      icon: '🚶',
      color: '#27ae60',
    },
    {
      id: 'polygon',
      title: 'Polygon',
      description: 'Manually place points on a map to define field boundaries',
      icon: '📐',
      color: '#3498db',
    },
    {
      id: 'manual',
      title: 'Manual Entry',
      description: 'Enter coordinates manually for precise measurements',
      icon: '⌨️',
      color: '#9b59b6',
    },
  ];

  const startMeasurement = (mode: string) => {
    setSelectedMode(mode);
    
    switch (mode) {
      case 'walk_around':
        navigation.navigate('WalkAroundMeasurement');
        break;
      case 'polygon':
        navigation.navigate('PolygonMeasurement');
        break;
      case 'manual':
        navigation.navigate('ManualMeasurement');
        break;
      default:
        Alert.alert('Coming Soon', `${mode} measurement will be available soon`);
    }
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
        <Text style={styles.headerTitle}>{t('gps.title')}</Text>
        <View style={{ width: 50 }} />
      </View>

      <View style={styles.content}>
        <Text style={styles.instructionText}>
          Choose a measurement method to start
        </Text>

        {measurementModes.map((mode) => (
          <TouchableOpacity
            key={mode.id}
            style={[styles.modeCard, { borderLeftColor: mode.color }]}
            onPress={() => startMeasurement(mode.id)}
          >
            <Text style={styles.modeIcon}>{mode.icon}</Text>
            <View style={styles.modeTextContainer}>
              <Text style={styles.modeTitle}>{mode.title}</Text>
              <Text style={styles.modeDescription}>{mode.description}</Text>
            </View>
          </TouchableOpacity>
        ))}

        <View style={styles.infoBox}>
          <Text style={styles.infoTitle}>📍 GPS Accuracy Tips</Text>
          <Text style={styles.infoText}>• Ensure GPS is enabled</Text>
          <Text style={styles.infoText}>• Best accuracy in open areas</Text>
          <Text style={styles.infoText}>• Wait for strong signal (≤10m accuracy)</Text>
          <Text style={styles.infoText}>• Avoid measurements near tall buildings</Text>
        </View>
      </View>
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
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  content: {
    flex: 1,
    padding: 15,
  },
  instructionText: {
    fontSize: 16,
    color: '#7f8c8d',
    textAlign: 'center',
    marginBottom: 20,
    marginTop: 10,
  },
  modeCard: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    marginBottom: 15,
    alignItems: 'center',
    borderLeftWidth: 5,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  modeIcon: {
    fontSize: 48,
    marginRight: 15,
  },
  modeTextContainer: {
    flex: 1,
  },
  modeTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 5,
  },
  modeDescription: {
    fontSize: 14,
    color: '#7f8c8d',
    lineHeight: 20,
  },
  infoBox: {
    backgroundColor: '#e8f5e9',
    padding: 20,
    borderRadius: 12,
    marginTop: 20,
    borderWidth: 1,
    borderColor: '#27ae60',
  },
  infoTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#27ae60',
    marginBottom: 10,
  },
  infoText: {
    fontSize: 14,
    color: '#2c3e50',
    marginBottom: 5,
  },
});
