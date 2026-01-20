import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Land } from '../../types/land';

interface LandCardProps {
  land: Land;
}

const LandCard: React.FC<LandCardProps> = ({ land }) => {
  return (
    <View style={styles.card}>
      <Text style={styles.title}>{land.name}</Text>
      <Text style={styles.details}>Area: {land.area} acres</Text>
      <Text style={styles.details}>Location: {land.location}</Text>
      <Text style={styles.details}>Last Measured: {land.lastMeasured}</Text>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
    marginVertical: 8,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  details: {
    fontSize: 14,
    color: '#555',
  },
});

export default LandCard;