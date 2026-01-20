import React from 'react';
import { StyleSheet, View } from 'react-native';
import MapView, { Polygon, PROVIDER_GOOGLE } from 'react-native-maps';
import { GpsCoordinate } from '../types';
import { COLORS, MAP_CONSTANTS } from '../constants';
import { calculateMapRegion } from '../utils';

interface MapPreviewProps {
  coordinates: GpsCoordinate[];
  height?: number;
}

export default function MapPreview({ coordinates, height = 150 }: MapPreviewProps) {
  // Use utility function to calculate region
  const region = calculateMapRegion(coordinates);

  return (
    <View style={[styles.container, { height }]}>
      <MapView
        style={styles.map}
        provider={PROVIDER_GOOGLE}
        mapType="hybrid"
        region={region}
        scrollEnabled={false}
        zoomEnabled={false}
        rotateEnabled={false}
        pitchEnabled={false}
        toolbarEnabled={false}
        cacheEnabled={true}
      >
        {coordinates.length >= MAP_CONSTANTS.MIN_POLYGON_POINTS && (
          <Polygon
            coordinates={coordinates.map(coord => ({
              latitude: coord.latitude,
              longitude: coord.longitude,
            }))}
            fillColor={COLORS.polygonFill}
            strokeColor={COLORS.polygonStroke}
            strokeWidth={2}
          />
        )}
      </MapView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    width: '100%',
    overflow: 'hidden',
    borderRadius: 8,
  },
  map: {
    width: '100%',
    height: '100%',
  },
});
