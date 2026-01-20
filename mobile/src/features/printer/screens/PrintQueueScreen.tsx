/**
 * Print Queue Screen
 * Display and manage print queue with pending, completed, and failed jobs
 */

import React, { useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  Alert,
} from 'react-native';
import { Button, Card, Badge, IconButton } from 'react-native-paper';
import { usePrinterStore } from '../../../store/printerStore';
import { useTranslation } from 'react-i18next';
import { format } from 'date-fns';
import type { PrintJob } from '../../../shared/services/printer';

export const PrintQueueScreen: React.FC = () => {
  const { t } = useTranslation();
  const {
    printJobs,
    queueStats,
    isPrinting,
    isConnected,
    loadPrintQueue,
    loadQueueStats,
    processQueue,
    retryJob,
    deleteJob,
    clearCompletedJobs,
  } = usePrinterStore();

  const [refreshing, setRefreshing] = React.useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    await loadPrintQueue();
    await loadQueueStats();
  };

  const handleRefresh = async () => {
    setRefreshing(true);
    await loadData();
    setRefreshing(false);
  };

  const handleProcessQueue = async () => {
    if (!isConnected) {
      Alert.alert(
        t('printer.error'),
        t('printer.notConnected'),
        [
          {
            text: t('common.cancel'),
            style: 'cancel',
          },
          {
            text: t('printer.goToSettings'),
            onPress: () => {
              // Navigate to printer settings
            },
          },
        ]
      );
      return;
    }

    try {
      await processQueue();
      Alert.alert(t('printer.success'), t('printer.queueProcessed'));
    } catch (err) {
      Alert.alert(t('printer.error'), t('printer.queueProcessFailed'));
    }
  };

  const handleRetry = async (jobId: string) => {
    try {
      await retryJob(jobId);
      Alert.alert(t('printer.success'), t('printer.jobRetried'));
    } catch (err) {
      Alert.alert(t('printer.error'), t('printer.retryFailed'));
    }
  };

  const handleDelete = (jobId: string) => {
    Alert.alert(
      t('printer.confirmDelete'),
      t('printer.confirmDeleteMessage'),
      [
        {
          text: t('common.cancel'),
          style: 'cancel',
        },
        {
          text: t('common.delete'),
          style: 'destructive',
          onPress: async () => {
            try {
              await deleteJob(jobId);
            } catch (err) {
              Alert.alert(t('printer.error'), t('printer.deleteFailed'));
            }
          },
        },
      ]
    );
  };

  const handleClearCompleted = () => {
    Alert.alert(
      t('printer.clearCompleted'),
      t('printer.clearCompletedMessage'),
      [
        {
          text: t('common.cancel'),
          style: 'cancel',
        },
        {
          text: t('common.clear'),
          style: 'destructive',
          onPress: async () => {
            try {
              await clearCompletedJobs();
              Alert.alert(t('printer.success'), t('printer.completedCleared'));
            } catch (err) {
              Alert.alert(t('printer.error'), t('printer.clearFailed'));
            }
          },
        },
      ]
    );
  };

  const getStatusColor = (status: PrintJob['status']): string => {
    switch (status) {
      case 'pending':
        return '#ff9800';
      case 'printing':
        return '#2196f3';
      case 'completed':
        return '#4caf50';
      case 'failed':
        return '#f44336';
      default:
        return '#9e9e9e';
    }
  };

  const getJobTypeLabel = (type: PrintJob['type']): string => {
    switch (type) {
      case 'invoice':
        return t('printer.invoice');
      case 'receipt':
        return t('printer.receipt');
      case 'job_summary':
        return t('printer.jobSummary');
      default:
        return type;
    }
  };

  const renderJob = ({ item }: { item: PrintJob }) => {
    const statusColor = getStatusColor(item.status);

    return (
      <Card style={styles.jobCard}>
        <Card.Content>
          <View style={styles.jobHeader}>
            <View style={styles.jobInfo}>
              <View style={styles.jobTitleRow}>
                <Text style={styles.jobType}>{getJobTypeLabel(item.type)}</Text>
                <Badge
                  style={[styles.statusBadge, { backgroundColor: statusColor }]}
                >
                  {t(`printer.status.${item.status}`)}
                </Badge>
              </View>
              <Text style={styles.jobDate}>
                {format(item.createdAt, 'MMM dd, yyyy HH:mm')}
              </Text>
              {item.attempts > 0 && (
                <Text style={styles.jobAttempts}>
                  {t('printer.attempts')}: {item.attempts}
                </Text>
              )}
              {item.error && (
                <Text style={styles.jobError}>{item.error}</Text>
              )}
            </View>
            <View style={styles.jobActions}>
              {item.status === 'failed' && (
                <IconButton
                  icon="refresh"
                  size={20}
                  onPress={() => handleRetry(item.id)}
                />
              )}
              {item.status !== 'printing' && (
                <IconButton
                  icon="delete"
                  size={20}
                  onPress={() => handleDelete(item.id)}
                />
              )}
            </View>
          </View>
        </Card.Content>
      </Card>
    );
  };

  return (
    <View style={styles.container}>
      {/* Stats Card */}
      <Card style={styles.statsCard}>
        <Card.Content>
          <Text style={styles.statsTitle}>{t('printer.queueStats')}</Text>
          <View style={styles.statsRow}>
            <View style={styles.statItem}>
              <Text style={styles.statValue}>{queueStats.total}</Text>
              <Text style={styles.statLabel}>{t('printer.total')}</Text>
            </View>
            <View style={styles.statItem}>
              <Text style={[styles.statValue, { color: '#ff9800' }]}>
                {queueStats.pending}
              </Text>
              <Text style={styles.statLabel}>{t('printer.pending')}</Text>
            </View>
            <View style={styles.statItem}>
              <Text style={[styles.statValue, { color: '#4caf50' }]}>
                {queueStats.completed}
              </Text>
              <Text style={styles.statLabel}>{t('printer.completed')}</Text>
            </View>
            <View style={styles.statItem}>
              <Text style={[styles.statValue, { color: '#f44336' }]}>
                {queueStats.failed}
              </Text>
              <Text style={styles.statLabel}>{t('printer.failed')}</Text>
            </View>
          </View>
        </Card.Content>
      </Card>

      {/* Action Buttons */}
      <View style={styles.actionsSection}>
        <Button
          mode="contained"
          onPress={handleProcessQueue}
          loading={isPrinting}
          disabled={isPrinting || queueStats.pending === 0}
          style={styles.actionButton}
          icon="printer"
        >
          {t('printer.processQueue')} ({queueStats.pending})
        </Button>
        {queueStats.completed > 0 && (
          <Button
            mode="outlined"
            onPress={handleClearCompleted}
            style={styles.actionButton}
            icon="delete-sweep"
          >
            {t('printer.clearCompleted')}
          </Button>
        )}
      </View>

      {/* Jobs List */}
      <FlatList
        data={printJobs}
        renderItem={renderJob}
        keyExtractor={(item) => item.id}
        contentContainerStyle={styles.jobsList}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>{t('printer.noJobs')}</Text>
          </View>
        }
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  statsCard: {
    margin: 16,
  },
  statsTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 12,
    color: '#333',
  },
  statsRow: {
    flexDirection: 'row',
    justifyContent: 'space-around',
  },
  statItem: {
    alignItems: 'center',
  },
  statValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 4,
  },
  statLabel: {
    fontSize: 12,
    color: '#666',
  },
  actionsSection: {
    padding: 16,
    paddingTop: 0,
    gap: 8,
  },
  actionButton: {
    marginBottom: 8,
  },
  jobsList: {
    padding: 16,
    paddingTop: 0,
  },
  jobCard: {
    marginBottom: 12,
  },
  jobHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  jobInfo: {
    flex: 1,
  },
  jobTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  jobType: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
    marginRight: 8,
  },
  statusBadge: {
    paddingHorizontal: 8,
    height: 20,
  },
  jobDate: {
    fontSize: 12,
    color: '#666',
    marginBottom: 2,
  },
  jobAttempts: {
    fontSize: 12,
    color: '#ff9800',
    marginTop: 4,
  },
  jobError: {
    fontSize: 12,
    color: '#f44336',
    marginTop: 4,
  },
  jobActions: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: 32,
  },
  emptyText: {
    fontSize: 16,
    color: '#666',
  },
});
