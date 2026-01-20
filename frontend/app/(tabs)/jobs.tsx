import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, ActivityIndicator, RefreshControl, Alert } from 'react-native';
import jobApi, { Job } from '../../src/services/api/jobs';
import { usePrinter } from '../../src/hooks/usePrinter';
import { JobSummaryPrintData } from '../../src/services/printerService';

export default function JobsScreen() {
  const [jobs, setJobs] = useState<Job[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  
  const { printJobSummary, isConnected, status } = usePrinter();

  const fetchJobs = async () => {
    try {
      setError(null);
      const response = await jobApi.getAll();
      setJobs(response.data || []);
    } catch (err: any) {
      console.error('Failed to fetch jobs:', err);
      setError(err.response?.data?.message || 'Failed to load jobs');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchJobs();
  }, []);

  const onRefresh = () => {
    setRefreshing(true);
    fetchJobs();
  };

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      pending: '#ff9800',
      assigned: '#2196f3',
      in_progress: '#9c27b0',
      completed: '#4caf50',
      billed: '#00bcd4',
      paid: '#2e7d32',
    };
    return colors[status] || '#757575';
  };

  const getStatusLabel = (status: string) => {
    return status.replace('_', ' ').toUpperCase();
  };

  const handleUpdateStatus = async (job: Job) => {
    const statusFlow = ['pending', 'assigned', 'in_progress', 'completed', 'billed', 'paid'];
    const currentIndex = statusFlow.indexOf(job.status);
    if (currentIndex < statusFlow.length - 1) {
      const nextStatus = statusFlow[currentIndex + 1];
      try {
        await jobApi.updateStatus(job.id, { status: nextStatus });
        fetchJobs();
        Alert.alert('Success', `Job status updated to ${getStatusLabel(nextStatus)}`);
      } catch (err: any) {
        Alert.alert('Error', err.response?.data?.message || 'Failed to update job status');
      }
    }
  };

  const handlePrintJobSummary = async (job: Job) => {
    if (!isConnected) {
      Alert.alert(
        'Printer Not Connected',
        'Would you like to connect to a printer or generate a PDF instead?',
        [
          { text: 'Cancel', style: 'cancel' },
          { text: 'Connect Printer', onPress: () => {
            // Navigate to printer settings
            Alert.alert('Info', 'Please go to Profile > Printer Settings to connect a printer');
          }},
          { text: 'Generate PDF', onPress: () => {
            Alert.alert('PDF Generation', 'PDF generation feature coming soon');
          }},
        ]
      );
      return;
    }

    try {
      const printData: JobSummaryPrintData = {
        jobNumber: `JOB-${job.id}`,
        customerName: job.customer?.name || 'N/A',
        driverName: job.driver?.name || 'N/A',
        machineName: job.machine?.name || 'N/A',
        date: job.scheduled_at ? new Date(job.scheduled_at).toLocaleDateString() : new Date().toLocaleDateString(),
        location: job.location || 'N/A',
        area: job.measured_area || 0,
        areaUnit: 'acres',
        serviceType: job.service_type || 'Field Service',
        status: getStatusLabel(job.status),
        organizationName: 'GeoOps Services',
      };

      await printJobSummary(printData);
      Alert.alert('Success', 'Job summary print queued successfully');
    } catch (err: any) {
      Alert.alert('Error', err.message || 'Failed to print job summary');
    }
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#2e7d32" />
        <Text style={styles.loadingText}>Loading jobs...</Text>
      </View>
    );
  }

  if (!loading && jobs.length === 0) {
    return (
      <ScrollView 
        style={styles.container}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#2e7d32']} />
        }
      >
        <View style={styles.emptyState}>
          <Text style={styles.emptyIcon}>📋</Text>
          <Text style={styles.emptyTitle}>No Jobs Yet</Text>
          <Text style={styles.emptyText}>
            Create jobs to assign field work to drivers and machines.
          </Text>
          {error && <Text style={styles.errorText}>{error}</Text>}
        </View>
      </ScrollView>
    );
  }

  return (
    <ScrollView 
      style={styles.container}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={['#2e7d32']} />
      }
    >
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Jobs</Text>
        <Text style={styles.headerSubtitle}>{jobs.length} total</Text>
      </View>

      {error && (
        <View style={styles.errorBanner}>
          <Text style={styles.errorBannerText}>⚠️ {error}</Text>
        </View>
      )}

      {jobs.map((job) => (
        <View key={job.id} style={styles.jobCard}>
          <View style={styles.cardHeader}>
            <View style={styles.cardTitleContainer}>
              <Text style={styles.cardIcon}>📋</Text>
              <View style={styles.titleColumn}>
                <Text style={styles.cardTitle}>{job.service_type}</Text>
                <Text style={styles.cardSubtitle}>Job #{job.id}</Text>
              </View>
            </View>
            <View style={[styles.statusBadge, { backgroundColor: getStatusColor(job.status) }]}>
              <Text style={styles.statusText}>{getStatusLabel(job.status)}</Text>
            </View>
          </View>

          <View style={styles.cardDetails}>
            {job.customer && (
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Customer:</Text>
                <Text style={styles.detailValue}>{job.customer.name}</Text>
              </View>
            )}
            {job.driver && (
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Driver:</Text>
                <Text style={styles.detailValue}>{job.driver.name}</Text>
              </View>
            )}
            {job.machine && (
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Machine:</Text>
                <Text style={styles.detailValue}>{job.machine.name}</Text>
              </View>
            )}
            {job.scheduled_at && (
              <View style={styles.detailRow}>
                <Text style={styles.detailLabel}>Scheduled:</Text>
                <Text style={styles.detailValue}>
                  {new Date(job.scheduled_at).toLocaleDateString()}
                </Text>
              </View>
            )}
          </View>

          <View style={styles.actionButtons}>
            {job.status !== 'paid' && (
              <TouchableOpacity 
                style={[styles.actionButton, styles.primaryButton, { backgroundColor: getStatusColor(job.status) }]}
                onPress={() => handleUpdateStatus(job)}
              >
                <Text style={styles.actionButtonText}>Update Status</Text>
              </TouchableOpacity>
            )}
            
            {job.status === 'completed' && (
              <TouchableOpacity 
                style={[styles.actionButton, styles.secondaryButton]}
                onPress={() => handlePrintJobSummary(job)}
              >
                <Text style={styles.actionButtonText}>🖨️ Print Summary</Text>
              </TouchableOpacity>
            )}
          </View>
          
          {isConnected && job.status === 'completed' && (
            <View style={styles.printerStatus}>
              <View style={styles.printerDot} />
              <Text style={styles.printerStatusText}>
                Printer ready: {status.deviceName}
              </Text>
            </View>
          )}
        </View>
      ))}
    </ScrollView>
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
    marginTop: 12,
    fontSize: 14,
    color: '#666',
  },
  header: {
    backgroundColor: '#2e7d32',
    padding: 20,
    paddingBottom: 24,
  },
  headerTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 4,
  },
  headerSubtitle: {
    fontSize: 14,
    color: '#a5d6a7',
  },
  errorBanner: {
    backgroundColor: '#ffebee',
    padding: 12,
    margin: 16,
    marginBottom: 8,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#c62828',
  },
  errorBannerText: {
    color: '#c62828',
    fontSize: 13,
  },
  jobCard: {
    backgroundColor: '#fff',
    margin: 16,
    marginTop: 8,
    marginBottom: 8,
    borderRadius: 12,
    padding: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  cardTitleContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  cardIcon: {
    fontSize: 24,
    marginRight: 8,
  },
  titleColumn: {
    flex: 1,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
  },
  cardSubtitle: {
    fontSize: 12,
    color: '#999',
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  statusText: {
    color: '#fff',
    fontSize: 11,
    fontWeight: 'bold',
  },
  cardDetails: {
    marginBottom: 16,
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 6,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  detailLabel: {
    fontSize: 14,
    color: '#666',
    fontWeight: '500',
  },
  detailValue: {
    fontSize: 14,
    color: '#333',
    fontWeight: '600',
  },
  actionButtons: {
    flexDirection: 'row',
    gap: 8,
  },
  actionButton: {
    flex: 1,
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  primaryButton: {
    // backgroundColor set dynamically
  },
  secondaryButton: {
    backgroundColor: '#2196f3',
  },
  actionButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
  printerStatus: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 8,
    paddingTop: 8,
    borderTopWidth: 1,
    borderTopColor: '#f0f0f0',
  },
  printerDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#4caf50',
    marginRight: 6,
  },
  printerStatusText: {
    fontSize: 12,
    color: '#666',
  },
  emptyState: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 40,
    marginTop: 100,
  },
  emptyIcon: {
    fontSize: 64,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 14,
    color: '#666',
    textAlign: 'center',
    lineHeight: 20,
  },
  errorText: {
    fontSize: 12,
    color: '#c62828',
    marginTop: 8,
    textAlign: 'center',
  },
});
