import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useAuthStore } from '../../stores/authStore';

export default function LoginScreen({ navigation }: any) {
  const { t } = useTranslation();
  const { login, isLoading, error, clearError } = useAuthStore();
  
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert(t('common.error'), 'Please enter email and password');
      return;
    }

    try {
      await login(email, password);
      // Navigation will be handled by auth state change
    } catch (error: any) {
      Alert.alert(t('common.error'), error.message);
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
      <View style={styles.content}>
        <Text style={styles.title}>{t('common.appName')}</Text>
        <Text style={styles.subtitle}>{t('auth.login')}</Text>

        <TextInput
          style={styles.input}
          placeholder={t('auth.email')}
          value={email}
          onChangeText={setEmail}
          keyboardType="email-address"
          autoCapitalize="none"
          autoCorrect={false}
        />

        <TextInput
          style={styles.input}
          placeholder={t('auth.password')}
          value={password}
          onChangeText={setPassword}
          secureTextEntry
          autoCapitalize="none"
          autoCorrect={false}
        />

        <TouchableOpacity
          style={[styles.button, isLoading && styles.buttonDisabled]}
          onPress={handleLogin}
          disabled={isLoading}
        >
          {isLoading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.buttonText}>{t('auth.login')}</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.linkButton}
          onPress={() => navigation.navigate('Register')}
        >
          <Text style={styles.linkText}>
            Don't have an account? {t('auth.register')}
          </Text>
        </TouchableOpacity>

        {error && (
          <Text style={styles.errorText}>{error}</Text>
        )}
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  content: {
    flex: 1,
    justifyContent: 'center',
    padding: 20,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 10,
    color: '#2c3e50',
  },
  subtitle: {
    fontSize: 24,
    textAlign: 'center',
    marginBottom: 40,
    color: '#7f8c8d',
  },
  input: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 8,
    marginBottom: 15,
    fontSize: 16,
    borderWidth: 1,
    borderColor: '#ddd',
  },
  button: {
    backgroundColor: '#27ae60',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 10,
  },
  buttonDisabled: {
    backgroundColor: '#95a5a6',
  },
  buttonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  linkButton: {
    marginTop: 20,
    alignItems: 'center',
  },
  linkText: {
    color: '#3498db',
    fontSize: 16,
  },
  errorText: {
    color: '#e74c3c',
    textAlign: 'center',
    marginTop: 15,
    fontSize: 14,
  },
});
