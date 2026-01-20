import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ScrollView,
  SafeAreaView,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { paymentsApi } from '../../../services/api/payments';

type PaymentMethod = 'cash' | 'bank_transfer' | 'mobile_money' | 'credit';

export default function RecordPaymentScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const invoiceId = params.invoiceId ? parseInt(params.invoiceId as string, 10) : undefined;
  const invoiceAmount = params.amount ? parseFloat(params.amount as string) : 0;

  const [amount, setAmount] = useState(invoiceAmount.toString());
  const [paymentMethod, setPaymentMethod] = useState<PaymentMethod>('cash');
  const [reference, setReference] = useState('');
  const [notes, setNotes] = useState('');
  const [loading, setLoading] = useState(false);

  const paymentMethods: { value: PaymentMethod; label: string; icon: string }[] = [
    { value: 'cash', label: 'Cash', icon: '💵' },
    { value: 'bank_transfer', label: 'Bank Transfer', icon: '🏦' },
    { value: 'mobile_money', label: 'Mobile Money', icon: '📱' },
    { value: 'credit', label: 'Credit', icon: '💳' },
  ];

  const handleSubmit = async () => {
    const parsedAmount = parseFloat(amount);
    
    if (!amount || isNaN(parsedAmount) || parsedAmount <= 0) {
      Alert.alert('Validation Error', 'Please enter a valid amount');
      return;
    }

    if (!invoiceId) {
      Alert.alert('Error', 'Invalid invoice ID');
      return;
    }

    try {
      setLoading(true);
      await paymentsApi.create({
        invoice_id: invoiceId,
        amount: parsedAmount,
        payment_method: paymentMethod,
        reference: reference.trim() || undefined,
        notes: notes.trim() || undefined,
        paid_at: new Date().toISOString(),
      });

      Alert.alert('Success', 'Payment recorded successfully', [
        {
          text: 'OK',
          onPress: () => router.back(),
        },
      ]);
    } catch (err: any) {
      console.error('Payment error:', err);
      Alert.alert(
        'Error',
        err.response?.data?.message || 'Failed to record payment'
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView style={styles.scrollView}>
        <View style={styles.header}>
          <Text style={styles.title}>Record Payment</Text>
          <Text style={styles.subtitle}>Invoice #{invoiceId}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Amount</Text>
          <View style={styles.amountContainer}>
            <Text style={styles.currency}>LKR</Text>
            <TextInput
              style={styles.amountInput}
              value={amount}
              onChangeText={setAmount}
              keyboardType="decimal-pad"
              placeholder="0.00"
              placeholderTextColor="#999"
            />
          </View>
          {invoiceAmount > 0 && (
            <Text style={styles.hint}>Invoice amount: LKR {invoiceAmount.toLocaleString()}</Text>
          )}
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Payment Method</Text>
          <View style={styles.methodGrid}>
            {paymentMethods.map((method) => (
              <TouchableOpacity
                key={method.value}
                style={[
                  styles.methodCard,
                  paymentMethod === method.value && styles.methodCardSelected,
                ]}
                onPress={() => setPaymentMethod(method.value)}
              >
                <Text style={styles.methodIcon}>{method.icon}</Text>
                <Text
                  style={[
                    styles.methodLabel,
                    paymentMethod === method.value && styles.methodLabelSelected,
                  ]}
                >
                  {method.label}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {(paymentMethod === 'bank_transfer' || paymentMethod === 'mobile_money') && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>
              {paymentMethod === 'bank_transfer' ? 'Transaction' : 'Mobile'} Reference
            </Text>
            <TextInput
              style={styles.input}
              value={reference}
              onChangeText={setReference}
              placeholder={
                paymentMethod === 'bank_transfer'
                  ? 'Transaction ID or Check Number'
                  : 'Mobile transaction ID'
              }
              placeholderTextColor="#999"
            />
          </View>
        )}

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Notes (Optional)</Text>
          <TextInput
            style={[styles.input, styles.textArea]}
            value={notes}
            onChangeText={setNotes}
            placeholder="Add payment notes..."
            placeholderTextColor="#999"
            multiline
            numberOfLines={4}
            textAlignVertical="top"
          />
        </View>

        <TouchableOpacity
          style={[styles.submitButton, loading && styles.submitButtonDisabled]}
          onPress={handleSubmit}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.submitButtonText}>Record Payment</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.cancelButton}
          onPress={() => router.back()}
          disabled={loading}
        >
          <Text style={styles.cancelButtonText}>Cancel</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  scrollView: {
    flex: 1,
  },
  header: {
    backgroundColor: '#fff',
    padding: 20,
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
  section: {
    backgroundColor: '#fff',
    marginTop: 16,
    padding: 16,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 12,
    color: '#333',
  },
  amountContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 2,
    borderColor: '#4caf50',
    borderRadius: 12,
    paddingHorizontal: 16,
    backgroundColor: '#f9f9f9',
  },
  currency: {
    fontSize: 20,
    fontWeight: '700',
    color: '#666',
    marginRight: 8,
  },
  amountInput: {
    flex: 1,
    fontSize: 28,
    fontWeight: '700',
    color: '#333',
    paddingVertical: 16,
  },
  hint: {
    marginTop: 8,
    fontSize: 12,
    color: '#666',
  },
  methodGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  methodCard: {
    flex: 1,
    minWidth: '45%',
    backgroundColor: '#f9f9f9',
    borderWidth: 2,
    borderColor: '#ddd',
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
  },
  methodCardSelected: {
    borderColor: '#4caf50',
    backgroundColor: '#e8f5e9',
  },
  methodIcon: {
    fontSize: 32,
    marginBottom: 8,
  },
  methodLabel: {
    fontSize: 14,
    color: '#666',
    textAlign: 'center',
  },
  methodLabelSelected: {
    color: '#2e7d32',
    fontWeight: '600',
  },
  input: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    backgroundColor: '#fff',
  },
  textArea: {
    height: 100,
    textAlignVertical: 'top',
  },
  submitButton: {
    backgroundColor: '#4caf50',
    margin: 16,
    marginBottom: 8,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  submitButtonDisabled: {
    backgroundColor: '#a5d6a7',
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: '600',
  },
  cancelButton: {
    marginHorizontal: 16,
    marginBottom: 32,
    padding: 16,
    alignItems: 'center',
  },
  cancelButtonText: {
    color: '#666',
    fontSize: 16,
    fontWeight: '600',
  },
});
