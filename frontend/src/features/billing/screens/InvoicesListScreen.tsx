import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  SafeAreaView,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { useRouter } from 'expo-router';
import { invoiceApi } from '../../../services/api/invoices';

interface Invoice {
  id: number;
  invoice_number: string;
  job_id: number;
  customer_id: number;
  amount: number;
  tax_amount: number;
  total_amount: number;
  status: 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled';
  due_date: string;
  issued_at: string;
  paid_at?: string;
  customer?: {
    name: string;
    phone?: string;
  };
  created_at: string;
}

export default function InvoicesListScreen() {
  const router = useRouter();
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchInvoices();
  }, []);

  const fetchInvoices = async () => {
    try {
      setError(null);
      const response = await invoiceApi.getAll();
      setInvoices(response.data || []);
    } catch (err: any) {
      console.error('Failed to fetch invoices:', err);
      setError(err.response?.data?.message || 'Failed to load invoices');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    fetchInvoices();
  };

  const getStatusColor = (status: string): string => {
    const colors: Record<string, string> = {
      draft: '#9e9e9e',
      sent: '#2196f3',
      paid: '#4caf50',
      overdue: '#f44336',
      cancelled: '#757575',
    };
    return colors[status] || '#9e9e9e';
  };

  const getStatusLabel = (status: string): string => {
    return status.charAt(0).toUpperCase() + status.slice(1);
  };

  const handleViewInvoice = (invoice: Invoice) => {
    router.push(`/invoices/${invoice.id}` as any);
  };

  const handleGeneratePDF = async (invoice: Invoice) => {
    try {
      setLoading(true);
      await invoiceApi.generatePdf(invoice.id);
      Alert.alert('Success', 'Invoice PDF generated successfully');
    } catch (err: any) {
      Alert.alert('Error', err.response?.data?.message || 'Failed to generate PDF');
    } finally {
      setLoading(false);
    }
  };

  const renderInvoiceItem = ({ item }: { item: Invoice }) => {
    const isOverdue = item.status !== 'paid' && new Date(item.due_date) < new Date();
    const displayStatus = isOverdue ? 'overdue' : item.status;

    return (
      <TouchableOpacity
        style={styles.invoiceCard}
        onPress={() => handleViewInvoice(item)}
      >
        <View style={styles.cardHeader}>
          <View style={styles.invoiceInfo}>
            <Text style={styles.invoiceNumber}>{item.invoice_number}</Text>
            {item.customer && (
              <Text style={styles.customerName}>{item.customer.name}</Text>
            )}
          </View>
          <View style={[styles.statusBadge, { backgroundColor: getStatusColor(displayStatus) }]}>
            <Text style={styles.statusText}>{getStatusLabel(displayStatus)}</Text>
          </View>
        </View>

        <View style={styles.cardContent}>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Amount:</Text>
            <Text style={styles.amountValue}>LKR {item.total_amount.toLocaleString()}</Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Issued:</Text>
            <Text style={styles.infoValue}>
              {new Date(item.issued_at).toLocaleDateString()}
            </Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Due Date:</Text>
            <Text style={[styles.infoValue, isOverdue && styles.overdueText]}>
              {new Date(item.due_date).toLocaleDateString()}
            </Text>
          </View>

          {item.paid_at && (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Paid:</Text>
              <Text style={styles.paidValue}>
                {new Date(item.paid_at).toLocaleDateString()}
              </Text>
            </View>
          )}
        </View>

        <View style={styles.actionButtons}>
          <TouchableOpacity
            style={styles.viewButton}
            onPress={() => handleViewInvoice(item)}
          >
            <Text style={styles.viewButtonText}>View Details</Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={styles.pdfButton}
            onPress={() => handleGeneratePDF(item)}
          >
            <Text style={styles.pdfButtonText}>📄 PDF</Text>
          </TouchableOpacity>
        </View>
      </TouchableOpacity>
    );
  };

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#2196f3" />
        <Text style={styles.loadingText}>Loading invoices...</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Invoices</Text>
        <Text style={styles.subtitle}>
          {invoices.length} total • {invoices.filter(i => i.status === 'paid').length} paid
        </Text>
      </View>

      {error && (
        <View style={styles.errorBanner}>
          <Text style={styles.errorBannerText}>⚠️ {error}</Text>
        </View>
      )}

      {invoices.length === 0 ? (
        <View style={styles.emptyContainer}>
          <Text style={styles.emptyText}>No invoices yet</Text>
          <Text style={styles.emptySubtext}>
            Invoices will appear here when jobs are completed
          </Text>
        </View>
      ) : (
        <FlatList
          data={invoices}
          renderItem={renderInvoiceItem}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.list}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
          }
        />
      )}
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
    marginTop: 12,
    fontSize: 14,
    color: '#666',
  },
  header: {
    backgroundColor: '#fff',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#ddd',
  },
  title: {
    fontSize: 24,
    fontWeight: '700',
    marginBottom: 4,
  },
  subtitle: {
    fontSize: 14,
    color: '#666',
  },
  errorBanner: {
    backgroundColor: '#ffebee',
    padding: 12,
    margin: 16,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#c62828',
  },
  errorBannerText: {
    color: '#c62828',
    fontSize: 13,
  },
  list: {
    padding: 16,
  },
  invoiceCard: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  invoiceInfo: {
    flex: 1,
  },
  invoiceNumber: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
    marginBottom: 4,
  },
  customerName: {
    fontSize: 14,
    color: '#666',
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  statusText: {
    color: '#fff',
    fontSize: 11,
    fontWeight: '600',
  },
  cardContent: {
    marginBottom: 12,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 6,
  },
  infoLabel: {
    fontSize: 14,
    color: '#666',
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
  },
  amountValue: {
    fontSize: 16,
    fontWeight: '700',
    color: '#2196f3',
  },
  paidValue: {
    fontSize: 14,
    fontWeight: '600',
    color: '#4caf50',
  },
  overdueText: {
    color: '#f44336',
  },
  actionButtons: {
    flexDirection: 'row',
    gap: 8,
  },
  viewButton: {
    flex: 1,
    backgroundColor: '#2196f3',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  viewButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
  pdfButton: {
    backgroundColor: '#ff9800',
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  pdfButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 32,
  },
  emptyText: {
    fontSize: 20,
    fontWeight: '600',
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#666',
    textAlign: 'center',
  },
});
