import React, { useRef } from 'react';
import { StyleSheet, ViewStyle } from 'react-native';
import RNMapView, {
  Marker,
  Polygon,
  Polyline,
  PROVIDER_GOOGLE,
  MapViewProps as RNMapViewProps,
} from 'react-native-maps';
import { GPSPoint } from '@/types';
import { DEFAULT_MAP_COORDINATES } from '../constants';

interface MapViewProps extends Partial<RNMapViewProps> {
  style?: ViewStyle;
  initialRegion?: {
    latitude: number;
    longitude: number;
    latitudeDelta?: number;
    longitudeDelta?: number;
  };
  markers?: Array<{
    coordinate: {
      latitude: number;
      longitude: number;
    };
    title?: string;
    description?: string;
    color?: string;
    onPress?: () => void;
  }>;
  polygons?: Array<{
    coordinates: GPSPoint[];
    fillColor?: string;
    strokeColor?: string;
  }>;
  polylines?: Array<{
    coordinates: GPSPoint[];
    strokeColor?: string;
    strokeWidth?: number;
  }>;
  showUserLocation?: boolean;
}

export const MapView: React.FC<MapViewProps> = ({
  style,
  initialRegion,
  markers = [],
  polygons = [],
  polylines = [],
  showUserLocation = true,
  ...props
}) => {
  const mapRef = useRef<RNMapView>(null);

  const defaultRegion = {
    latitude: initialRegion?.latitude || DEFAULT_MAP_COORDINATES.latitude,
    longitude: initialRegion?.longitude || DEFAULT_MAP_COORDINATES.longitude,
    latitudeDelta: initialRegion?.latitudeDelta || DEFAULT_MAP_COORDINATES.latitudeDelta,
    longitudeDelta: initialRegion?.longitudeDelta || DEFAULT_MAP_COORDINATES.longitudeDelta,
  };

  return (
    <RNMapView
      ref={mapRef}
      provider={PROVIDER_GOOGLE}
      style={[styles.map, style]}
      initialRegion={defaultRegion}
      showsUserLocation={showUserLocation}
      showsMyLocationButton
      showsCompass
      showsScale
      {...props}
    >
      {markers.map((marker, index) => (
        <Marker
          key={`marker-${index}`}
          coordinate={marker.coordinate}
          title={marker.title}
          description={marker.description}
          pinColor={marker.color}
          onPress={marker.onPress}
        />
      ))}

      {polygons.map((polygon, index) => (
        <Polygon
          key={`polygon-${index}`}
          coordinates={polygon.coordinates.map(p => ({
            latitude: p.latitude,
            longitude: p.longitude,
          }))}
          fillColor={polygon.fillColor || 'rgba(33, 150, 243, 0.3)'}
          strokeColor={polygon.strokeColor || '#2196F3'}
          strokeWidth={2}
        />
      ))}

      {polylines.map((polyline, index) => (
        <Polyline
          key={`polyline-${index}`}
          coordinates={polyline.coordinates.map(p => ({
            latitude: p.latitude,
            longitude: p.longitude,
          }))}
          strokeColor={polyline.strokeColor || '#2196F3'}
          strokeWidth={polyline.strokeWidth || 3}
        />
      ))}
    </RNMapView>
  );
};

const styles = StyleSheet.create({
  map: {
    flex: 1,
  },
});
