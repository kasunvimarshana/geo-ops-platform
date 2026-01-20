import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useJobsStore } from '../../../store/jobsStore';
import { useSyncStore } from '../../../store/syncStore';
import { Card } from '../../../shared/components/Card';
import { Button } from '../../../shared/components/Button';
import { LoadingSpinner } from '../../../shared/components/LoadingSpinner';
import { colors } from '../../../theme/colors';
import { typography } from '../../../theme/typography';
import { spacing } from '../../../theme/spacing';
import { FieldJob } from '../../../shared/types/api.types';

interface JobListScreenProps {
  navigation: any;
}

export const JobListScreen: React.FC<JobListScreenProps> = ({ navigation }) => {
  const { t } = useTranslation();
  const { jobs, isLoading, fetchJobs, loadLocalJobs, statusFilter, setStatusFilter } = useJobsStore();
  const { networkStatus } = useSyncStore();
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadData();
  }, [statusFilter]);

  const loadData = async () => {
    if (networkStatus === 'online') {
      await fetchJobs(statusFilter || undefined);
    } else {
      await loadLocalJobs(statusFilter || undefined);
    }
  };

  const handleRefresh = async () => {
    setRefreshing(true);
    await loadData();
    setRefreshing(false);
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'pending':
        return colors.warning;
      case 'in_progress':
        return colors.info;
      case 'completed':
        return colors.success;
      case 'cancelled':
        return colors.error;
      default:
        return colors.text.secondary;
    }
  };

  const renderJobCard = ({ item }: { item: FieldJob }) => (
    <TouchableOpacity
      onPress={() => navigation.navigate('JobDetail', { jobId: item.id })}
    >
      <Card style={styles.jobCard}>
        <View style={styles.jobHeader}>
          <Text style={styles.jobTitle}>{item.title}</Text>
          <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.status) }]}>
            <Text style={styles.statusText}>{t(`jobs.${item.status}`)}</Text>
          </View>
        </View>
        <Text style={styles.jobCustomer}>{item.customer_name}</Text>
        <Text style={styles.jobLocation}>{item.location}</Text>
        {item.scheduled_date && (
          <Text style={styles.jobDate}>
            {new Date(item.scheduled_date).toLocaleDateString()}
          </Text>
        )}
        {!item.synced && (
          <View style={styles.offlineBadge}>
            <Text style={styles.offlineText}>{t('sync.offline')}</Text>
          </View>
        )}
      </Card>
    </TouchableOpacity>
  );

  if (isLoading && !refreshing) {
    return <LoadingSpinner fullScreen message={t('common.loading')} />;
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>{t('jobs.title')}</Text>
        <Button
          title={t('jobs.createJob')}
          onPress={() => navigation.navigate('CreateJob')}
          style={styles.createButton}
        />
      </View>

      <View style={styles.filters}>
        {['all', 'pending', 'in_progress', 'completed'].map((status) => (
          <TouchableOpacity
            key={status}
            style={[
              styles.filterButton,
              statusFilter === (status === 'all' ? null : status) && styles.filterButtonActive,
            ]}
            onPress={() => setStatusFilter(status === 'all' ? null : status)}
          >
            <Text
              style={[
                styles.filterText,
                statusFilter === (status === 'all' ? null : status) && styles.filterTextActive,
              ]}
            >
              {status === 'all' ? 'All' : t(`jobs.${status}`)}
            </Text>
          </TouchableOpacity>
        ))}
      </View>

      <FlatList
        data={jobs}
        renderItem={renderJobCard}
        keyExtractor={(item, index) => item.id?.toString() || item.local_id || index.toString()}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={handleRefresh} />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>{t('jobs.noJobs')}</Text>
          </View>
        }
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.surface,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: spacing.md,
    backgroundColor: colors.background,
  },
  title: {
    ...typography.h2,
    color: colors.text.primary,
  },
  createButton: {
    paddingVertical: spacing.sm,
    paddingHorizontal: spacing.md,
    minHeight: 40,
  },
  filters: {
    flexDirection: 'row',
    padding: spacing.sm,
    backgroundColor: colors.background,
    gap: spacing.sm,
  },
  filterButton: {
    paddingVertical: spacing.xs,
    paddingHorizontal: spacing.md,
    borderRadius: 20,
    backgroundColor: colors.surface,
    borderWidth: 1,
    borderColor: colors.border,
  },
  filterButtonActive: {
    backgroundColor: colors.primary,
    borderColor: colors.primary,
  },
  filterText: {
    ...typography.body2,
    color: colors.text.secondary,
  },
  filterTextActive: {
    color: colors.text.white,
  },
  listContent: {
    padding: spacing.md,
  },
  jobCard: {
    marginBottom: spacing.md,
  },
  jobHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: spacing.sm,
  },
  jobTitle: {
    ...typography.h4,
    color: colors.text.primary,
    flex: 1,
    marginRight: spacing.sm,
  },
  statusBadge: {
    paddingHorizontal: spacing.sm,
    paddingVertical: spacing.xs,
    borderRadius: 12,
  },
  statusText: {
    ...typography.caption,
    color: colors.text.white,
    fontWeight: '600',
  },
  jobCustomer: {
    ...typography.body1,
    color: colors.text.primary,
    marginBottom: spacing.xs,
  },
  jobLocation: {
    ...typography.body2,
    color: colors.text.secondary,
    marginBottom: spacing.xs,
  },
  jobDate: {
    ...typography.body2,
    color: colors.text.secondary,
  },
  offlineBadge: {
    marginTop: spacing.sm,
    paddingVertical: spacing.xs,
    paddingHorizontal: spacing.sm,
    backgroundColor: colors.offline,
    borderRadius: 4,
    alignSelf: 'flex-start',
  },
  offlineText: {
    ...typography.caption,
    color: colors.text.white,
  },
  emptyContainer: {
    padding: spacing.xxl,
    alignItems: 'center',
  },
  emptyText: {
    ...typography.body1,
    color: colors.text.secondary,
  },
});
