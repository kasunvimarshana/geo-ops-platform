import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { useTranslation } from 'react-i18next';
import { useOfflineSync } from '../hooks/useOfflineSync';
import { colors } from '../../theme/colors';
import { typography } from '../../theme/typography';
import { spacing } from '../../theme/spacing';

export const SyncStatusBar: React.FC = () => {
  const { t } = useTranslation();
  const { isConnected, syncStatus, pendingCount, lastSyncTime } = useOfflineSync();

  if (isConnected && pendingCount === 0) {
    return null;
  }

  const getStatusColor = () => {
    if (!isConnected) return colors.offline;
    if (syncStatus === 'syncing') return colors.info;
    if (syncStatus === 'error') return colors.error;
    return colors.success;
  };

  const getStatusText = () => {
    if (!isConnected) return t('sync.offline');
    if (syncStatus === 'syncing') return t('sync.syncing');
    if (pendingCount > 0) return `${pendingCount} ${t('sync.pendingItems')}`;
    return t('sync.synced');
  };

  return (
    <View style={[styles.container, { backgroundColor: getStatusColor() }]}>
      <Text style={styles.text}>{getStatusText()}</Text>
      {lastSyncTime && isConnected && (
        <Text style={styles.subText}>
          {t('sync.lastSync')}: {lastSyncTime.toLocaleTimeString()}
        </Text>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    paddingVertical: spacing.xs,
    paddingHorizontal: spacing.md,
    alignItems: 'center',
    justifyContent: 'center',
  },
  text: {
    ...typography.caption,
    color: colors.text.white,
    fontWeight: '600',
  },
  subText: {
    ...typography.caption,
    color: colors.text.white,
    fontSize: 10,
  },
});
