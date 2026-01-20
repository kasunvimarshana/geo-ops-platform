import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ActivityIndicator } from 'react-native';
import { useRoute } from '@react-navigation/native';
import { getLandById } from '../../src/api/lands';
import { Land } from '../../src/types/land';

const LandDetailScreen = () => {
    const route = useRoute();
    const { id } = route.params;
    const [land, setLand] = useState<Land | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchLandDetails = async () => {
            try {
                const response = await getLandById(id);
                setLand(response.data);
            } catch (error) {
                console.error('Error fetching land details:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchLandDetails();
    }, [id]);

    if (loading) {
        return (
            <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color="#0000ff" />
            </View>
        );
    }

    if (!land) {
        return (
            <View style={styles.container}>
                <Text>No land details found.</Text>
            </View>
        );
    }

    return (
        <View style={styles.container}>
            <Text style={styles.title}>{land.name}</Text>
            <Text>Area: {land.area} acres</Text>
            <Text>Location: {land.location}</Text>
            <Text>Measurement History: {land.measurementHistory.join(', ')}</Text>
            {/* Add more land details as needed */}
        </View>
    );
};

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 20,
        backgroundColor: '#fff',
    },
    loadingContainer: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
    },
    title: {
        fontSize: 24,
        fontWeight: 'bold',
        marginBottom: 10,
    },
});

export default LandDetailScreen;