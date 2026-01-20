import React, { useEffect, useState } from 'react';
import { View, Text, Button, StyleSheet } from 'react-native';
import MapView, { Polygon } from 'react-native-maps';
import { useGPS } from '../../hooks/useGPS';
import { LandMeasurementService } from '../../services/LandMeasurementService';
import { useStore } from '../../store/landStore';

const MeasureLandScreen = () => {
    const [coordinates, setCoordinates] = useState([]);
    const [isMeasuring, setIsMeasuring] = useState(false);
    const { getCurrentLocation } = useGPS();
    const { addLandMeasurement } = useStore();

    const startMeasurement = async () => {
        setIsMeasuring(true);
        const location = await getCurrentLocation();
        setCoordinates([...coordinates, location]);
    };

    const stopMeasurement = () => {
        setIsMeasuring(false);
        if (coordinates.length > 2) {
            const area = LandMeasurementService.calculateArea(coordinates);
            addLandMeasurement({ coordinates, area });
            alert(`Area measured: ${area} acres`);
        } else {
            alert('At least 3 points are required to measure an area.');
        }
    };

    useEffect(() => {
        if (isMeasuring) {
            const interval = setInterval(async () => {
                const location = await getCurrentLocation();
                setCoordinates(prev => [...prev, location]);
            }, 5000); // Update location every 5 seconds

            return () => clearInterval(interval);
        }
    }, [isMeasuring]);

    return (
        <View style={styles.container}>
            <Text style={styles.title}>Measure Land</Text>
            <MapView style={styles.map}>
                {coordinates.length > 0 && (
                    <Polygon
                        coordinates={coordinates}
                        strokeColor="rgba(255, 0, 0, 0.5)"
                        fillColor="rgba(255, 0, 0, 0.3)"
                        strokeWidth={2}
                    />
                )}
            </MapView>
            <Button
                title={isMeasuring ? "Stop Measurement" : "Start Measurement"}
                onPress={isMeasuring ? stopMeasurement : startMeasurement}
            />
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    title: {
        fontSize: 24,
        marginBottom: 20,
    },
    map: {
        width: '100%',
        height: '70%',
    },
});

export default MeasureLandScreen;