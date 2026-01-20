import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, ActivityIndicator, RefreshControl } from 'react-native';
import { useAuthStore } from '../../src/store/authStore';
import reportApi from '../../src/services/api/reports';
import { useRouter } from 'expo-router';

interface DashboardData {
  total_measurements: number;
  total_jobs: number;
  active_jobs: number;
  completed_jobs: number;
  total_income: number;
  total_expenses: number;
  pending_invoices: number;
  [key: string]: any;
}

export default function DashboardScreen() {
  const { user } = useAuthStore();
  const router = useRouter();
  const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchDashboard = async () => {
    try {
      setError(null);
      const response = await reportApi.getDashboard();
      setDashboardData(response.data);
    } catch (err: any) {
      console.error('Failed to fetch dashboard:', err);
      setError(err.response?.data?.message || 'Failed to load dashboard data');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchDashboard();
  }, []);

  const onRefresh = () => {
    setRefreshing(true);
    fetchDashboard();
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#2e7d32" />
        <Text style={styles.loadingText}>Loading dashboard...</Text>
      </View>
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
        <Text style={styles.welcome}>Welcome, {user?.name}!</Text>
        <Text style={styles.role}>{user?.role?.toUpperCase()}</Text>
      </View>

      {error && (
        <View style={styles.errorBanner}>
          <Text style={styles.errorText}>⚠️ {error}</Text>
          <TouchableOpacity onPress={fetchDashboard}>
            <Text style={styles.retryText}>Retry</Text>
          </TouchableOpacity>
        </View>
      )}

      <View style={styles.statsContainer}>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{dashboardData?.total_measurements || 0}</Text>
          <Text style={styles.statLabel}>Measurements</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{dashboardData?.active_jobs || 0}</Text>
          <Text style={styles.statLabel}>Active Jobs</Text>
        </View>
      </View>

      <View style={styles.statsContainer}>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{dashboardData?.completed_jobs || 0}</Text>
          <Text style={styles.statLabel}>Completed</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{dashboardData?.pending_invoices || 0}</Text>
          <Text style={styles.statLabel}>Pending Invoices</Text>
        </View>
      </View>

      <View style={styles.actionsContainer}>
        <TouchableOpacity 
          style={styles.actionButton}
          onPress={() => router.push('/(tabs)/measurements')}
        >
          <Text style={styles.actionButtonText}>📏 New Measurement</Text>
        </TouchableOpacity>
        <TouchableOpacity 
          style={styles.actionButton}
          onPress={() => router.push('/(tabs)/jobs')}
        >
          <Text style={styles.actionButtonText}>📋 View Jobs</Text>
        </TouchableOpacity>
      </View>

      <View style={styles.infoBox}>
        <Text style={styles.infoTitle}>Getting Started</Text>
        <Text style={styles.infoText}>
          1. Go to "Fields" tab to measure land{'\n'}
          2. Create jobs for field work{'\n'}
          3. Track driver progress{'\n'}
          4. Generate invoices and reports
        </Text>
      </View>
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
    paddingBottom: 30,
  },
  welcome: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 4,
  },
  role: {
    fontSize: 14,
    color: '#a5d6a7',
    fontWeight: '600',
  },
  errorBanner: {
    backgroundColor: '#ffebee',
    padding: 16,
    margin: 16,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#c62828',
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  errorText: {
    color: '#c62828',
    fontSize: 14,
    flex: 1,
  },
  retryText: {
    color: '#2e7d32',
    fontWeight: 'bold',
    fontSize: 14,
    marginLeft: 12,
  },
  statsContainer: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    paddingVertical: 8,
    gap: 16,
  },
  statCard: {
    flex: 1,
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  statNumber: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#2e7d32',
    marginBottom: 4,
  },
  statLabel: {
    fontSize: 14,
    color: '#666',
  },
  actionsContainer: {
    padding: 16,
    gap: 12,
  },
  actionButton: {
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    borderLeftWidth: 4,
    borderLeftColor: '#2e7d32',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  actionButtonText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
  },
  infoBox: {
    margin: 16,
    backgroundColor: '#e8f5e9',
    padding: 20,
    borderRadius: 12,
  },
  infoTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#1b5e20',
    marginBottom: 12,
  },
  infoText: {
    fontSize: 14,
    color: '#2e7d32',
    lineHeight: 22,
  },
});
