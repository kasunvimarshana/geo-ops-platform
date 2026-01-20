import React from 'react';
import { View, Text, StyleSheet, ViewStyle } from 'react-native';

interface ErrorMessageProps {
  message: string;
  style?: ViewStyle;
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#ffe0e0',
    padding: 12,
    borderRadius: 8,
    borderLeftWidth: 4,
    borderLeftColor: '#e74c3c',
    marginVertical: 8,
  },
  text: {
    color: '#c0392b',
    fontSize: 14,
    fontWeight: '500',
  },
});

export function ErrorMessage({ message, style }: ErrorMessageProps) {
  return (
    <View style={[styles.container, style]}>
      <Text style={styles.text}>{message}</Text>
    </View>
  );
}
