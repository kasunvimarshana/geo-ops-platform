/**
 * Printer Settings Screen
 * 
 * Allows users to:
 * - Scan for Bluetooth printers
 * - Connect/disconnect from printers
 * - View printer status
 * - Manage print queue
 * - Test print functionality
 */

import React, { useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { usePrinter } from '../../hooks/usePrinter';
import { BluetoothDevice } from '../../services/printerService';

export default function PrinterSettingsScreen() {
  const {
    status,
    isConnected,
    isScanning,
    devices,
    pendingJobsCount,
    printQueue,
    scanDevices,
    connectToDevice,
    disconnect,
    refreshStatus,
    clearFailedJobs,
    error,
    clearError,
  } = usePrinter();

  useEffect(() => {
    if (error) {
      Alert.alert('Printer Error', error, [
        { text: 'OK', onPress: clearError },
      ]);
    }
  }, [error, clearError]);

  const handleScan = async () => {
    await scanDevices();
  };

  const handleConnect = async (device: BluetoothDevice) => {
    const success = await connectToDevice(device);
    if (success) {
      Alert.alert('Success', `Connected to ${device.name}`);
    }
  };

  const handleDisconnect = async () => {
    Alert.alert(
      'Disconnect Printer',
      'Are you sure you want to disconnect from the printer?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Disconnect',
          style: 'destructive',
          onPress: async () => {
            await disconnect();
          },
        },
      ]
    );
  };

  const handleTestPrint = async () => {
    if (!isConnected) {
      Alert.alert('Error', 'Please connect to a printer first');
      return;
    }

    Alert.alert('Test Print', 'Printing test receipt...', [{ text: 'OK' }]);
  };

  const handleClearFailed = async () => {
    await clearFailedJobs();
    Alert.alert('Success', 'Failed print jobs cleared');
  };

  const getPaperStatusColor = (paperStatus: string) => {
    switch (paperStatus) {
      case 'ok':
        return '#4caf50';
      case 'low':
        return '#ff9800';
      case 'out':
        return '#f44336';
      default:
        return '#9e9e9e';
    }
  };

  const getJobStatusColor = (jobStatus: string) => {
    switch (jobStatus) {
      case 'completed':
        return '#4caf50';
      case 'printing':
        return '#2196f3';
      case 'pending':
        return '#ff9800';
      case 'failed':
        return '#f44336';
      default:
        return '#9e9e9e';
    }
  };

  return (
    <ScrollView
      style={styles.container}
      refreshControl={
        <RefreshControl refreshing={false} onRefresh={refreshStatus} />
      }
    >
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Printer Status</Text>
        
        {isConnected ? (
          <View>
            <View style={styles.statusRow}>
              <View style={[styles.statusDot, { backgroundColor: '#4caf50' }]} />
              <Text style={styles.statusText}>Connected</Text>
            </View>
            
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Device:</Text>
              <Text style={styles.infoValue}>{status.deviceName}</Text>
            </View>
            
            <TouchableOpacity
              style={[styles.button, styles.dangerButton]}
              onPress={handleDisconnect}
            >
              <Text style={styles.buttonText}>Disconnect</Text>
            </TouchableOpacity>
            
            <TouchableOpacity
              style={[styles.button, styles.secondaryButton]}
              onPress={handleTestPrint}
            >
              <Text style={styles.buttonText}>Test Print</Text>
            </TouchableOpacity>
          </View>
        ) : (
          <View>
            <View style={styles.statusRow}>
              <View style={[styles.statusDot, { backgroundColor: '#f44336' }]} />
              <Text style={styles.statusText}>Not Connected</Text>
            </View>
            <Text style={styles.helpText}>
              Scan for available Bluetooth printers to connect
            </Text>
          </View>
        )}
      </View>

      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <Text style={styles.cardTitle}>Available Printers</Text>
          <TouchableOpacity
            style={styles.scanButton}
            onPress={handleScan}
            disabled={isScanning}
          >
            {isScanning ? (
              <ActivityIndicator size="small" color="#2e7d32" />
            ) : (
              <Text style={styles.scanButtonText}>Scan</Text>
            )}
          </TouchableOpacity>
        </View>

        {devices.length === 0 ? (
          <Text style={styles.emptyText}>
            {isScanning ? 'Scanning for devices...' : 'No devices found'}
          </Text>
        ) : (
          devices.map((device, index) => (
            <TouchableOpacity
              key={index}
              style={styles.deviceCard}
              onPress={() => handleConnect(device)}
              disabled={isConnected && status.deviceAddress === device.address}
            >
              <View style={styles.deviceInfo}>
                <Text style={styles.deviceName}>{device.name}</Text>
                <Text style={styles.deviceAddress}>{device.address}</Text>
              </View>
              
              {isConnected && status.deviceAddress === device.address && (
                <View style={styles.connectedBadge}>
                  <Text style={styles.connectedText}>Connected</Text>
                </View>
              )}
            </TouchableOpacity>
          ))
        )}
      </View>

      <View style={styles.card}>
        <Text style={styles.cardTitle}>Bluetooth Printer Setup</Text>
        <Text style={styles.helpText}>
          1. Turn on your Bluetooth thermal printer{'\n'}
          2. Ensure Bluetooth is enabled on your device{'\n'}
          3. Tap "Scan" to find available printers{'\n'}
          4. Select your printer to connect{'\n'}
          5. Once connected, you can print invoices and receipts
        </Text>
        
        <View style={styles.infoBox}>
          <Text style={styles.infoBoxTitle}>Supported Printers</Text>
          <Text style={styles.infoBoxText}>
            • ESC/POS compatible thermal printers{'\n'}
            • 58mm or 80mm paper width{'\n'}
            • Bluetooth Classic or BLE
          </Text>
        </View>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  card: {
    backgroundColor: 'white',
    margin: 16,
    padding: 16,
    borderRadius: 8,
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
    marginBottom: 16,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 12,
  },
  statusRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  statusDot: {
    width: 12,
    height: 12,
    borderRadius: 6,
    marginRight: 8,
  },
  statusText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  infoLabel: {
    fontSize: 14,
    color: '#666',
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '500',
    color: '#333',
  },
  button: {
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 8,
    marginTop: 12,
    alignItems: 'center',
  },
  dangerButton: {
    backgroundColor: '#f44336',
  },
  secondaryButton: {
    backgroundColor: '#2196f3',
  },
  buttonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: '600',
  },
  helpText: {
    fontSize: 14,
    color: '#666',
    lineHeight: 20,
  },
  scanButton: {
    paddingVertical: 6,
    paddingHorizontal: 16,
    borderRadius: 6,
    backgroundColor: '#e8f5e9',
  },
  scanButtonText: {
    color: '#2e7d32',
    fontSize: 14,
    fontWeight: '600',
  },
  emptyText: {
    fontSize: 14,
    color: '#999',
    fontStyle: 'italic',
    textAlign: 'center',
    paddingVertical: 16,
  },
  deviceCard: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 12,
    borderWidth: 1,
    borderColor: '#e0e0e0',
    borderRadius: 8,
    marginBottom: 8,
  },
  deviceInfo: {
    flex: 1,
  },
  deviceName: {
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
    marginBottom: 4,
  },
  deviceAddress: {
    fontSize: 12,
    color: '#999',
  },
  connectedBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 4,
    backgroundColor: '#e8f5e9',
    marginLeft: 8,
  },
  connectedText: {
    fontSize: 12,
    color: '#4caf50',
    fontWeight: '600',
  },
  infoBox: {
    marginTop: 16,
    padding: 12,
    backgroundColor: '#f0f4f8',
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#2196f3',
  },
  infoBoxTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
    marginBottom: 8,
  },
  infoBoxText: {
    fontSize: 13,
    color: '#666',
    lineHeight: 20,
  },
});
