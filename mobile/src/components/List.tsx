import React from 'react';
import {
  FlatList,
  FlatListProps,
  View,
  Text,
  StyleSheet,
  RefreshControl,
} from 'react-native';
import { Loading } from './Loading';

interface ListProps<T> extends Partial<FlatListProps<T>> {
  data: T[];
  renderItem: (item: T, index: number) => React.ReactElement;
  keyExtractor?: (item: T, index: number) => string;
  loading?: boolean;
  refreshing?: boolean;
  onRefresh?: () => void;
  emptyMessage?: string;
  emptyComponent?: React.ReactElement;
}

export function List<T>({
  data,
  renderItem,
  keyExtractor,
  loading = false,
  refreshing = false,
  onRefresh,
  emptyMessage = 'No items found',
  emptyComponent,
  ...props
}: ListProps<T>) {
  if (loading && data.length === 0) {
    return <Loading />;
  }

  const renderEmpty = () => {
    if (emptyComponent) {
      return emptyComponent;
    }

    return (
      <View style={styles.emptyContainer}>
        <Text style={styles.emptyText}>{emptyMessage}</Text>
      </View>
    );
  };

  return (
    <FlatList
      data={data}
      renderItem={({ item, index }) => renderItem(item, index)}
      keyExtractor={
        keyExtractor || ((item, index) => index.toString())
      }
      contentContainerStyle={[
        styles.container,
        data.length === 0 && styles.emptyList,
      ]}
      ListEmptyComponent={renderEmpty}
      refreshControl={
        onRefresh ? (
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            tintColor="#2196F3"
          />
        ) : undefined
      }
      {...props}
    />
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 16,
  },
  emptyList: {
    flexGrow: 1,
    justifyContent: 'center',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 48,
  },
  emptyText: {
    fontSize: 16,
    color: '#999',
    textAlign: 'center',
  },
});
