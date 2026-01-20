/**
 * Print Button Component
 * Reusable button for printing documents with icon and loading state
 */

import React from 'react';
import { TouchableOpacity, Text, StyleSheet, ActivityIndicator, View } from 'react-native';
import { usePrinterStore } from '../../../store/printerStore';

interface PrintButtonProps {
  onPress: () => void;
  label?: string;
  variant?: 'primary' | 'secondary' | 'outline';
  disabled?: boolean;
  loading?: boolean;
  icon?: string;
  style?: any;
}

export const PrintButton: React.FC<PrintButtonProps> = ({
  onPress,
  label = 'Print',
  variant = 'primary',
  disabled = false,
  loading = false,
  icon,
  style,
}) => {
  const { isPrinting, isConnected } = usePrinterStore();

  const isDisabled = disabled || loading || isPrinting;
  const showLoading = loading || isPrinting;

  const getButtonStyle = () => {
    const baseStyle = [styles.button];

    if (variant === 'primary') {
      baseStyle.push(styles.buttonPrimary);
    } else if (variant === 'secondary') {
      baseStyle.push(styles.buttonSecondary);
    } else if (variant === 'outline') {
      baseStyle.push(styles.buttonOutline);
    }

    if (isDisabled) {
      baseStyle.push(styles.buttonDisabled);
    }

    if (style) {
      baseStyle.push(style);
    }

    return baseStyle;
  };

  const getTextStyle = () => {
    const baseStyle = [styles.buttonText];

    if (variant === 'primary') {
      baseStyle.push(styles.textPrimary);
    } else if (variant === 'secondary') {
      baseStyle.push(styles.textSecondary);
    } else if (variant === 'outline') {
      baseStyle.push(styles.textOutline);
    }

    if (isDisabled) {
      baseStyle.push(styles.textDisabled);
    }

    return baseStyle;
  };

  return (
    <TouchableOpacity
      style={getButtonStyle()}
      onPress={onPress}
      disabled={isDisabled}
      activeOpacity={0.7}
    >
      <View style={styles.content}>
        {showLoading ? (
          <ActivityIndicator
            size="small"
            color={variant === 'primary' ? '#fff' : '#2196f3'}
            style={styles.loader}
          />
        ) : null}
        <Text style={getTextStyle()}>
          {showLoading ? 'Printing...' : label}
        </Text>
        {!isConnected && !showLoading && (
          <Text style={styles.badge}>PDF</Text>
        )}
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  button: {
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 8,
    minHeight: 44,
    justifyContent: 'center',
    alignItems: 'center',
  },
  buttonPrimary: {
    backgroundColor: '#2196f3',
  },
  buttonSecondary: {
    backgroundColor: '#4caf50',
  },
  buttonOutline: {
    backgroundColor: 'transparent',
    borderWidth: 1,
    borderColor: '#2196f3',
  },
  buttonDisabled: {
    opacity: 0.5,
  },
  content: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  loader: {
    marginRight: 4,
  },
  buttonText: {
    fontSize: 16,
    fontWeight: '600',
  },
  textPrimary: {
    color: '#fff',
  },
  textSecondary: {
    color: '#fff',
  },
  textOutline: {
    color: '#2196f3',
  },
  textDisabled: {
    color: '#999',
  },
  badge: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#fff',
    backgroundColor: '#ff9800',
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
    marginLeft: 4,
  },
});
