import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  SafeAreaView,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useAuthStore } from '../../stores/authStore';

export default function HomeScreen({ navigation }: any) {
  const { t } = useTranslation();
  const { user, logout } = useAuthStore();

  const menuItems = [
    {
      title: t('fields.title'),
      description: 'Manage your agricultural fields',
      icon: '🗺️',
      screen: 'Fields',
      color: '#27ae60',
    },
    {
      title: t('gps.title'),
      description: 'Measure land with GPS',
      icon: '📍',
      screen: 'GPSMeasurement',
      color: '#3498db',
    },
    {
      title: t('jobs.title'),
      description: 'Manage tasks and assignments',
      icon: '📋',
      screen: 'Jobs',
      color: '#e67e22',
    },
    {
      title: t('settings.title'),
      description: 'App settings and preferences',
      icon: '⚙️',
      screen: 'Settings',
      color: '#95a5a6',
    },
  ];

  const handleLogout = async () => {
    await logout();
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>{t('common.appName')}</Text>
        <Text style={styles.headerSubtitle}>
          {t('common.welcome')}, {user?.name}!
        </Text>
      </View>

      <ScrollView style={styles.content}>
        <View style={styles.menuGrid}>
          {menuItems.map((item, index) => (
            <TouchableOpacity
              key={index}
              style={[styles.menuItem, { borderLeftColor: item.color }]}
              onPress={() => navigation.navigate(item.screen)}
            >
              <Text style={styles.menuIcon}>{item.icon}</Text>
              <View style={styles.menuTextContainer}>
                <Text style={styles.menuTitle}>{item.title}</Text>
                <Text style={styles.menuDescription}>{item.description}</Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>

        <TouchableOpacity
          style={styles.logoutButton}
          onPress={handleLogout}
        >
          <Text style={styles.logoutButtonText}>{t('auth.logout')}</Text>
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
    backgroundColor: '#27ae60',
    padding: 20,
    paddingTop: 40,
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#fff',
    marginBottom: 5,
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#fff',
    opacity: 0.9,
  },
  content: {
    flex: 1,
    padding: 15,
  },
  menuGrid: {
    marginTop: 10,
  },
  menuItem: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    padding: 20,
    borderRadius: 12,
    marginBottom: 15,
    alignItems: 'center',
    borderLeftWidth: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  menuIcon: {
    fontSize: 40,
    marginRight: 15,
  },
  menuTextContainer: {
    flex: 1,
  },
  menuTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 5,
  },
  menuDescription: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  logoutButton: {
    backgroundColor: '#e74c3c',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20,
    marginBottom: 30,
  },
  logoutButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
