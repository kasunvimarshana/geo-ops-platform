import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Button, Input } from '@/components';
import { apiClient } from '@/services/api/client';
import { ApiResponse } from '@/types';

interface RegisterScreenProps {
  navigation: any;
}

export const RegisterScreen: React.FC<RegisterScreenProps> = ({ navigation }) => {
  const { t } = useTranslation();
  
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    password: '',
    confirmPassword: '',
    organizationName: '',
  });
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const updateField = (field: string, value: string) => {
    setFormData({ ...formData, [field]: value });
    setErrors({ ...errors, [field]: '' });
  };

  const validate = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!formData.name.trim()) {
      newErrors.name = t('auth.name_required');
    }

    if (!formData.email.trim()) {
      newErrors.email = t('auth.email_required');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = t('auth.email_invalid');
    }

    if (!formData.phone.trim()) {
      newErrors.phone = t('auth.phone_required');
    }

    if (!formData.password) {
      newErrors.password = t('auth.password_required');
    } else if (formData.password.length < 8) {
      newErrors.password = t('auth.password_too_short');
    }

    if (formData.password !== formData.confirmPassword) {
      newErrors.confirmPassword = t('auth.passwords_dont_match');
    }

    if (!formData.organizationName.trim()) {
      newErrors.organizationName = t('auth.organization_required');
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleRegister = async () => {
    if (!validate()) return;

    try {
      setLoading(true);
      
      const response = await apiClient.post<ApiResponse>('/auth/register', {
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        password: formData.password,
        organization_name: formData.organizationName,
      });

      if (response.success) {
        Alert.alert(
          t('common.success'),
          t('auth.registration_success'),
          [
            {
              text: t('common.ok'),
              onPress: () => navigation.navigate('Login'),
            },
          ]
        );
      } else {
        Alert.alert(
          t('common.error'),
          response.message || t('auth.registration_failed')
        );
      }
    } catch (error: any) {
      Alert.alert(
        t('common.error'),
        error.message || t('auth.registration_failed')
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.header}>
          <Text style={styles.title}>{t('auth.register')}</Text>
          <Text style={styles.subtitle}>{t('auth.create_account')}</Text>
        </View>

        <View style={styles.form}>
          <Input
            label={t('auth.name')}
            value={formData.name}
            onChangeText={(text) => updateField('name', text)}
            placeholder={t('auth.name_placeholder')}
            error={errors.name}
            editable={!loading}
          />

          <Input
            label={t('auth.email')}
            value={formData.email}
            onChangeText={(text) => updateField('email', text)}
            type="email"
            placeholder="example@email.com"
            error={errors.email}
            editable={!loading}
          />

          <Input
            label={t('auth.phone')}
            value={formData.phone}
            onChangeText={(text) => updateField('phone', text)}
            type="phone"
            placeholder="+94771234567"
            error={errors.phone}
            editable={!loading}
          />

          <Input
            label={t('auth.organization_name')}
            value={formData.organizationName}
            onChangeText={(text) => updateField('organizationName', text)}
            placeholder={t('auth.organization_placeholder')}
            error={errors.organizationName}
            editable={!loading}
          />

          <Input
            label={t('auth.password')}
            value={formData.password}
            onChangeText={(text) => updateField('password', text)}
            type="password"
            placeholder="••••••••"
            error={errors.password}
            editable={!loading}
          />

          <Input
            label={t('auth.confirm_password')}
            value={formData.confirmPassword}
            onChangeText={(text) => updateField('confirmPassword', text)}
            type="password"
            placeholder="••••••••"
            error={errors.confirmPassword}
            editable={!loading}
          />

          <Button
            title={t('auth.register')}
            onPress={handleRegister}
            loading={loading}
            style={styles.registerButton}
          />

          <Button
            title={t('auth.back_to_login')}
            onPress={() => navigation.goBack()}
            variant="secondary"
            disabled={loading}
            style={styles.backButton}
          />
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  content: {
    flexGrow: 1,
    padding: 24,
  },
  header: {
    alignItems: 'center',
    marginTop: 24,
    marginBottom: 32,
  },
  title: {
    fontSize: 28,
    fontWeight: '700',
    color: '#2196F3',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
    color: '#666',
  },
  form: {
    width: '100%',
  },
  registerButton: {
    marginTop: 8,
  },
  backButton: {
    marginTop: 16,
  },
});
