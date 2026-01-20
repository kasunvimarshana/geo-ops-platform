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
import { useAuthStore } from '@/stores/authStore';
import { apiClient } from '@/services/api/client';
import { ApiResponse } from '@/types';

interface LoginScreenProps {
  navigation: any;
}

export const LoginScreen: React.FC<LoginScreenProps> = ({ navigation }) => {
  const { t } = useTranslation();
  const setAuth = useAuthStore((state) => state.setAuth);
  
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const validate = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!email.trim()) {
      newErrors.email = t('auth.email_required');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      newErrors.email = t('auth.email_invalid');
    }

    if (!password) {
      newErrors.password = t('auth.password_required');
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleLogin = async () => {
    if (!validate()) return;

    try {
      setLoading(true);
      
      const response = await apiClient.post<ApiResponse>('/auth/login', {
        email,
        password,
      });

      if (response.success && response.data) {
        const { user, organization, access_token, refresh_token } = response.data;
        setAuth(user, organization, access_token, refresh_token);
        
        // Navigation will be handled automatically by auth state change
      } else {
        Alert.alert(
          t('common.error'),
          response.message || t('auth.invalid_credentials')
        );
      }
    } catch (error: any) {
      Alert.alert(
        t('common.error'),
        error.message || t('auth.login_failed')
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
          <Text style={styles.title}>{t('common.app_name')}</Text>
          <Text style={styles.subtitle}>{t('auth.login_subtitle')}</Text>
        </View>

        <View style={styles.form}>
          <Input
            label={t('auth.email')}
            value={email}
            onChangeText={(text) => {
              setEmail(text);
              setErrors({ ...errors, email: '' });
            }}
            type="email"
            placeholder="example@email.com"
            error={errors.email}
            autoCapitalize="none"
            editable={!loading}
          />

          <Input
            label={t('auth.password')}
            value={password}
            onChangeText={(text) => {
              setPassword(text);
              setErrors({ ...errors, password: '' });
            }}
            type="password"
            placeholder="••••••••"
            error={errors.password}
            editable={!loading}
          />

          <Button
            title={t('auth.login')}
            onPress={handleLogin}
            loading={loading}
            style={styles.loginButton}
          />

          <Button
            title={t('auth.register')}
            onPress={() => navigation.navigate('Register')}
            variant="secondary"
            disabled={loading}
            style={styles.registerButton}
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
    justifyContent: 'center',
    padding: 24,
  },
  header: {
    alignItems: 'center',
    marginBottom: 48,
  },
  title: {
    fontSize: 32,
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
  loginButton: {
    marginTop: 8,
  },
  registerButton: {
    marginTop: 16,
  },
});
