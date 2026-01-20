import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useJobsStore } from '../../../store/jobsStore';
import { usePlotsStore } from '../../../store/plotsStore';
import { Card } from '../../../shared/components/Card';
import { Button } from '../../../shared/components/Button';
import { LoadingSpinner } from '../../../shared/components/LoadingSpinner';
import { colors } from '../../../theme/colors';
import { typography } from '../../../theme/typography';
import { spacing } from '../../../theme/spacing';
import { formatDate, formatCurrency } from '../../../shared/utils/formatters';

interface JobDetailScreenProps {
  route: any;
  navigation: any;
}

export const JobDetailScreen: React.FC<JobDetailScreenProps> = ({ route, navigation }) => {
  const { t } = useTranslation();
  const { jobId } = route.params;
  const { currentJob, fetchJob, updateJobStatus, isLoading } = useJobsStore();
  const { plots, fetchPlots } = usePlotsStore();

  useEffect(() => {
    loadData();
  }, [jobId]);

  const loadData = async () => {
    await fetchJob(jobId);
    await fetchPlots(jobId);
  };

  const handleStatusChange = async (newStatus: string) => {
    Alert.alert(
      t('common.confirm'),
      `Change status to ${newStatus}?`,
      [
        { text: t('common.cancel'), style: 'cancel' },
        {
          text: t('common.yes'),
          onPress: async () => {
            try {
              await updateJobStatus(jobId, newStatus);
              await loadData();
            } catch (error: any) {
              Alert.alert(t('common.error'), error.message);
            }
          },
        },
      ]
    );
  };

  const handleAddMeasurement = () => {
    navigation.navigate('GPSTab', { screen: 'Measurement', params: { jobId } });
  };

  if (isLoading || !currentJob) {
    return <LoadingSpinner fullScreen />;
  }

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

  return (
    <ScrollView style={styles.container}>
      <Card style={styles.mainCard}>
        <View style={styles.header}>
          <Text style={styles.title}>{currentJob.title}</Text>
          <View style={[styles.statusBadge, { backgroundColor: getStatusColor(currentJob.status) }]}>
            <Text style={styles.statusText}>{t(`jobs.${currentJob.status}`)}</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>{t('jobs.customerName')}</Text>
          <Text style={styles.value}>{currentJob.customer_name}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>{t('jobs.location')}</Text>
          <Text style={styles.value}>{currentJob.location}</Text>
        </View>

        {currentJob.description && (
          <View style={styles.section}>
            <Text style={styles.label}>{t('jobs.description')}</Text>
            <Text style={styles.value}>{currentJob.description}</Text>
          </View>
        )}

        {currentJob.estimated_price && (
          <View style={styles.section}>
            <Text style={styles.label}>{t('jobs.estimatedPrice')}</Text>
            <Text style={styles.value}>{formatCurrency(currentJob.estimated_price)}</Text>
          </View>
        )}

        {currentJob.scheduled_date && (
          <View style={styles.section}>
            <Text style={styles.label}>{t('jobs.scheduledDate')}</Text>
            <Text style={styles.value}>{formatDate(currentJob.scheduled_date)}</Text>
          </View>
        )}
      </Card>

      <Card style={styles.plotsCard}>
        <View style={styles.plotsHeader}>
          <Text style={styles.sectionTitle}>Land Plots ({plots.length})</Text>
          <Button
            title={t('gps.addPoint')}
            onPress={handleAddMeasurement}
            style={styles.addButton}
          />
        </View>

        {plots.map((plot, index) => (
          <View key={plot.id || index} style={styles.plotItem}>
            <Text style={styles.plotLabel}>Plot {index + 1}</Text>
            <Text style={styles.plotValue}>
              {plot.area_sqm.toFixed(2)} {t('gps.sqm')} ({plot.area_acres.toFixed(3)} {t('gps.acres')})
            </Text>
            <Text style={styles.plotValue}>
              {t('gps.perimeter')}: {plot.perimeter_m.toFixed(2)} {t('gps.meters')}
            </Text>
          </View>
        ))}
      </Card>

      <Card style={styles.actionsCard}>
        <Text style={styles.sectionTitle}>{t('jobs.status')} Actions</Text>
        <View style={styles.statusActions}>
          {currentJob.status === 'pending' && (
            <Button
              title="Start Job"
              onPress={() => handleStatusChange('in_progress')}
              style={styles.actionButton}
            />
          )}
          {currentJob.status === 'in_progress' && (
            <Button
              title="Complete Job"
              onPress={() => handleStatusChange('completed')}
              variant="secondary"
              style={styles.actionButton}
            />
          )}
          {currentJob.status !== 'cancelled' && currentJob.status !== 'completed' && (
            <Button
              title="Cancel Job"
              onPress={() => handleStatusChange('cancelled')}
              variant="outline"
              style={styles.actionButton}
            />
          )}
        </View>
      </Card>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.surface,
  },
  mainCard: {
    margin: spacing.md,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: spacing.md,
  },
  title: {
    ...typography.h2,
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
  section: {
    marginBottom: spacing.md,
  },
  label: {
    ...typography.body2,
    color: colors.text.secondary,
    marginBottom: spacing.xs,
  },
  value: {
    ...typography.body1,
    color: colors.text.primary,
  },
  plotsCard: {
    margin: spacing.md,
    marginTop: 0,
  },
  plotsHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: spacing.md,
  },
  sectionTitle: {
    ...typography.h4,
    color: colors.text.primary,
  },
  addButton: {
    paddingVertical: spacing.xs,
    paddingHorizontal: spacing.md,
    minHeight: 36,
  },
  plotItem: {
    padding: spacing.sm,
    backgroundColor: colors.surface,
    borderRadius: 8,
    marginBottom: spacing.sm,
  },
  plotLabel: {
    ...typography.body1,
    color: colors.text.primary,
    fontWeight: '600',
    marginBottom: spacing.xs,
  },
  plotValue: {
    ...typography.body2,
    color: colors.text.secondary,
  },
  actionsCard: {
    margin: spacing.md,
    marginTop: 0,
  },
  statusActions: {
    gap: spacing.sm,
    marginTop: spacing.md,
  },
  actionButton: {
    marginBottom: spacing.sm,
  },
});
