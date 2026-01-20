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
import { useTranslation } from 'react-i18next';
import { useJobStore } from '../../stores/jobStore';
import { JobCreateData } from '../../../domain/entities/Job';

export default function CreateJobScreen({ navigation }: any) {
  const { t } = useTranslation();
  const { createJob, isLoading } = useJobStore();
  
  const [formData, setFormData] = useState<Partial<JobCreateData>>({
    title: '',
    description: '',
    status: 'pending',
    priority: 'medium',
    due_date: '',
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!formData.title || formData.title.trim() === '') {
      newErrors.title = 'Job title is required';
    }

    if (!formData.description || formData.description.trim() === '') {
      newErrors.description = 'Description is required';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async () => {
    if (!validateForm()) {
      Alert.alert('Validation Error', 'Please fill in all required fields');
      return;
    }

    try {
      await createJob(formData as JobCreateData);
      Alert.alert(
        'Success',
        'Job created successfully',
        [
          {
            text: 'OK',
            onPress: () => navigation.goBack(),
          },
        ]
      );
    } catch (error: any) {
      Alert.alert('Error', error.message || 'Failed to create job');
    }
  };

  const priorityOptions = [
    { value: 'low', label: 'Low', color: '#95a5a6' },
    { value: 'medium', label: 'Medium', color: '#f39c12' },
    { value: 'high', label: 'High', color: '#e74c3c' },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => navigation.goBack()}>
          <Text style={styles.backButton}>← Back</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Create Job</Text>
        <View style={{ width: 60 }} />
      </View>

      <ScrollView style={styles.content} contentContainerStyle={styles.contentContainer}>
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Job Information</Text>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Job Title *</Text>
            <TextInput
              style={[styles.input, errors.title && styles.inputError]}
              placeholder="Enter job title"
              value={formData.title}
              onChangeText={(text) => {
                setFormData({ ...formData, title: text });
                if (errors.title) setErrors({ ...errors, title: '' });
              }}
            />
            {errors.title && <Text style={styles.errorText}>{errors.title}</Text>}
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Description *</Text>
            <TextInput
              style={[styles.input, styles.textArea, errors.description && styles.inputError]}
              placeholder="Enter job description"
              value={formData.description}
              onChangeText={(text) => {
                setFormData({ ...formData, description: text });
                if (errors.description) setErrors({ ...errors, description: '' });
              }}
              multiline
              numberOfLines={4}
            />
            {errors.description && <Text style={styles.errorText}>{errors.description}</Text>}
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Due Date</Text>
            <TextInput
              style={styles.input}
              placeholder="YYYY-MM-DD"
              value={formData.due_date}
              onChangeText={(text) => setFormData({ ...formData, due_date: text })}
            />
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Priority</Text>
          <View style={styles.priorityContainer}>
            {priorityOptions.map((option) => (
              <TouchableOpacity
                key={option.value}
                style={[
                  styles.priorityOption,
                  formData.priority === option.value && styles.priorityOptionActive,
                  { borderColor: option.color },
                ]}
                onPress={() => setFormData({ ...formData, priority: option.value })}
              >
                <Text
                  style={[
                    styles.priorityText,
                    formData.priority === option.value && { color: option.color },
                  ]}
                >
                  {option.label}
                </Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        <TouchableOpacity
          style={[styles.submitButton, isLoading && styles.submitButtonDisabled]}
          onPress={handleSubmit}
          disabled={isLoading}
        >
          {isLoading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.submitButtonText}>Create Job</Text>
          )}
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
  section: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 15,
    marginBottom: 15,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 15,
  },
  inputGroup: {
    marginBottom: 15,
  },
  label: {
    fontSize: 14,
    fontWeight: '600',
    color: '#34495e',
    marginBottom: 5,
  },
  input: {
    backgroundColor: '#f8f9fa',
    borderWidth: 1,
    borderColor: '#e0e0e0',
    borderRadius: 8,
    padding: 12,
    fontSize: 16,
    color: '#2c3e50',
  },
  textArea: {
    height: 100,
    textAlignVertical: 'top',
  },
  inputError: {
    borderColor: '#e74c3c',
  },
  errorText: {
    color: '#e74c3c',
    fontSize: 12,
    marginTop: 5,
  },
  priorityContainer: {
    flexDirection: 'row',
    gap: 10,
  },
  priorityOption: {
    flex: 1,
    borderWidth: 2,
    borderRadius: 8,
    padding: 12,
    alignItems: 'center',
    backgroundColor: '#f8f9fa',
  },
  priorityOptionActive: {
    backgroundColor: '#fff',
  },
  priorityText: {
    fontSize: 14,
    fontWeight: '600',
    color: '#7f8c8d',
  },
  submitButton: {
    backgroundColor: '#e67e22',
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
    marginTop: 15,
    marginBottom: 30,
  },
  submitButtonDisabled: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
