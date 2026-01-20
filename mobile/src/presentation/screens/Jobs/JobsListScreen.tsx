import React, { useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  SafeAreaView,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useJobStore } from '../../stores/jobStore';
import { Job } from '../../../domain/entities/Job';

export default function JobsListScreen({ navigation }: any) {
  const { t } = useTranslation();
  const { jobs, isLoading, error, fetchJobs } = useJobStore();

  useEffect(() => {
    loadJobs();
  }, []);

  const loadJobs = async () => {
    try {
      await fetchJobs();
    } catch (error) {
      console.error('Error loading jobs:', error);
    }
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

  const renderJobItem = ({ item }: { item: Job }) => (
    <TouchableOpacity
      style={styles.jobItem}
      onPress={() => navigation.navigate('JobDetail', { job: item })}
    >
      <View style={styles.jobHeader}>
        <Text style={styles.jobTitle}>{item.title}</Text>
        <View style={[styles.priorityBadge, { backgroundColor: getPriorityColor(item.priority) }]}>
          <Text style={styles.priorityText}>{item.priority}</Text>
        </View>
      </View>
      
      <Text style={styles.jobDescription} numberOfLines={2}>
        {item.description}
      </Text>

      <View style={styles.jobFooter}>
        <View style={[styles.statusBadge, { backgroundColor: getStatusColor(item.status) }]}>
          <Text style={styles.statusText}>{item.status.replace('_', ' ')}</Text>
        </View>
        {item.due_date && (
          <Text style={styles.dueDate}>Due: {item.due_date}</Text>
        )}
      </View>
    </TouchableOpacity>
  );

  if (isLoading && jobs.length === 0) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#e67e22" />
        <Text style={styles.loadingText}>{t('common.loading')}</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>{t('jobs.title')}</Text>
        <TouchableOpacity
          style={styles.addButton}
          onPress={() => navigation.navigate('CreateJob')}
        >
          <Text style={styles.addButtonText}>+ Add Job</Text>
        </TouchableOpacity>
      </View>

      {error && (
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>{error}</Text>
        </View>
      )}

      <FlatList
        data={jobs}
        renderItem={renderJobItem}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl
            refreshing={isLoading}
            onRefresh={loadJobs}
            colors={['#e67e22']}
          />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyIcon}>📋</Text>
            <Text style={styles.emptyText}>No jobs yet</Text>
            <Text style={styles.emptySubtext}>
              Start by adding your first job
            </Text>
          </View>
        }
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  centerContainer: {
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
  errorContainer: {
    backgroundColor: '#fee',
    padding: 10,
    margin: 15,
    borderRadius: 8,
  },
  errorText: {
    color: '#e74c3c',
    textAlign: 'center',
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
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  addButton: {
    backgroundColor: '#e67e22',
    paddingHorizontal: 15,
    paddingVertical: 8,
    borderRadius: 8,
  },
  addButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 14,
  },
  listContent: {
    padding: 15,
  },
  jobItem: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 12,
    marginBottom: 15,
    borderLeftWidth: 4,
    borderLeftColor: '#e67e22',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  jobHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  jobTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    flex: 1,
    marginRight: 10,
  },
  priorityBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  priorityText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
    textTransform: 'uppercase',
  },
  jobDescription: {
    fontSize: 14,
    color: '#7f8c8d',
    marginBottom: 10,
  },
  jobFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  statusText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
  dueDate: {
    fontSize: 12,
    color: '#95a5a6',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyIcon: {
    fontSize: 60,
    marginBottom: 15,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#7f8c8d',
    marginBottom: 5,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#95a5a6',
  },
});
