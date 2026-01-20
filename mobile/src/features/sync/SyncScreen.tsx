import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Header, Card, Button } from '@/components';
import { useSyncStore } from '@/stores/syncStore';
import { format } from 'date-fns';

interface SyncScreenProps {
  navigation: any;
}

export const SyncScreen: React.FC<SyncScreenProps> = ({ navigation }) => {
  const { t } = useTranslation();
  const {
    isSyncing,
    lastSyncTime,
    pendingItemsCount,
    syncErrors,
  } = useSyncStore();

  const handleSync = () => {
    // Sync will be triggered by the sync service
    // This is just a placeholder to show the UI
  };

  return (
    <View style={styles.container}>
      <Header
        title={t('sync.title')}
        onBack={() => navigation.goBack()}
      />

      <ScrollView style={styles.content}>
        <Card>
          <Text style={styles.sectionTitle}>{t('sync.status')}</Text>
          
          <View style={styles.statusRow}>
            <Text style={styles.label}>{t('sync.last_sync')}:</Text>
            <Text style={styles.value}>
              {lastSyncTime
                ? format(lastSyncTime, 'MMM dd, yyyy hh:mm a')
                : t('sync.never')}
            </Text>
          </View>

          <View style={styles.statusRow}>
            <Text style={styles.label}>{t('sync.pending_items')}:</Text>
            <Text style={[styles.value, styles.pendingValue]}>
              {pendingItemsCount}
            </Text>
          </View>

          <Button
            title={
              isSyncing ? t('sync.syncing') : t('sync.sync_now')
            }
            onPress={handleSync}
            loading={isSyncing}
            disabled={pendingItemsCount === 0}
            style={styles.syncButton}
          />
        </Card>

        {syncErrors.length > 0 && (
          <Card>
            <Text style={styles.sectionTitle}>{t('sync.errors')}</Text>
            
            {syncErrors.map((error, index) => (
              <View key={index} style={styles.errorItem}>
                <View style={styles.errorHeader}>
                  <Text style={styles.errorType}>{error.entityType}</Text>
                  <Text style={styles.errorTime}>
                    {format(error.timestamp, 'hh:mm a')}
                  </Text>
                </View>
                <Text style={styles.errorMessage}>{error.error}</Text>
              </View>
            ))}
          </Card>
        )}

        <Card>
          <Text style={styles.sectionTitle}>{t('sync.info')}</Text>
          <Text style={styles.infoText}>
            {t('sync.info_description')}
          </Text>
          <Text style={styles.infoText}>
            • {t('sync.info_automatic')}
          </Text>
          <Text style={styles.infoText}>
            • {t('sync.info_manual')}
          </Text>
          <Text style={styles.infoText}>
            • {t('sync.info_conflicts')}
          </Text>
        </Card>
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  content: {
    flex: 1,
    padding: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
    marginBottom: 16,
  },
  statusRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  label: {
    fontSize: 14,
    color: '#666',
  },
  value: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
  },
  pendingValue: {
    color: '#FF9800',
  },
  syncButton: {
    marginTop: 16,
  },
  errorItem: {
    backgroundColor: '#FFF3E0',
    padding: 12,
    borderRadius: 8,
    marginBottom: 8,
  },
  errorHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  errorType: {
    fontSize: 14,
    fontWeight: '600',
    color: '#F57C00',
  },
  errorTime: {
    fontSize: 12,
    color: '#666',
  },
  errorMessage: {
    fontSize: 13,
    color: '#666',
  },
  infoText: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
    lineHeight: 20,
  },
});
