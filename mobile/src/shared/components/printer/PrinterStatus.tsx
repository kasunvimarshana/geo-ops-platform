/**
 * Printer Connection Status Component
 * Shows current printer connection status
 */

import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { usePrinterStore } from '../../../store/printerStore';
import { useTranslation } from 'react-i18next';

interface PrinterStatusProps {
  onPress?: () => void;
  compact?: boolean;
}

export const PrinterStatus: React.FC<PrinterStatusProps> = ({ onPress, compact = false }) => {
  const { t } = useTranslation();
  const { isConnected, connectedDevice, queueStats } = usePrinterStore();

  const content = (
    <View style={[styles.container, compact && styles.containerCompact]}>
      {isConnected && connectedDevice ? (
        <View style={styles.connected}>
          <View style={styles.statusIndicator} />
          <View style={styles.info}>
            <Text style={styles.statusText}>{t('printer.connected')}</Text>
            {!compact && (
              <Text style={styles.deviceName}>{connectedDevice.name}</Text>
            )}
          </View>
          {queueStats.pending > 0 && (
            <View style={styles.badge}>
              <Text style={styles.badgeText}>{queueStats.pending}</Text>
            </View>
          )}
        </View>
      ) : (
        <View style={styles.disconnected}>
          <View style={[styles.statusIndicator, styles.statusDisconnected]} />
          <Text style={styles.statusTextDisconnected}>
            {t('printer.notConnected')}
          </Text>
        </View>
      )}
    </View>
  );

  if (onPress) {
    return (
      <TouchableOpacity onPress={onPress} activeOpacity={0.7}>
        {content}
      </TouchableOpacity>
    );
  }

  return content;
};

const styles = StyleSheet.create({
  container: {
    padding: 12,
    backgroundColor: '#fff',
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#e0e0e0',
  },
  containerCompact: {
    padding: 8,
  },
  connected: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  disconnected: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  statusIndicator: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: '#4caf50',
  },
  statusDisconnected: {
    backgroundColor: '#f44336',
  },
  info: {
    flex: 1,
  },
  statusText: {
    fontSize: 14,
    fontWeight: '600',
    color: '#4caf50',
  },
  statusTextDisconnected: {
    fontSize: 14,
    fontWeight: '600',
    color: '#666',
  },
  deviceName: {
    fontSize: 12,
    color: '#666',
    marginTop: 2,
  },
  badge: {
    backgroundColor: '#ff9800',
    borderRadius: 12,
    minWidth: 24,
    height: 24,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 6,
  },
  badgeText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
});
