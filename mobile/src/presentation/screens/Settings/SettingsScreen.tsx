import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  SafeAreaView,
  Switch,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useAuthStore } from '../../stores/authStore';

export default function SettingsScreen({ navigation }: any) {
  const { t, i18n } = useTranslation();
  const { user } = useAuthStore();
  const [notificationsEnabled, setNotificationsEnabled] = useState(true);
  const [locationEnabled, setLocationEnabled] = useState(true);

  const changeLanguage = (lang: string) => {
    i18n.changeLanguage(lang);
  };

  const settingsSections = [
    {
      title: 'Account',
      items: [
        {
          label: 'Name',
          value: user?.name || 'N/A',
          type: 'info',
        },
        {
          label: 'Email',
          value: user?.email || 'N/A',
          type: 'info',
        },
        {
          label: 'Organization',
          value: user?.organization?.name || 'N/A',
          type: 'info',
        },
      ],
    },
    {
      title: 'Preferences',
      items: [
        {
          label: 'Language',
          value: i18n.language === 'si' ? 'සිංහල' : 'English',
          type: 'language',
          onPress: () => {
            const newLang = i18n.language === 'en' ? 'si' : 'en';
            changeLanguage(newLang);
          },
        },
        {
          label: 'Notifications',
          value: notificationsEnabled,
          type: 'toggle',
          onToggle: setNotificationsEnabled,
        },
        {
          label: 'Location Services',
          value: locationEnabled,
          type: 'toggle',
          onToggle: setLocationEnabled,
        },
      ],
    },
    {
      title: 'GPS Settings',
      items: [
        {
          label: 'Accuracy Mode',
          value: 'High',
          type: 'button',
          onPress: () => {},
        },
        {
          label: 'Update Interval',
          value: '2 seconds',
          type: 'button',
          onPress: () => {},
        },
      ],
    },
    {
      title: 'About',
      items: [
        {
          label: 'Version',
          value: '1.0.0',
          type: 'info',
        },
        {
          label: 'Privacy Policy',
          type: 'button',
          onPress: () => {},
        },
        {
          label: 'Terms of Service',
          type: 'button',
          onPress: () => {},
        },
      ],
    },
  ];

  const renderSettingItem = (item: any, index: number) => {
    switch (item.type) {
      case 'toggle':
        return (
          <View key={index} style={styles.settingItem}>
            <Text style={styles.settingLabel}>{item.label}</Text>
            <Switch
              value={item.value}
              onValueChange={item.onToggle}
              trackColor={{ false: '#ddd', true: '#27ae60' }}
              thumbColor="#fff"
            />
          </View>
        );
      
      case 'button':
        return (
          <TouchableOpacity
            key={index}
            style={styles.settingItem}
            onPress={item.onPress}
          >
            <Text style={styles.settingLabel}>{item.label}</Text>
            <View style={styles.settingValueContainer}>
              {item.value && (
                <Text style={styles.settingValue}>{item.value}</Text>
              )}
              <Text style={styles.chevron}>›</Text>
            </View>
          </TouchableOpacity>
        );
      
      case 'language':
        return (
          <TouchableOpacity
            key={index}
            style={styles.settingItem}
            onPress={item.onPress}
          >
            <Text style={styles.settingLabel}>{item.label}</Text>
            <View style={styles.settingValueContainer}>
              <Text style={styles.settingValue}>{item.value}</Text>
              <Text style={styles.chevron}>›</Text>
            </View>
          </TouchableOpacity>
        );
      
      default:
        return (
          <View key={index} style={styles.settingItem}>
            <Text style={styles.settingLabel}>{item.label}</Text>
            <Text style={styles.settingValue}>{item.value}</Text>
          </View>
        );
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>{t('settings.title')}</Text>
      </View>

      <ScrollView style={styles.content}>
        {settingsSections.map((section, sectionIndex) => (
          <View key={sectionIndex} style={styles.section}>
            <Text style={styles.sectionTitle}>{section.title}</Text>
            <View style={styles.sectionContent}>
              {section.items.map((item, itemIndex) =>
                renderSettingItem(item, itemIndex)
              )}
            </View>
          </View>
        ))}
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
    backgroundColor: '#fff',
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  content: {
    flex: 1,
  },
  section: {
    marginTop: 20,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#95a5a6',
    textTransform: 'uppercase',
    marginLeft: 15,
    marginBottom: 10,
  },
  sectionContent: {
    backgroundColor: '#fff',
  },
  settingItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  settingLabel: {
    fontSize: 16,
    color: '#2c3e50',
    flex: 1,
  },
  settingValue: {
    fontSize: 16,
    color: '#7f8c8d',
    marginRight: 10,
  },
  settingValueContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  chevron: {
    fontSize: 24,
    color: '#bdc3c7',
  },
});
