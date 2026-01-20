/**
 * Printer Connection Status Component
 * 
 * Shows current printer connection status and provides quick actions
 */

import React, { useEffect } from 'react';
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { usePrinterStore } from '../../stores/printerStore';

interface PrinterConnectionStatusProps {
  onOpenSettings?: () => void;
}

export const PrinterConnectionStatus: React.FC<PrinterConnectionStatusProps> = ({
  onOpenSettings,
}) => {
  const { connectedDevice, printerStatus, getStatus } = usePrinterStore();

  useEffect(() => {
    getStatus();
    // Refresh status every 10 seconds
    const interval = setInterval(getStatus, 10000);
    return () => clearInterval(interval);
  }, []);

  const isConnected = printerStatus?.isConnected || false;
  const queueLength = printerStatus?.queueLength || 0;

  return (
    <View style={styles.container}>
      <View style={styles.statusContainer}>
        <View style={[styles.statusIndicator, isConnected && styles.statusIndicatorConnected]} />
        <View style={styles.statusInfo}>
          <Text style={styles.statusText}>
            {isConnected ? 'Printer Connected' : 'No Printer Connected'}
          </Text>
          {connectedDevice && (
            <Text style={styles.deviceName}>{connectedDevice.name || 'Unknown Device'}</Text>
          )}
          {queueLength > 0 && (
            <Text style={styles.queueText}>{queueLength} items in print queue</Text>
          )}
        </View>
      </View>

      {onOpenSettings && (
        <TouchableOpacity style={styles.settingsButton} onPress={onOpenSettings}>
          <Text style={styles.settingsButtonText}>Settings</Text>
        </TouchableOpacity>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 12,
    backgroundColor: '#f5f5f5',
    borderRadius: 8,
    marginBottom: 16,
  },
  statusContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  statusIndicator: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: '#ff9800',
    marginRight: 12,
  },
  statusIndicatorConnected: {
    backgroundColor: '#4caf50',
  },
  statusInfo: {
    flex: 1,
  },
  statusText: {
    fontSize: 14,
    fontWeight: '600',
    marginBottom: 2,
  },
  deviceName: {
    fontSize: 12,
    color: '#666',
  },
  queueText: {
    fontSize: 12,
    color: '#ff9800',
    marginTop: 2,
  },
  settingsButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    backgroundColor: '#007AFF',
    borderRadius: 6,
  },
  settingsButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
});
