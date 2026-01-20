import React, { useRef, useEffect } from 'react';
import { StyleSheet, View } from 'react-native';
import MapView, { Marker, Polygon, Polyline, PROVIDER_GOOGLE } from 'react-native-maps';
import { GpsCoordinate } from '../types';
import { COLORS, DEFAULT_MAP_REGION, MAP_CONSTANTS } from '../constants';

interface MeasurementMapProps {
  coordinates: GpsCoordinate[];
  isRecording?: boolean;
  showPolygon?: boolean;
  mapType?: 'standard' | 'satellite' | 'hybrid';
  onMapReady?: () => void;
}

export default function MeasurementMap({
  coordinates,
  isRecording = false,
  showPolygon = true,
  mapType = 'hybrid',
  onMapReady,
}: MeasurementMapProps) {
  const mapRef = useRef<MapView>(null);

  // Auto-fit map to show all coordinates
  useEffect(() => {
    if (coordinates.length > 0 && mapRef.current) {
      // Use requestAnimationFrame to ensure map is rendered before fitting
      // requestAnimationFrame waits for the next paint cycle, guaranteeing the map
      // DOM is ready. This is more reliable than setTimeout and avoids race conditions
      // where fitToCoordinates might be called before the map view is fully rendered.
      requestAnimationFrame(() => {
        try {
          mapRef.current?.fitToCoordinates(coordinates, {
            edgePadding: { 
              top: MAP_CONSTANTS.MAP_EDGE_PADDING, 
              right: MAP_CONSTANTS.MAP_EDGE_PADDING, 
              bottom: MAP_CONSTANTS.MAP_EDGE_PADDING, 
              left: MAP_CONSTANTS.MAP_EDGE_PADDING 
            },
            animated: true,
          });
        } catch (error) {
          console.warn('Failed to fit map to coordinates:', error);
        }
      });
    }
  }, [coordinates]);

  // Get initial region based on first coordinate or default
  const getInitialRegion = () => {
    if (coordinates.length > 0) {
      return {
        latitude: coordinates[0].latitude,
        longitude: coordinates[0].longitude,
        latitudeDelta: MAP_CONSTANTS.INITIAL_LATITUDE_DELTA,
        longitudeDelta: MAP_CONSTANTS.INITIAL_LONGITUDE_DELTA,
      };
    }
    // Default to configured region (Sri Lanka by default, can be changed in constants)
    return DEFAULT_MAP_REGION;
  };

  return (
    <View style={styles.container}>
      <MapView
        ref={mapRef}
        style={styles.map}
        provider={PROVIDER_GOOGLE}
        mapType={mapType}
        initialRegion={getInitialRegion()}
        showsUserLocation={isRecording}
        showsMyLocationButton={isRecording}
        showsCompass={true}
        showsScale={true}
        onMapReady={onMapReady}
      >
        {/* Show markers for each GPS point */}
        {coordinates.map((coord, index) => (
          <Marker
            key={`marker-${index}`}
            coordinate={{
              latitude: coord.latitude,
              longitude: coord.longitude,
            }}
            title={`Point ${index + 1}`}
            description={`Lat: ${coord.latitude.toFixed(6)}, Lng: ${coord.longitude.toFixed(6)}`}
            pinColor={index === 0 ? COLORS.success : COLORS.primary}
          />
        ))}

        {/* Show line connecting points while recording */}
        {coordinates.length > 1 && (
          <Polyline
            coordinates={coordinates.map(coord => ({
              latitude: coord.latitude,
              longitude: coord.longitude,
            }))}
            strokeColor={COLORS.primary}
            strokeWidth={3}
          />
        )}

        {/* Show filled polygon when measurement is complete */}
        {showPolygon && coordinates.length >= MAP_CONSTANTS.MIN_POLYGON_POINTS && (
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
    flex: 1,
  },
  map: {
    width: '100%',
    height: '100%',
  },
});
