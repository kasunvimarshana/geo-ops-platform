import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Alert } from 'react-native';
import { useAuthStore } from '../../src/store/authStore';
import { useRouter } from 'expo-router';
import { usePrinter } from '../../src/hooks/usePrinter';

export default function ProfileScreen() {
  const { user, logout } = useAuthStore();
  const router = useRouter();
  const { isConnected, status, pendingJobsCount } = usePrinter();

  const handleLogout = () => {
    Alert.alert(
      'Logout',
      'Are you sure you want to logout?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Logout',
          onPress: async () => {
            await logout();
            router.replace('/(auth)/login');
          },
          style: 'destructive',
        },
      ]
    );
  };

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>
            {user?.name?.charAt(0).toUpperCase()}
          </Text>
        </View>
        <Text style={styles.name}>{user?.name}</Text>
        <Text style={styles.email}>{user?.email}</Text>
        <Text style={styles.role}>{user?.role?.toUpperCase()}</Text>
      </View>

      <View style={styles.section}>
        <TouchableOpacity style={styles.menuItem}>
          <Text style={styles.menuText}>📱 Account Settings</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.menuItem} onPress={() => {
          // Navigate to printer settings - will implement route later
          Alert.alert('Printer Settings', 'Opening printer settings...');
        }}>
          <View style={styles.menuRow}>
            <Text style={styles.menuText}>🖨️ Printer Settings</Text>
            {isConnected && (
              <View style={styles.printerBadge}>
                <View style={styles.connectedDot} />
                <Text style={styles.badgeText}>Connected</Text>
              </View>
            )}
          </View>
        </TouchableOpacity>
        <TouchableOpacity style={styles.menuItem}>
          <Text style={styles.menuText}>🔔 Notifications</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.menuItem}>
          <Text style={styles.menuText}>🌍 Language</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.menuItem}>
          <Text style={styles.menuText}>❓ Help & Support</Text>
        </TouchableOpacity>
      </View>

      <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
        <Text style={styles.logoutText}>Logout</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    backgroundColor: '#2e7d32',
    padding: 40,
    alignItems: 'center',
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: '#fff',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  avatarText: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#2e7d32',
  },
  name: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 4,
  },
  email: {
    fontSize: 14,
    color: '#a5d6a7',
    marginBottom: 8,
  },
  role: {
    fontSize: 12,
    color: '#c8e6c9',
    fontWeight: '600',
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  section: {
    marginTop: 16,
    backgroundColor: '#fff',
  },
  menuItem: {
    padding: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  menuText: {
    fontSize: 16,
    color: '#333',
  },
  menuRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    width: '100%',
  },
  printerBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#e8f5e9',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  connectedDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#4caf50',
    marginRight: 4,
  },
  badgeText: {
    fontSize: 11,
    color: '#2e7d32',
    fontWeight: '600',
  },
  logoutButton: {
    margin: 16,
    backgroundColor: '#d32f2f',
    padding: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  logoutText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
