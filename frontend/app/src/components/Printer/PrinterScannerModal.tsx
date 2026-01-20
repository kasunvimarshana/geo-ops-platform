/**
 * Printer Scanner Modal Component
 * 
 * Allows users to scan for and connect to Bluetooth thermal printers
 */

import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  Modal,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  StyleSheet,
  Alert,
} from 'react-native';
import { Device } from 'react-native-ble-plx';
import { usePrinterStore } from '../../stores/printerStore';

interface PrinterScannerModalProps {
  visible: boolean;
  onClose: () => void;
  onDeviceConnected?: () => void;
}

export const PrinterScannerModal: React.FC<PrinterScannerModalProps> = ({
  visible,
  onClose,
  onDeviceConnected,
}) => {
  const {
    availableDevices,
    connectedDevice,
    isScanning,
    isConnecting,
    error,
    scanDevices,
    connectDevice,
    clearError,
  } = usePrinterStore();

  const [selectedDevice, setSelectedDevice] = useState<Device | null>(null);

  useEffect(() => {
    if (visible) {
      handleScan();
    }
  }, [visible]);

  useEffect(() => {
    if (error) {
      Alert.alert('Error', error, [{ text: 'OK', onPress: clearError }]);
    }
  }, [error]);

  const handleScan = async () => {
    try {
      await scanDevices();
    } catch (err) {
      console.error('Scan error:', err);
    }
  };

  const handleConnect = async (device: Device) => {
    setSelectedDevice(device);
    try {
      await connectDevice(device.id);
      Alert.alert('Success', `Connected to ${device.name}`, [
        {
          text: 'OK',
          onPress: () => {
            onDeviceConnected?.();
            onClose();
          },
        },
      ]);
    } catch (err) {
      console.error('Connect error:', err);
    } finally {
      setSelectedDevice(null);
    }
  };

  const renderDevice = ({ item }: { item: Device }) => {
    const isSelected = selectedDevice?.id === item.id;
    const isConnected = connectedDevice?.id === item.id;

    return (
      <TouchableOpacity
        style={[styles.deviceItem, isConnected && styles.deviceItemConnected]}
        onPress={() => handleConnect(item)}
        disabled={isConnecting}>
        <View style={styles.deviceInfo}>
          <Text style={styles.deviceName}>{item.name || 'Unknown Device'}</Text>
          <Text style={styles.deviceId}>{item.id}</Text>
        </View>
        {isSelected && isConnecting ? (
          <ActivityIndicator size="small" color="#007AFF" />
        ) : isConnected ? (
          <View style={styles.connectedBadge}>
            <Text style={styles.connectedText}>Connected</Text>
          </View>
        ) : null}
      </TouchableOpacity>
    );
  };

  return (
    <Modal visible={visible} animationType="slide" transparent={false} onRequestClose={onClose}>
      <View style={styles.container}>
        <View style={styles.header}>
          <Text style={styles.title}>Select Printer</Text>
          <TouchableOpacity onPress={onClose} style={styles.closeButton}>
            <Text style={styles.closeButtonText}>Close</Text>
          </TouchableOpacity>
        </View>

        <View style={styles.scanContainer}>
          {isScanning ? (
            <View style={styles.scanningIndicator}>
              <ActivityIndicator size="large" color="#007AFF" />
              <Text style={styles.scanningText}>Scanning for printers...</Text>
            </View>
          ) : (
            <TouchableOpacity style={styles.scanButton} onPress={handleScan}>
              <Text style={styles.scanButtonText}>Scan for Printers</Text>
            </TouchableOpacity>
          )}
        </View>

        <FlatList
          data={availableDevices}
          renderItem={renderDevice}
          keyExtractor={item => item.id}
          contentContainerStyle={styles.listContainer}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Text style={styles.emptyText}>
                {isScanning ? 'Searching for printers...' : 'No printers found. Tap scan to search.'}
              </Text>
            </View>
          }
        />

        <View style={styles.footer}>
          <Text style={styles.footerText}>
            Make sure your printer is powered on and in pairing mode.
          </Text>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  title: {
    fontSize: 20,
    fontWeight: 'bold',
  },
  closeButton: {
    padding: 8,
  },
  closeButtonText: {
    fontSize: 16,
    color: '#007AFF',
  },
  scanContainer: {
    padding: 16,
  },
  scanningIndicator: {
    alignItems: 'center',
    padding: 20,
  },
  scanningText: {
    marginTop: 10,
    fontSize: 16,
    color: '#666',
  },
  scanButton: {
    backgroundColor: '#007AFF',
    padding: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  scanButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  listContainer: {
    padding: 16,
  },
  deviceItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    backgroundColor: '#f5f5f5',
    borderRadius: 8,
    marginBottom: 12,
  },
  deviceItemConnected: {
    backgroundColor: '#e8f5e9',
    borderWidth: 2,
    borderColor: '#4caf50',
  },
  deviceInfo: {
    flex: 1,
  },
  deviceName: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 4,
  },
  deviceId: {
    fontSize: 12,
    color: '#666',
  },
  connectedBadge: {
    backgroundColor: '#4caf50',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  connectedText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  emptyContainer: {
    padding: 40,
    alignItems: 'center',
  },
  emptyText: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
  },
  footer: {
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: '#e0e0e0',
  },
  footerText: {
    fontSize: 14,
    color: '#666',
    textAlign: 'center',
  },
});
