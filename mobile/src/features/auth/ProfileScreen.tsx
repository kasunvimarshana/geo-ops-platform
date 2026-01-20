import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  Alert,
  Switch,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Button, Input, Card, Header } from '@/components';
import { useAuthStore } from '@/stores/authStore';
import { apiClient } from '@/services/api/client';
import { ApiResponse } from '@/types';

interface ProfileScreenProps {
  navigation: any;
}

export const ProfileScreen: React.FC<ProfileScreenProps> = ({ navigation }) => {
  const { t, i18n } = useTranslation();
  const { user, organization, clearAuth, updateUser } = useAuthStore();
  
  const [editing, setEditing] = useState(false);
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    name: user?.name || '',
    phone: user?.phone || '',
  });

  const handleSave = async () => {
    try {
      setLoading(true);
      
      const response = await apiClient.patch<ApiResponse>('/auth/profile', formData);

      if (response.success && response.data) {
        updateUser(response.data);
        setEditing(false);
        Alert.alert(t('common.success'), t('profile.update_success'));
      } else {
        Alert.alert(t('common.error'), response.message);
      }
    } catch (error: any) {
      Alert.alert(t('common.error'), error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = () => {
    Alert.alert(
      t('auth.logout'),
      t('auth.logout_confirm'),
      [
        { text: t('common.cancel'), style: 'cancel' },
        {
          text: t('auth.logout'),
          style: 'destructive',
          onPress: async () => {
            try {
              await apiClient.post('/auth/logout');
            } catch (error) {
              console.error('Logout error:', error);
            } finally {
              clearAuth();
            }
          },
        },
      ]
    );
  };

  const toggleLanguage = async () => {
    const newLang = i18n.language === 'en' ? 'si' : 'en';
    await i18n.changeLanguage(newLang);
    
    try {
      await apiClient.patch('/auth/profile', { language: newLang });
      updateUser({ language: newLang });
    } catch (error) {
      console.error('Language update error:', error);
    }
  };

  return (
    <View style={styles.container}>
      <Header
        title={t('profile.title')}
        onBack={() => navigation.goBack()}
      />
      
      <ScrollView style={styles.content}>
        <Card>
          <Text style={styles.sectionTitle}>{t('profile.personal_info')}</Text>
          
          <Input
            label={t('auth.name')}
            value={formData.name}
            onChangeText={(text) => setFormData({ ...formData, name: text })}
            editable={editing && !loading}
          />

          <Input
            label={t('auth.email')}
            value={user?.email || ''}
            editable={false}
            containerStyle={styles.disabledInput}
          />

          <Input
            label={t('auth.phone')}
            value={formData.phone}
            onChangeText={(text) => setFormData({ ...formData, phone: text })}
            type="phone"
            editable={editing && !loading}
          />

          {editing ? (
            <View style={styles.actions}>
              <Button
                title={t('common.save')}
                onPress={handleSave}
                loading={loading}
                style={styles.actionButton}
              />
              <Button
                title={t('common.cancel')}
                onPress={() => {
                  setEditing(false);
                  setFormData({
                    name: user?.name || '',
                    phone: user?.phone || '',
                  });
                }}
                variant="secondary"
                disabled={loading}
                style={[styles.actionButton, styles.actionButtonSpacing]}
              />
            </View>
          ) : (
            <Button
              title={t('common.edit')}
              onPress={() => setEditing(true)}
              variant="secondary"
            />
          )}
        </Card>

        <Card>
          <Text style={styles.sectionTitle}>{t('profile.organization')}</Text>
          <View style={styles.infoRow}>
            <Text style={styles.label}>{t('profile.organization_name')}:</Text>
            <Text style={styles.value}>{organization?.name || '-'}</Text>
          </View>
          <View style={styles.infoRow}>
            <Text style={styles.label}>{t('profile.subscription')}:</Text>
            <Text style={styles.value}>
              {organization?.subscription_package?.toUpperCase() || '-'}
            </Text>
          </View>
        </Card>

        <Card>
          <Text style={styles.sectionTitle}>{t('profile.preferences')}</Text>
          <View style={styles.preferenceRow}>
            <Text style={styles.preferenceLabel}>{t('profile.language')}</Text>
            <View style={styles.preferenceValue}>
              <Text style={styles.preferenceText}>
                {i18n.language === 'en' ? 'English' : 'සිංහල'}
              </Text>
              <Switch
                value={i18n.language === 'si'}
                onValueChange={toggleLanguage}
              />
            </View>
          </View>
        </Card>

        <Button
          title={t('auth.logout')}
          onPress={handleLogout}
          variant="danger"
          style={styles.logoutButton}
        />
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  content: {
    flex: 1,
    padding: 16,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: '600',
    color: '#333',
    marginBottom: 16,
  },
  disabledInput: {
    opacity: 0.6,
  },
  actions: {
    flexDirection: 'row',
  },
  actionButton: {
    flex: 1,
  },
  actionButtonSpacing: {
    marginLeft: 12,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  label: {
    fontSize: 14,
    color: '#666',
  },
  value: {
    fontSize: 14,
    fontWeight: '600',
    color: '#333',
  },
  preferenceRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 8,
  },
  preferenceLabel: {
    fontSize: 16,
    color: '#333',
  },
  preferenceValue: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  preferenceText: {
    fontSize: 14,
    color: '#666',
    marginRight: 12,
  },
  logoutButton: {
    marginTop: 8,
    marginBottom: 32,
  },
});
