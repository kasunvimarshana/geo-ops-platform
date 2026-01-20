import React from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  StatusBar,
  Platform,
} from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

interface HeaderProps {
  title: string;
  onBack?: () => void;
  rightAction?: {
    label: string;
    onPress: () => void;
  };
  showSync?: boolean;
  isSyncing?: boolean;
}

export const Header: React.FC<HeaderProps> = ({
  title,
  onBack,
  rightAction,
  showSync = false,
  isSyncing = false,
}) => {
  const insets = useSafeAreaInsets();

  return (
    <View
      style={[
        styles.container,
        { paddingTop: insets.top + 8 },
      ]}
    >
      <StatusBar barStyle="light-content" />
      <View style={styles.content}>
        {onBack ? (
          <TouchableOpacity
            style={styles.backButton}
            onPress={onBack}
          >
            <Text style={styles.backText}>←</Text>
          </TouchableOpacity>
        ) : (
          <View style={styles.backButton} />
        )}
        
        <Text style={styles.title} numberOfLines={1}>
          {title}
        </Text>

        {rightAction ? (
          <TouchableOpacity
            style={styles.rightButton}
            onPress={rightAction.onPress}
          >
            <Text style={styles.rightText}>
              {rightAction.label}
            </Text>
          </TouchableOpacity>
        ) : showSync ? (
          <View style={styles.syncIndicator}>
            {isSyncing && (
              <View style={styles.syncDot} />
            )}
          </View>
        ) : (
          <View style={styles.rightButton} />
        )}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#2196F3',
    ...Platform.select({
      ios: {
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.1,
        shadowRadius: 4,
      },
      android: {
        elevation: 4,
      },
    }),
  },
  content: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    minHeight: 56,
  },
  backButton: {
    width: 40,
    height: 40,
    alignItems: 'center',
    justifyContent: 'center',
  },
  backText: {
    fontSize: 28,
    color: '#FFFFFF',
    fontWeight: '300',
  },
  title: {
    flex: 1,
    fontSize: 20,
    fontWeight: '600',
    color: '#FFFFFF',
    textAlign: 'center',
  },
  rightButton: {
    width: 40,
    height: 40,
    alignItems: 'center',
    justifyContent: 'center',
  },
  rightText: {
    fontSize: 14,
    color: '#FFFFFF',
    fontWeight: '600',
  },
  syncIndicator: {
    width: 40,
    height: 40,
    alignItems: 'center',
    justifyContent: 'center',
  },
  syncDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#4CAF50',
  },
});
