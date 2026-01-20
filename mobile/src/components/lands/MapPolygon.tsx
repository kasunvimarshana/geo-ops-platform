import React from 'react';
import { View, StyleSheet } from 'react-native';
import MapView, { Polygon } from 'react-native-maps';

interface MapPolygonProps {
  coordinates: { latitude: number; longitude: number }[];
  fillColor?: string;
  strokeColor?: string;
  strokeWidth?: number;
}

const MapPolygon: React.FC<MapPolygonProps> = ({
  coordinates,
  fillColor = 'rgba(255, 0, 0, 0.5)',
  strokeColor = 'rgba(255, 0, 0, 1)',
  strokeWidth = 2,
}) => {
  return (
    <View style={styles.container}>
      <MapView style={styles.map}>
        <Polygon
          coordinates={coordinates}
          fillColor={fillColor}
          strokeColor={strokeColor}
          strokeWidth={strokeWidth}
        />
      </MapView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  map: {
    flex: 1,
  },
});

export default MapPolygon;