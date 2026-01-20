import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { useAuth } from '../../src/hooks/useAuth';

const ProfileScreen = () => {
  const { user } = useAuth();

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Profile</Text>
      <Text style={styles.label}>Name:</Text>
      <Text style={styles.value}>{user?.name}</Text>
      <Text style={styles.label}>Email:</Text>
      <Text style={styles.value}>{user?.email}</Text>
      <Text style={styles.label}>Role:</Text>
      <Text style={styles.value}>{user?.role}</Text>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 20,
    backgroundColor: '#fff',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 20,
  },
  label: {
    fontSize: 16,
    fontWeight: '600',
    marginTop: 10,
  },
  value: {
    fontSize: 16,
    marginBottom: 10,
  },
});

export default ProfileScreen;