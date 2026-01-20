import React, { useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  ActivityIndicator,
  SafeAreaView,
  RefreshControl,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useFieldStore } from '../../stores/fieldStore';
import { Field } from '../../../domain/entities/Field';

export default function FieldsListScreen({ navigation }: any) {
  const { t } = useTranslation();
  const { fields, isLoading, error, fetchFields } = useFieldStore();

  useEffect(() => {
    loadFields();
  }, []);

  const loadFields = async () => {
    try {
      await fetchFields();
    } catch (error) {
      console.error('Error loading fields:', error);
    }
  };

  const navigateToFieldDetail = (field: Field) => {
    navigation.navigate('FieldDetail', { field });
  };

  const navigateToCreateField = () => {
    navigation.navigate('CreateField');
  };

  const renderFieldItem = ({ item }: { item: Field }) => (
    <TouchableOpacity
      style={styles.fieldItem}
      onPress={() => navigateToFieldDetail(item)}
    >
      <View style={styles.fieldHeader}>
        <Text style={styles.fieldName}>{item.name}</Text>
        <Text style={styles.fieldArea}>
          {item.area ? `${(item.area / 10000).toFixed(2)} ha` : 'N/A'}
        </Text>
      </View>
      {item.location && (
        <Text style={styles.fieldLocation}>{item.location}</Text>
      )}
      {item.crop_type && (
        <Text style={styles.fieldCrop}>🌾 {item.crop_type}</Text>
      )}
      <Text style={styles.fieldType}>
        📏 {item.measurement_type?.replace('_', ' ')}
      </Text>
    </TouchableOpacity>
  );

  if (isLoading && fields.length === 0) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color="#27ae60" />
        <Text style={styles.loadingText}>{t('common.loading')}</Text>
      </View>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>{t('fields.title')}</Text>
        <TouchableOpacity
          style={styles.addButton}
          onPress={navigateToCreateField}
        >
          <Text style={styles.addButtonText}>+ Add Field</Text>
        </TouchableOpacity>
      </View>

      {error && (
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>{error}</Text>
        </View>
      )}

      <FlatList
        data={fields}
        renderItem={renderFieldItem}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl
            refreshing={isLoading}
            onRefresh={loadFields}
            colors={['#27ae60']}
          />
        }
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyIcon}>🗺️</Text>
            <Text style={styles.emptyText}>No fields yet</Text>
            <Text style={styles.emptySubtext}>
              Start by adding your first field
            </Text>
          </View>
        }
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
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
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  addButton: {
    backgroundColor: '#27ae60',
    paddingHorizontal: 15,
    paddingVertical: 8,
    borderRadius: 8,
  },
  addButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 14,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#7f8c8d',
  },
  errorContainer: {
    backgroundColor: '#fee',
    padding: 10,
    margin: 15,
    borderRadius: 8,
  },
  errorText: {
    color: '#e74c3c',
    textAlign: 'center',
  },
  listContent: {
    padding: 15,
  },
  fieldItem: {
    backgroundColor: '#fff',
    padding: 15,
    borderRadius: 12,
    marginBottom: 15,
    borderLeftWidth: 4,
    borderLeftColor: '#27ae60',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  fieldHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  fieldName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    flex: 1,
  },
  fieldArea: {
    fontSize: 16,
    fontWeight: '600',
    color: '#27ae60',
  },
  fieldLocation: {
    fontSize: 14,
    color: '#7f8c8d',
    marginBottom: 5,
  },
  fieldCrop: {
    fontSize: 14,
    color: '#34495e',
    marginBottom: 3,
  },
  fieldType: {
    fontSize: 12,
    color: '#95a5a6',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyIcon: {
    fontSize: 60,
    marginBottom: 15,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#7f8c8d',
    marginBottom: 5,
  },
  emptySubtext: {
    fontSize: 14,
    color: '#95a5a6',
  },
});
