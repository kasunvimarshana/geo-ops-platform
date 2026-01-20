import React, { useState, useEffect } from 'react';
import { View, StyleSheet, Text, TouchableOpacity } from 'react-native';
import MapView, { Marker, Polygon, Polyline, PROVIDER_GOOGLE } from 'react-native-maps';
import { Coordinate } from '../utils/areaCalculator';

interface MeasurementMapProps {
  coordinates: Coordinate[];
  onAddCoordinate?: (coordinate: Coordinate) => void;
  currentLocation?: Coordinate | null;
  editable?: boolean;
  showMarkers?: boolean;
  polygonColor?: string;
  lineColor?: string;
}

export const MeasurementMap: React.FC<MeasurementMapProps> = ({
  coordinates,
  onAddCoordinate,
  currentLocation,
  editable = false,
  showMarkers = true,
  polygonColor = 'rgba(0, 122, 255, 0.3)',
  lineColor = '#007AFF',
}) => {
  const [region, setRegion] = useState<any>(null);

  useEffect(() => {
    if (currentLocation) {
      setRegion({
        latitude: currentLocation.latitude,
        longitude: currentLocation.longitude,
        latitudeDelta: 0.005,
        longitudeDelta: 0.005,
      });
    } else if (coordinates.length > 0) {
      const center = coordinates[0];
      setRegion({
        latitude: center.latitude,
        longitude: center.longitude,
        latitudeDelta: 0.01,
        longitudeDelta: 0.01,
      });
    }
  }, [currentLocation, coordinates]);

  const handleMapPress = (e: any) => {
    if (editable && onAddCoordinate) {
      const { latitude, longitude } = e.nativeEvent.coordinate;
      onAddCoordinate({ latitude, longitude });
    }
  };

  const mapPoints = coordinates.map((coord) => ({
    latitude: coord.latitude,
    longitude: coord.longitude,
  }));

  return (
    <View style={styles.container}>
      {region && (
        <MapView
          provider={PROVIDER_GOOGLE}
          style={styles.map}
          region={region}
          onRegionChangeComplete={setRegion}
          onPress={handleMapPress}
          showsUserLocation
          showsMyLocationButton
          showsCompass
        >
          {/* Draw polygon if we have at least 3 points */}
          {coordinates.length >= 3 && (
            <Polygon
              coordinates={mapPoints}
              fillColor={polygonColor}
              strokeColor={lineColor}
              strokeWidth={2}
            />
          )}

          {/* Draw polyline for less than 3 points */}
          {coordinates.length > 0 && coordinates.length < 3 && (
            <Polyline
              coordinates={mapPoints}
              strokeColor={lineColor}
              strokeWidth={2}
            />
          )}

          {/* Show markers for each coordinate */}
          {showMarkers &&
            coordinates.map((coord, index) => (
              <Marker
                key={index}
                coordinate={{
                  latitude: coord.latitude,
                  longitude: coord.longitude,
                }}
                title={`Point ${index + 1}`}
                pinColor={index === 0 ? 'green' : index === coordinates.length - 1 ? 'red' : 'blue'}
              />
            ))}

          {/* Show current location marker */}
          {currentLocation && (
            <Marker
              coordinate={{
                latitude: currentLocation.latitude,
                longitude: currentLocation.longitude,
              }}
              title="Current Location"
              pinColor="orange"
            />
          )}
        </MapView>
      )}

      {!region && (
        <View style={styles.loadingContainer}>
          <Text>Loading map...</Text>
        </View>
      )}
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
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f5f5f5',
  },
});
