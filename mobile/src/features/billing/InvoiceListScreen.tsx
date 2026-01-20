import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Header, List, Card } from '@/components';
import { Invoice } from '@/types';
import { apiClient } from '@/services/api/client';
import { format } from 'date-fns';

interface InvoiceListScreenProps {
  navigation: any;
}

export const InvoiceListScreen: React.FC<InvoiceListScreenProps> = ({
  navigation,
}) => {
  const { t } = useTranslation();
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    fetchInvoices();
  }, []);

  const fetchInvoices = async () => {
    try {
      setLoading(true);
      const response = await apiClient.get('/invoices');
      if (response.success && response.data) {
        setInvoices(response.data);
      }
    } catch (error) {
      console.error('Error fetching invoices:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const renderInvoiceItem = (invoice: Invoice) => {
    const statusColor = {
      draft: '#9E9E9E',
      sent: '#2196F3',
      paid: '#4CAF50',
      overdue: '#F44336',
      cancelled: '#757575',
    }[invoice.status];

    return (
      <Card
        onPress={() =>
          navigation.navigate('InvoiceDetail', { invoiceId: invoice.id })
        }
      >
        <View style={styles.invoiceHeader}>
          <Text style={styles.invoiceNumber}>{invoice.invoice_number}</Text>
          <View style={[styles.statusBadge, { backgroundColor: statusColor }]}>
            <Text style={styles.statusText}>
              {t(`billing.status_${invoice.status}`)}
            </Text>
          </View>
        </View>

        <View style={styles.invoiceInfo}>
          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('billing.customer')}:</Text>
            <Text style={styles.infoValue}>{invoice.customer_name}</Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('billing.date')}:</Text>
            <Text style={styles.infoValue}>
              {format(new Date(invoice.invoice_date), 'MMM dd, yyyy')}
            </Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>{t('billing.total')}:</Text>
            <Text style={styles.amountValue}>
              Rs. {invoice.total_amount.toFixed(2)}
            </Text>
          </View>

          {invoice.balance > 0 && (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>{t('billing.balance')}:</Text>
              <Text style={[styles.amountValue, styles.balanceValue]}>
                Rs. {invoice.balance.toFixed(2)}
              </Text>
            </View>
          )}
        </View>

        {invoice.sync_status === 'pending' && (
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
        title={t('billing.title')}
        rightAction={{
          label: '+',
          onPress: () => navigation.navigate('CreateInvoice'),
        }}
      />

      <List
        data={invoices}
        renderItem={renderInvoiceItem}
        keyExtractor={(item) => item.id.toString()}
        loading={loading}
        refreshing={refreshing}
        onRefresh={fetchInvoices}
        emptyMessage={t('billing.no_invoices')}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  invoiceHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  invoiceNumber: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
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
  invoiceInfo: {
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
  amountValue: {
    fontSize: 16,
    fontWeight: '700',
    color: '#2196F3',
  },
  balanceValue: {
    color: '#F44336',
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
