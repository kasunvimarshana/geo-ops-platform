/**
 * Printer Settings Screen
 * Bluetooth device discovery, connection, and printer management
 */

import React, { useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Button, Card, Divider } from 'react-native-paper';
import { usePrinterStore } from '../../../store/printerStore';
import { useTranslation } from 'react-i18next';
import type { BluetoothDevice } from '../../../shared/services/printer';

export const PrinterSettingsScreen: React.FC = () => {
  const { t } = useTranslation();
  const {
    connectedDevice,
    availableDevices,
    isScanning,
    isConnecting,
    isConnected,
    error,
    scanDevices,
    connectDevice,
    disconnectDevice,
    testPrint,
    clearError,
  } = usePrinterStore();

  useEffect(() => {
    // Auto-scan on mount
    scanDevices();
  }, []);

  const handleScan = async () => {
    try {
      clearError();
      await scanDevices();
    } catch (err) {
      Alert.alert(t('printer.error'), t('printer.scanFailed'));
    }
  };

  const handleConnect = async (device: BluetoothDevice) => {
    try {
      clearError();
      await connectDevice(device);
      Alert.alert(t('printer.success'), t('printer.connected'));
    } catch (err) {
      Alert.alert(t('printer.error'), t('printer.connectionFailed'));
    }
  };

  const handleDisconnect = async () => {
    try {
      clearError();
      await disconnectDevice();
      Alert.alert(t('printer.success'), t('printer.disconnected'));
    } catch (err) {
      Alert.alert(t('printer.error'), t('printer.disconnectionFailed'));
    }
  };

  const handleTestPrint = async () => {
    try {
      clearError();
      await testPrint();
      Alert.alert(t('printer.success'), t('printer.testPrintSuccess'));
    } catch (err) {
      Alert.alert(t('printer.error'), t('printer.testPrintFailed'));
    }
  };

  const renderDevice = ({ item }: { item: BluetoothDevice }) => {
    const isCurrentlyConnected = isConnected && connectedDevice?.id === item.id;

    return (
      <Card style={styles.deviceCard}>
        <Card.Content>
          <View style={styles.deviceHeader}>
            <View style={styles.deviceInfo}>
              <Text style={styles.deviceName}>{item.name}</Text>
              <Text style={styles.deviceAddress}>{item.address}</Text>
              {item.paired && (
                <Text style={styles.pairedBadge}>{t('printer.paired')}</Text>
              )}
            </View>
            {isCurrentlyConnected ? (
              <View style={styles.connectedBadge}>
                <Text style={styles.connectedText}>{t('printer.connected')}</Text>
              </View>
            ) : (
              <Button
                mode="contained"
                onPress={() => handleConnect(item)}
                loading={isConnecting}
                disabled={isConnecting || isScanning}
                style={styles.connectButton}
              >
                {t('printer.connect')}
              </Button>
            )}
          </View>
        </Card.Content>
      </Card>
    );
  };

  return (
    <View style={styles.container}>
      {/* Connection Status Card */}
      {isConnected && connectedDevice && (
        <Card style={styles.statusCard}>
          <Card.Content>
            <Text style={styles.statusTitle}>{t('printer.currentPrinter')}</Text>
            <Text style={styles.statusDevice}>{connectedDevice.name}</Text>
            <Text style={styles.statusAddress}>{connectedDevice.address}</Text>
            <View style={styles.statusActions}>
              <Button
                mode="outlined"
                onPress={handleTestPrint}
                style={styles.actionButton}
              >
                {t('printer.testPrint')}
              </Button>
              <Button
                mode="outlined"
                onPress={handleDisconnect}
                style={styles.actionButton}
              >
                {t('printer.disconnect')}
              </Button>
            </View>
          </Card.Content>
        </Card>
      )}

      {/* Error Display */}
      {error && (
        <Card style={styles.errorCard}>
          <Card.Content>
            <Text style={styles.errorText}>{error}</Text>
            <Button mode="text" onPress={clearError}>
              {t('common.dismiss')}
            </Button>
          </Card.Content>
        </Card>
      )}

      {/* Scan Button */}
      <View style={styles.scanSection}>
        <Button
          mode="contained"
          onPress={handleScan}
          loading={isScanning}
          disabled={isScanning}
          style={styles.scanButton}
          icon="bluetooth"
        >
          {isScanning ? t('printer.scanning') : t('printer.scanDevices')}
        </Button>
      </View>

      {/* Devices List */}
      <View style={styles.devicesSection}>
        <Text style={styles.sectionTitle}>{t('printer.availableDevices')}</Text>
        {isScanning ? (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" />
            <Text style={styles.loadingText}>{t('printer.scanningDevices')}</Text>
          </View>
        ) : availableDevices.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>{t('printer.noDevicesFound')}</Text>
            <Text style={styles.emptySubtext}>{t('printer.noDevicesHint')}</Text>
          </View>
        ) : (
          <FlatList
            data={availableDevices}
            renderItem={renderDevice}
            keyExtractor={(item) => item.id}
            contentContainerStyle={styles.devicesList}
          />
        )}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  statusCard: {
    margin: 16,
    backgroundColor: '#e8f5e9',
  },
  statusTitle: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
  },
  statusDevice: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2e7d32',
    marginBottom: 4,
  },
  statusAddress: {
    fontSize: 14,
    color: '#666',
    marginBottom: 16,
  },
  statusActions: {
    flexDirection: 'row',
    gap: 8,
  },
  actionButton: {
    flex: 1,
  },
  errorCard: {
    margin: 16,
    marginTop: 0,
    backgroundColor: '#ffebee',
  },
  errorText: {
    color: '#c62828',
    marginBottom: 8,
  },
  scanSection: {
    padding: 16,
    paddingTop: 0,
  },
  scanButton: {
    marginBottom: 8,
  },
  devicesSection: {
    flex: 1,
    padding: 16,
    paddingTop: 0,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 12,
    color: '#333',
  },
  devicesList: {
    gap: 12,
  },
  deviceCard: {
    marginBottom: 8,
  },
  deviceHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  deviceInfo: {
    flex: 1,
  },
  deviceName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 4,
  },
  deviceAddress: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  pairedBadge: {
    fontSize: 12,
    color: '#2196f3',
    fontWeight: '500',
  },
  connectedBadge: {
    backgroundColor: '#4caf50',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 4,
  },
  connectedText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 12,
  },
  connectButton: {
    marginLeft: 12,
  },
  loadingContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: 32,
  },
  loadingText: {
    marginTop: 12,
    color: '#666',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: 32,
  },
  emptyText: {
    fontSize: 16,
    color: '#666',
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#999',
    textAlign: 'center',
  },
});
