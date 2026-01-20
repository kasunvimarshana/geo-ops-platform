import React from 'react';
import { ActivityIndicator, View, StyleSheet, ViewStyle } from 'react-native';

interface LoadingSpinnerProps {
  size?: 'small' | 'large';
  color?: string;
  style?: ViewStyle;
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
});

export function LoadingSpinner({
  size = 'large',
  color = '#2ecc71',
  style,
}: LoadingSpinnerProps) {
  return (
    <View style={[styles.container, style]}>
      <ActivityIndicator size={size} color={color} />
    </View>
  );
}
