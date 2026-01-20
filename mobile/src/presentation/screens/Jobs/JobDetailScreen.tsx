import React, { useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  SafeAreaView,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useJobStore } from '../../stores/jobStore';

export default function JobDetailScreen({ navigation, route }: any) {
  const { t } = useTranslation();
  const { job } = route.params;
  const { currentJob, isLoading, fetchJob, updateJobStatus, deleteJob } = useJobStore();

  useEffect(() => {
    if (job?.id) {
      loadJob();
    }
  }, [job?.id]);

  const loadJob = async () => {
    try {
      await fetchJob(job.id.toString());
    } catch (error) {
      console.error('Error loading job:', error);
    }
  };

  const handleStatusChange = async (newStatus: string) => {
    Alert.alert(
      'Update Status',
      `Change status to "${newStatus.replace('_', ' ')}"?`,
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Confirm',
          onPress: async () => {
            try {
              await updateJobStatus(job.id.toString(), newStatus);
              Alert.alert('Success', 'Job status updated');
            } catch (error: any) {
              Alert.alert('Error', error.message || 'Failed to update status');
            }
          },
        },
      ]
    );
  };

  const handleDelete = () => {
    Alert.alert(
      'Delete Job',
      'Are you sure you want to delete this job? This action cannot be undone.',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Delete',
          style: 'destructive',
          onPress: async () => {
            try {
              await deleteJob(job.id.toString());
              Alert.alert('Success', 'Job deleted', [
                { text: 'OK', onPress: () => navigation.goBack() },
              ]);
            } catch (error: any) {
              Alert.alert('Error', error.message || 'Failed to delete job');
            }
          },
        },
      ]
    );
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
        return '#27ae60';
      case 'in_progress':
        return '#3498db';
      default:
        return '#95a5a6';
    }
  };

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'high':
        return '#e74c3c';
      case 'medium':
        return '#f39c12';
      default:
        return '#95a5a6';
    }
  };

  const displayJob = currentJob || job;

  if (isLoading && !displayJob) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#e67e22" />
        <Text style={styles.loadingText}>{t('common.loading')}</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.backButton}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Job Details</Text>
        <TouchableOpacity onPress={handleDelete}>
          <Text style={styles.deleteButton}>🗑️</Text>
        </TouchableOpacity>
      </View>

      <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
        <View style={styles.card}>
          <View style={styles.titleContainer}>
            <Text style={styles.title}>{displayJob.title}</Text>
            <View style={[styles.priorityBadge, { backgroundColor: getPriorityColor(displayJob.priority) }]}>
              <Text style={styles.badgeText}>{displayJob.priority}</Text>
            </View>
          </View>

          <View style={[styles.statusBadge, { backgroundColor: getStatusColor(displayJob.status) }]}>
            <Text style={styles.badgeText}>{displayJob.status.replace('_', ' ')}</Text>
          </View>
        </View>

        <View style={styles.card}>
          <Text style={styles.sectionTitle}>Description</Text>
          <Text style={styles.description}>{displayJob.description}</Text>
        </View>

        {displayJob.due_date && (
          <View style={styles.card}>
            <Text style={styles.sectionTitle}>Due Date</Text>
            <Text style={styles.dueDate}>📅 {displayJob.due_date}</Text>
          </View>
        )}

        {displayJob.assignee_id && (
          <View style={styles.card}>
            <Text style={styles.sectionTitle}>Assigned To</Text>
            <Text style={styles.info}>User ID: {displayJob.assignee_id}</Text>
          </View>
        )}

        {displayJob.field_id && (
          <View style={styles.card}>
            <Text style={styles.sectionTitle}>Related Field</Text>
            <Text style={styles.info}>Field ID: {displayJob.field_id}</Text>
          </View>
        )}

        <View style={styles.card}>
          <Text style={styles.sectionTitle}>Update Status</Text>
          <View style={styles.statusOptions}>
            <TouchableOpacity
              style={[styles.statusButton, { backgroundColor: '#95a5a6' }]}
              onPress={() => handleStatusChange('pending')}
            >
              <Text style={styles.statusButtonText}>Pending</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.statusButton, { backgroundColor: '#3498db' }]}
              onPress={() => handleStatusChange('in_progress')}
            >
              <Text style={styles.statusButtonText}>In Progress</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={[styles.statusButton, { backgroundColor: '#27ae60' }]}
              onPress={() => handleStatusChange('completed')}
            >
              <Text style={styles.statusButtonText}>Completed</Text>
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f5f5f5',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#7f8c8d',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#fff',
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  backButton: {
    fontSize: 16,
    color: '#e67e22',
    fontWeight: '600',
  },
  deleteButton: {
    fontSize: 24,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  content: {
    flex: 1,
  },
  contentContainer: {
    padding: 15,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 15,
    marginBottom: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  titleContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 10,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#2c3e50',
    flex: 1,
    marginRight: 10,
  },
  priorityBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
    alignSelf: 'flex-start',
  },
  badgeText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
    textTransform: 'uppercase',
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 10,
  },
  description: {
    fontSize: 16,
    color: '#34495e',
    lineHeight: 24,
  },
  dueDate: {
    fontSize: 16,
    color: '#34495e',
  },
  info: {
    fontSize: 16,
    color: '#34495e',
  },
  statusOptions: {
    gap: 10,
  },
  statusButton: {
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  statusButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
});
