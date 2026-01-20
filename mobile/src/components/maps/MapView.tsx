import React, { useEffect, useRef } from 'react';
import { StyleSheet, View } from 'react-native';
import MapView, { Marker, Polygon } from 'react-native-maps';
import { useGPS } from '../../hooks/useGPS';
import { Land } from '../../types/land';

interface MapViewProps {
  landData: Land;
}

const CustomMapView: React.FC<MapViewProps> = ({ landData }) => {
  const mapRef = useRef<MapView | null>(null);
  const { currentLocation } = useGPS();

  useEffect(() => {
    if (mapRef.current && currentLocation) {
      mapRef.current.animateToRegion({
        ...currentLocation,
        latitudeDelta: 0.01,
        longitudeDelta: 0.01,
      });
    }
  }, [currentLocation]);

  return (
    <View style={styles.container}>
      <MapView
        ref={mapRef}
        style={styles.map}
        initialRegion={{
          latitude: landData.latitude,
          longitude: landData.longitude,
          latitudeDelta: 0.01,
          longitudeDelta: 0.01,
        }}
      >
        <Marker coordinate={{ latitude: landData.latitude, longitude: landData.longitude }} />
        <Polygon
          coordinates={landData.coordinates}
          strokeColor="#000"
          fillColor="rgba(255, 0, 0, 0.5)"
          strokeWidth={2}
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

export default CustomMapView;