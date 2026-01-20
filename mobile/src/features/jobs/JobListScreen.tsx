import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Header, List, Card } from '@/components';
import { Job } from '@/types';
import { apiClient } from '@/services/api/client';
import { format } from 'date-fns';

interface JobListScreenProps {
  navigation: any;
}

export const JobListScreen: React.FC<JobListScreenProps> = ({ navigation }) => {
  const { t } = useTranslation();
  const [jobs, setJobs] = useState<Job[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    fetchJobs();
  }, []);

  const fetchJobs = async () => {
    try {
      setLoading(true);
      const response = await apiClient.get('/jobs');
      if (response.success && response.data) {
        setJobs(response.data);
      }
    } catch (error) {
      console.error('Error fetching jobs:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const renderJobItem = (job: Job) => {
    const statusColor = {
      pending: '#FFC107',
      in_progress: '#2196F3',
      completed: '#4CAF50',
      cancelled: '#F44336',
    }[job.status];

    return (
      <Card onPress={() => navigation.navigate('JobDetail', { jobId: job.id })}>
        <View style={styles.jobHeader}>
          <Text style={styles.jobTitle}>{job.title}</Text>
          <View style={[styles.statusBadge, { backgroundColor: statusColor }]}>
            <Text style={styles.statusText}>
              {t(`jobs.${job.status}`)}
            </Text>
          </View>
        </View>

        <View style={styles.jobInfo}>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('jobs.customer')}:</Text>
            <Text style={styles.infoValue}>{job.customer_name}</Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('jobs.date')}:</Text>
            <Text style={styles.infoValue}>
              {format(new Date(job.job_date), 'MMM dd, yyyy')}
            </Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('jobs.location')}:</Text>
            <Text style={styles.infoValue}>{job.location_name}</Text>
          </View>
        </View>

        {job.sync_status === 'pending' && (
          <View style={styles.syncBadge}>
            <Text style={styles.syncText}>⚠ {t('sync.pending')}</Text>
          </View>
        )}
      </Card>
    );
  };

  return (
    <View style={styles.container}>
      <Header
        title={t('jobs.title')}
        rightAction={{
          label: '+',
          onPress: () => navigation.navigate('CreateJob'),
        }}
      />

      <List
        data={jobs}
        renderItem={renderJobItem}
        keyExtractor={(item) => item.id.toString()}
        loading={loading}
        refreshing={refreshing}
        onRefresh={fetchJobs}
        emptyMessage={t('jobs.no_jobs')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  jobHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  jobTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
    flex: 1,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '600',
    color: '#FFFFFF',
  },
  jobInfo: {
    gap: 8,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
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
  syncBadge: {
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: '#EEE',
  },
  syncText: {
    fontSize: 12,
    color: '#FF9800',
    fontWeight: '500',
  },
});
