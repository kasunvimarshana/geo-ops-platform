import React, { useState, useEffect } from 'react';
import {
  View,
  StyleSheet,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { Header, MapView } from '@/components';
import { Land, Job } from '@/types';
import { apiClient } from '@/services/api/client';
import { MAP_CONFIG } from '@/constants';

interface MapScreenProps {
  navigation: any;
}

export const MapScreen: React.FC<MapScreenProps> = ({ navigation }) => {
  const { t } = useTranslation();
  const [lands, setLands] = useState<Land[]>([]);
  const [jobs, setJobs] = useState<Job[]>([]);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [landsResponse, jobsResponse] = await Promise.all([
        apiClient.get('/lands'),
        apiClient.get('/jobs'),
      ]);

      if (landsResponse.success) {
        setLands(landsResponse.data || []);
      }
      if (jobsResponse.success) {
        setJobs(jobsResponse.data || []);
      }
    } catch (error) {
      console.error('Error fetching map data:', error);
    }
  };

  const landMarkers = lands.map((land) => {
    const center = land.polygon[0];
    return {
      coordinate: {
        latitude: center.latitude,
        longitude: center.longitude,
      },
      title: land.name,
      description: `${land.area_acres.toFixed(2)} acres`,
      color: MAP_CONFIG.MARKER_COLORS.land,
      onPress: () => navigation.navigate('MeasurementDetail', { landId: land.id }),
    };
  });

  const jobMarkers = jobs.map((job) => ({
    coordinate: {
      latitude: job.location.latitude,
      longitude: job.location.longitude,
    },
    title: job.title,
    description: job.customer_name,
    color: MAP_CONFIG.MARKER_COLORS[`job_${job.status}` as keyof typeof MAP_CONFIG.MARKER_COLORS],
    onPress: () => navigation.navigate('JobDetail', { jobId: job.id }),
  }));

  const landPolygons = lands.map((land) => ({
    coordinates: land.polygon,
    fillColor: 'rgba(76, 175, 80, 0.3)',
    strokeColor: MAP_CONFIG.MARKER_COLORS.land,
  }));

  return (
    <View style={styles.container}>
      <Header
        title={t('maps.title')}
        showSync
      />

      <MapView
        style={styles.map}
        markers={[...landMarkers, ...jobMarkers]}
        polygons={landPolygons}
      />
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
