import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useJobsStore } from '../../../store/jobsStore';
import { Input } from '../../../shared/components/Input';
import { Button } from '../../../shared/components/Button';
import { colors } from '../../../theme/colors';
import { typography } from '../../../theme/typography';
import { spacing } from '../../../theme/spacing';

interface CreateJobScreenProps {
  navigation: any;
}

export const CreateJobScreen: React.FC<CreateJobScreenProps> = ({ navigation }) => {
  const { t } = useTranslation();
  const { createJob, isLoading } = useJobsStore();

  const [formData, setFormData] = useState({
    title: '',
    customer_name: '',
    location: '',
    description: '',
    estimated_price: '',
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const validateForm = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.title.trim()) {
      newErrors.title = 'Title is required';
    }
    if (!formData.customer_name.trim()) {
      newErrors.customer_name = 'Customer name is required';
    }
    if (!formData.location.trim()) {
      newErrors.location = 'Location is required';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async () => {
    if (!validateForm()) {
      return;
    }

    try {
      await createJob({
        title: formData.title,
        customer_name: formData.customer_name,
        location: formData.location,
        description: formData.description || undefined,
        estimated_price: formData.estimated_price ? parseFloat(formData.estimated_price) : undefined,
        status: 'pending',
      });

      Alert.alert(t('common.success'), 'Job created successfully');
      navigation.goBack();
    } catch (error: any) {
      Alert.alert(t('common.error'), error.message);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="handled"
      >
        <Text style={styles.title}>{t('jobs.createJob')}</Text>

        <Input
          label={t('jobs.jobTitle')}
          value={formData.title}
          onChangeText={(text) => setFormData({ ...formData, title: text })}
          error={errors.title}
        />

        <Input
          label={t('jobs.customerName')}
          value={formData.customer_name}
          onChangeText={(text) => setFormData({ ...formData, customer_name: text })}
          error={errors.customer_name}
        />

        <Input
          label={t('jobs.location')}
          value={formData.location}
          onChangeText={(text) => setFormData({ ...formData, location: text })}
          error={errors.location}
        />

        <Input
          label={t('jobs.description')}
          value={formData.description}
          onChangeText={(text) => setFormData({ ...formData, description: text })}
          multiline
          numberOfLines={4}
          style={styles.textArea}
        />

        <Input
          label={t('jobs.estimatedPrice')}
          value={formData.estimated_price}
          onChangeText={(text) => setFormData({ ...formData, estimated_price: text })}
          keyboardType="numeric"
        />

        <View style={styles.buttonContainer}>
          <Button
            title={t('common.cancel')}
            onPress={() => navigation.goBack()}
            variant="outline"
            style={styles.button}
          />
          <Button
            title={t('common.save')}
            onPress={handleSubmit}
            loading={isLoading}
            style={styles.button}
          />
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  scrollContent: {
    padding: spacing.md,
  },
  title: {
    ...typography.h2,
    color: colors.text.primary,
    marginBottom: spacing.lg,
  },
  textArea: {
    minHeight: 100,
    textAlignVertical: 'top',
  },
  buttonContainer: {
    flexDirection: 'row',
    gap: spacing.md,
    marginTop: spacing.lg,
  },
  button: {
    flex: 1,
  },
});
