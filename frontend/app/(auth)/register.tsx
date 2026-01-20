import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert, ScrollView, KeyboardAvoidingView, Platform } from 'react-native';
import { useRouter } from 'expo-router';
import { useAuthStore } from '../../src/store/authStore';

export default function RegisterScreen() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    organization_name: '',
  });
  
  const { register, isLoading } = useAuthStore();
  const router = useRouter();

  const handleRegister = async () => {
    if (!formData.name || !formData.email || !formData.password || !formData.organization_name) {
      Alert.alert('Error', 'Please fill in all required fields');
      return;
    }

    if (formData.password !== formData.password_confirmation) {
      Alert.alert('Error', 'Passwords do not match');
      return;
    }

    if (formData.password.length < 8) {
      Alert.alert('Error', 'Password must be at least 8 characters');
      return;
    }

    try {
      await register(formData);
      router.replace('/(tabs)');
    } catch (err: any) {
      Alert.alert('Registration Failed', err.message || 'Please try again');
    }
  };

  return (
    <KeyboardAvoidingView 
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <Text style={styles.title}>Create Account</Text>
        <Text style={styles.subtitle}>Start managing your fields</Text>

        <View style={styles.form}>
          <TextInput
            style={styles.input}
            placeholder="Organization Name *"
            value={formData.organization_name}
            onChangeText={(text) => setFormData({ ...formData, organization_name: text })}
            editable={!isLoading}
          />

          <TextInput
            style={styles.input}
            placeholder="Your Name *"
            value={formData.name}
            onChangeText={(text) => setFormData({ ...formData, name: text })}
            editable={!isLoading}
          />

          <TextInput
            style={styles.input}
            placeholder="Email *"
            value={formData.email}
            onChangeText={(text) => setFormData({ ...formData, email: text })}
            autoCapitalize="none"
            keyboardType="email-address"
            editable={!isLoading}
          />

          <TextInput
            style={styles.input}
            placeholder="Phone"
            value={formData.phone}
            onChangeText={(text) => setFormData({ ...formData, phone: text })}
            keyboardType="phone-pad"
            editable={!isLoading}
          />

          <TextInput
            style={styles.input}
            placeholder="Password (min 8 characters) *"
            value={formData.password}
            onChangeText={(text) => setFormData({ ...formData, password: text })}
            secureTextEntry
            editable={!isLoading}
          />

          <TextInput
            style={styles.input}
            placeholder="Confirm Password *"
            value={formData.password_confirmation}
            onChangeText={(text) => setFormData({ ...formData, password_confirmation: text })}
            secureTextEntry
            editable={!isLoading}
          />

          <TouchableOpacity
            style={[styles.button, isLoading && styles.buttonDisabled]}
            onPress={handleRegister}
            disabled={isLoading}
          >
            <Text style={styles.buttonText}>
              {isLoading ? 'Creating Account...' : 'Register'}
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.linkButton}
            onPress={() => router.back()}
            disabled={isLoading}
          >
            <Text style={styles.linkText}>
              Already have an account? Login
            </Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    paddingHorizontal: 24,
    paddingVertical: 40,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#2e7d32',
    textAlign: 'center',
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 16,
    color: '#666',
    textAlign: 'center',
    marginBottom: 32,
  },
  form: {
    marginBottom: 24,
  },
  input: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 8,
    padding: 16,
    marginBottom: 16,
    fontSize: 16,
  },
  button: {
    backgroundColor: '#2e7d32',
    borderRadius: 8,
    padding: 16,
    alignItems: 'center',
    marginTop: 8,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  buttonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: '600',
  },
  linkButton: {
    marginTop: 16,
    padding: 8,
    alignItems: 'center',
  },
  linkText: {
    color: '#2e7d32',
    fontSize: 14,
  },
});
